<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Carrito;
use App\Entity\Producto;

final class UserController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        //try {
            if (!$this->getUser()) {
                $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
                return $this->redirectToRoute('app_login');
            }

            // Obtener el usuario desde el repositorio
            $user = $entityManager->getRepository(User::class)->find($this->getUser());

            //$user = $this->getUser();

            if (!$user) {
                $this->addFlash('danger', 'Error: Usuario no encontrado.');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('user/index.html.twig');
        //} catch (\Exception $e) {
        //    $this->addFlash('danger', 'Ocurrió un error inesperado. Inténtalo nuevamente más tarde.');
            //return $this->redirectToRoute('profile');
        //}
    }

    #[Route('/profile/data', name: 'profile_data')]
    public function data(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        //$user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Error: Usuario no encontrado.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserFormType::class, $user);
        $form->get('telefono')->setData($user->getTelefono() ?? '');
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($form->get('roles')->getData()) {
                    $user->setRoles([$form->get('roles')->getData()]);
                }

                // ✅ Solo actualizar la contraseña si el usuario ingresó una nueva
                $newPassword = $form->get('password')->getData();
                if ($newPassword) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                    $user->setPassword($hashedPassword);
                }

                $codigoPais = $form->get('codigo_pais')->getData();
                $telefono = $form->get('telefono')->getData();

                // 🔹 Unir el código y el número antes de guardar
                if ($codigoPais && $telefono) {
                    $user->setTelefono($codigoPais . ' ' . $telefono);
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', '¡Tu perfil se ha actualizado correctamente!');
                return $this->redirectToRoute('profile_data');
            } else {
                $this->addFlash('danger', 'Hubo un error al actualizar tu perfil. Verifica los datos e intenta nuevamente.');
            }
        }

        return $this->render('user/data.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/addresses', name: 'profile_addresses')]
    public function addresses(EntityManagerInterface $entityManager)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        return $this->render('user/addresses.html.twig', [
            'addresses' => $user->getDireccions(),
        ]);
    }

    #[Route('/profile/payments', name: 'profile_payments')]
    public function payments(EntityManagerInterface $entityManager)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        return $this->render('user/payments.html.twig', [
            'payments' => $user->getPagos(),
        ]);
    }

    #[Route('/profile/orders', name: 'profile_orders')]
    public function profileOrders(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $compras = $user->getCarritos()->filter(fn($carrito) => $carrito->isComprado());

        return $this->render('user/orders.html.twig', [
            'compras' => $compras,
        ]);
    }

    #[Route('/profile/cart', name: 'cart')]
    public function cart(EntityManagerInterface $entityManager)
    {
        // ✅ Verificar que el usuario ha iniciado sesión
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu carrito.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        // ✅ Obtener los productos del carrito directamente desde el usuario
        $cartItems = $user->getCarritos()->filter(fn($carrito) => !$carrito->isComprado());

        $totalCarrito = 0;
        foreach ($cartItems as $item) {
            $totalCarrito += $item->getPrecioTotal();
        }

        return $this->render('user/cart.html.twig', [
            'cartItems' => $cartItems,
            'totalCarrito' => $totalCarrito,
        ]);
    }

    #[Route('/profile/cart/add/{id}', name: 'add_cart', methods: ['POST'])]
    public function addToCart(EntityManagerInterface $entityManager, Request $request, int $id)
    {
        // ✅ Verificar que el usuario ha iniciado sesión
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para añadir productos al carrito.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        $producto = $entityManager->getRepository(Producto::class)->find($id);
        if (!$producto || $producto->getCantidad() < 1) {
            return $this->json(['success' => false, 'message' => 'Este producto no está disponible.'], 404);
        }

        $cantidad = $request->request->getInt('cantidad', 1);
        if ($cantidad < 1 || $cantidad > $producto->getCantidad()) {
            return $this->json(['success' => false, 'message' => 'Cantidad inválida.'], 400);
        }

        // ✅ Buscar en la colección `$carritos` en `User`
        $carritoItem = null;
        foreach ($user->getCarritos() as $item) {
            if ($item->getIdProducto()->getId() === $producto->getId()) {
                $carritoItem = $item;
                break;
            }
        }

        if ($carritoItem && !$carritoItem->isComprado()) {
            $nuevaCantidad = $carritoItem->getCantidad() + $cantidad;
            $stockDisponible = $producto->getCantidad() + $carritoItem->getCantidad(); // 🔹 Consideramos las unidades en el carrito

            if ($nuevaCantidad > $stockDisponible) {
                return $this->json(['success' => false, 'message' => 'No hay suficiente stock disponible.'], 400);
            }

            $carritoItem->setCantidad($nuevaCantidad);
            $carritoItem->setPrecioTotal($nuevaCantidad * $producto->getPrecio());
        } else {
            // 🔹 Si el producto no está en el carrito, crear un nuevo registro
            $carritoItem = new Carrito();
            $carritoItem->setIdUser($user);
            $carritoItem->setIdProducto($producto);
            $carritoItem->setCantidad($cantidad);
            $carritoItem->setPrecioUnidad($producto->getPrecio());
            $carritoItem->setPrecioTotal($cantidad * $producto->getPrecio());
            $carritoItem->setComprado(false);

            $entityManager->persist($carritoItem);
        }

        // ✅ Restar la cantidad seleccionada del stock
        $producto->setCantidad($producto->getCantidad() - $cantidad);
        $entityManager->flush();

        $this->addFlash('success', 'Producto añadido al carrito correctamente.');
        return $this->redirectToRoute('cart');
    }
}
