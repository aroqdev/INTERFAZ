<?php

namespace App\Controller;

use App\Entity\Carrito;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CarritoController extends AbstractController
{
    #[Route('/profile/cart/update/{id}', name: 'update_cart', methods: ['POST'])]
    public function updateCart(EntityManagerInterface $entityManager, Request $request, int $id)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para realizar esta operacion.');
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $carritoItem = $entityManager->getRepository(Carrito::class)->findOneBy(['idUser' => $user, 'id' => $id]);
        if (!$carritoItem) {
            return $this->redirectToRoute('cart');
        }

        $producto = $carritoItem->getIdProducto();
        $cantidadNueva = $request->request->getInt('cantidad', 1);

        // ✅ Validar stock disponible
        $stockDisponible = $producto->getCantidad() + $carritoItem->getCantidad();
        if ($cantidadNueva > $stockDisponible || $cantidadNueva < 1) {
            $this->addFlash('danger', 'Cantidad inválida.');
            return $this->redirectToRoute('cart');
        }

        // ✅ Actualizar cantidad y stock
        $diferencia = $cantidadNueva - $carritoItem->getCantidad();
        $producto->setCantidad($producto->getCantidad() - $diferencia);
        $carritoItem->setCantidad($cantidadNueva);
        $carritoItem->setPrecioTotal($cantidadNueva * $carritoItem->getPrecioUnidad());

        $entityManager->flush();

        $this->addFlash('success', 'Carrito actualizado correctamente.');
        return $this->redirectToRoute('cart');
    }

    #[Route('/profile/cart/decrease/{id}', name: 'decrease_cart', methods: ['POST'])]
    public function decreaseCart(EntityManagerInterface $entityManager, int $id)
    {
        $user = $this->getUser();
        $carritoItem = $entityManager->getRepository(Carrito::class)->findOneBy(['idUser' => $user, 'id' => $id]);
        if (!$carritoItem || $carritoItem->getCantidad() < 1) {
            return $this->redirectToRoute('cart');
        }

        $producto = $carritoItem->getIdProducto();
        $producto->setCantidad($producto->getCantidad() + 1);
        $carritoItem->setCantidad($carritoItem->getCantidad() - 1);
        $carritoItem->setPrecioTotal($carritoItem->getCantidad() * $carritoItem->getPrecioUnidad());

        $entityManager->flush();

        return $this->redirectToRoute('cart');
    }

    #[Route('/profile/cart/increase/{id}', name: 'increase_cart', methods: ['POST'])]
    public function increaseCart(EntityManagerInterface $entityManager, int $id)
    {
        $user = $this->getUser();
        $carritoItem = $entityManager->getRepository(Carrito::class)->findOneBy(['idUser' => $user, 'id' => $id]);
        $producto = $carritoItem->getIdProducto();

        if (!$carritoItem || $producto->getCantidad() < 1) {
            return $this->redirectToRoute('cart');
        }

        $producto->setCantidad($producto->getCantidad() - 1);
        $carritoItem->setCantidad($carritoItem->getCantidad() + 1);
        $carritoItem->setPrecioTotal($carritoItem->getCantidad() * $carritoItem->getPrecioUnidad());

        $entityManager->flush();

        return $this->redirectToRoute('cart');
    }

    #[Route('/profile/cart/remove/{id}', name: 'remove_cart', methods: ['POST'])]
    public function removeCart(EntityManagerInterface $entityManager, int $id)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para realizar esta operacion.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        $carritoItem = $entityManager->getRepository(Carrito::class)->findOneBy(['idUser' => $user, 'id' => $id]);
        if (!$carritoItem) {
            return $this->redirectToRoute('cart');
        }

        // ✅ Devolver stock al eliminar el producto
        $producto = $carritoItem->getIdProducto();
        $producto->setCantidad($producto->getCantidad() + $carritoItem->getCantidad());

        $entityManager->remove($carritoItem);
        $entityManager->flush();

        $this->addFlash('success', 'Producto eliminado del carrito.');
        return $this->redirectToRoute('cart');
    }

    #[Route('/profile/cart/checkout', name: 'cart_checkout')]
    public function cartCheckout(EntityManagerInterface $entityManager)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para continuar.');
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        // ✅ Obtener método de pago activo
        $pagoActivo = $user->getPagos()->filter(fn($pago) => $pago->isActivo())->first();
        if (!$pagoActivo) {
            $this->addFlash('danger', 'No tienes un método de pago activo.');
            return $this->redirectToRoute('profile_payments');
        }

        // ✅ Obtener dirección activa
        $direccionActiva = $user->getDireccions()->filter(fn($direccion) => $direccion->isActivo())->first();
        if (!$direccionActiva) {
            $this->addFlash('danger', 'No tienes una dirección activa.');
            return $this->redirectToRoute('profile_addresses');
        }

        // ✅ Obtener productos en el carrito no comprados
        $productosCarrito = $user->getCarritos()->filter(fn($carrito) => !$carrito->isComprado());

        if ($productosCarrito->isEmpty()) {
            $this->addFlash('danger', 'Tu carrito está vacío.');
            return $this->redirectToRoute('profile_cart');
        }

        return $this->render('user/cart_checkout.html.twig', [
            'productosCarrito' => $productosCarrito,
            'pagoActivo' => $pagoActivo,
            'direccionActiva' => $direccionActiva,
        ]);
    }

    #[Route('/profile/cart/buy', name: 'buy_cart')]
    public function buyCart(EntityManagerInterface $entityManager)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para realizar compras.');
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        // ✅ Obtener productos en el carrito que aún no han sido comprados
        $productosCarrito = $user->getCarritos()->filter(fn($carrito) => !$carrito->isComprado());

        if ($productosCarrito->isEmpty()) {
            $this->addFlash('danger', 'Tu carrito está vacío o ya compraste todo.');
            return $this->redirectToRoute('profile_cart');
        }

        // ✅ Marcar todos los productos como comprados
        foreach ($productosCarrito as $producto) {
            $producto->setComprado(true);
        }

        $entityManager->flush();

        return $this->redirectToRoute('cart_thankyou');
    }

    #[Route('/profile/cart/thankyou', name: 'cart_thankyou')]
    public function cartThankYou()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/cart_thankyou.html.twig');
    }
}
