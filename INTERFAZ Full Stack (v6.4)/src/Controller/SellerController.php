<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\PoductoSellerTypeForm;
use App\Form\EditPoductoSellerTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Carrito;
use App\Entity\Producto;

final class SellerController extends AbstractController
{
    #[Route('/seller/products', name: 'seller_product')]
    public function sellerProducts(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || !in_array('ROLE_SELLER', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Acceso denegado.');
            return $this->redirectToRoute('home');
        }

        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $productos = $user->getProductos(); // ✅ Obtener los productos del vendedor

        return $this->render('seller/products.html.twig', [
            'productos' => $productos,
        ]);
    }

    #[Route('/seller/product/add', name: 'add_product')]
    public function addProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || !in_array('ROLE_SELLER', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Acceso denegado.');
            return $this->redirectToRoute('home');
        }

        $producto = new Producto();
        $form = $this->createForm(PoductoSellerTypeForm::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Asignar el vendedor al producto
            $producto->setIdUser($this->getUser());

            // ✅ Si el checkbox "oferta" está marcado, establecer precio anterior
            if ($producto->isOferta() && !$producto->getPrecioAnterior()) {
                $producto->setPrecioAnterior($producto->getPrecio()); // Guardar precio original antes de oferta
            }

            $entityManager->persist($producto);
            $entityManager->flush();

            $this->addFlash('success', 'Producto agregado correctamente.');
            return $this->redirectToRoute('seller_product');
        }

        return $this->render('seller/add_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/seller/product/edit/{id}', name: 'edit_product')]
    public function editProduct(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        if (!$this->getUser() || !in_array('ROLE_SELLER', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Acceso denegado.');
            return $this->redirectToRoute('home');
        }

        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto || $producto->getIdUser() !== $this->getUser()) {
            $this->addFlash('danger', 'Producto no encontrado o no tienes permiso para modificarlo.');
            return $this->redirectToRoute('seller_product');
        }

        $form = $this->createForm(EditPoductoSellerTypeForm::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Si el producto está en oferta pero no tiene precio anterior, lo asignamos
            if ($producto->isOferta() && !$producto->getPrecioAnterior()) {
                $producto->setPrecioAnterior($producto->getPrecio());
            }

            $entityManager->flush();

            $this->addFlash('success', 'Producto actualizado correctamente.');
            return $this->redirectToRoute('seller_product');
        }

        return $this->render('seller/edit_product.html.twig', [
            'form' => $form->createView(),
            'producto' => $producto,
        ]);
    }

    #[Route('/seller/product/toggle/{id}', name: 'toggle_product_status')]
    public function toggleProductStatus(EntityManagerInterface $entityManager, int $id): Response
    {
        if (!$this->getUser() || !in_array('ROLE_SELLER', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Acceso denegado.');
            return $this->redirectToRoute('home');
        }

        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto || $producto->getIdUser() !== $this->getUser()) {
            $this->addFlash('danger', 'Producto no encontrado o no tienes permiso para modificarlo.');
            return $this->redirectToRoute('seller_products');
        }

        // ✅ Alternar estado activo/inactivo
        $producto->setActivo(!$producto->isActivo());

        $entityManager->flush();

        $this->addFlash('success', 'Estado del producto actualizado.');
        return $this->redirectToRoute('seller_product');
    }

    #[Route('/seller/product/delete/{id}', name: 'delete_product', methods: ['POST'])]
    public function deleteProduct(EntityManagerInterface $entityManager, int $id): Response
    {
        if (!$this->getUser() || !in_array('ROLE_SELLER', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Acceso denegado.');
            return $this->redirectToRoute('home');
        }

        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto || $producto->getIdUser() !== $this->getUser()) {
            $this->addFlash('danger', 'Producto no encontrado o no tienes permiso para eliminarlo.');
            return $this->redirectToRoute('seller_product');
        }

        // ✅ Eliminar el producto
        $entityManager->remove($producto);
        $entityManager->flush();

        $this->addFlash('success', 'Producto eliminado correctamente.');
        return $this->redirectToRoute('seller_product');
    }

    #[Route('/seller/sales', name: 'seller_sale')]
    public function sellerSales(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || !in_array('ROLE_SELLER', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Acceso denegado.');
            return $this->redirectToRoute('home');
        }

        $user = $entityManager->getRepository(User::class)->find($this->getUser());
            // ✅ Usar QueryBuilder para obtener las ventas del vendedor
        $ventas = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Carrito::class, 'c')
            ->join('c.idProducto', 'p')
            ->where('p.idUser = :user')
            ->andWhere('c.comprado = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $this->render('seller/sales.html.twig', [
            'ventas' => $ventas,
        ]);
    }
}
