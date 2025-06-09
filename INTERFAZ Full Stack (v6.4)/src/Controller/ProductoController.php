<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Producto;
use App\Form\PoductoTypeForm;
use App\Form\EditPoductoTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class ProductoController extends AbstractController
{
    #[Route('/admin/product/add', name: 'admin_add_product')]
    public function addProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden agregar productos.');
            return $this->redirectToRoute('home');
        }

        $producto = new Producto();
        $form = $this->createForm(PoductoTypeForm::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Si el producto está en oferta pero no tiene precio anterior, lo asignamos
            if ($producto->isOferta() && !$producto->getPrecioAnterior()) {
                $producto->setPrecioAnterior($producto->getPrecio());
            }

            $entityManager->persist($producto);
            $entityManager->flush();

            $this->addFlash('success', 'Producto agregado correctamente.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/admin_add_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/product/edit/{id}', name: 'admin_edit_product')]
    public function editProduct(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden modificar productos.');
            return $this->redirectToRoute('home');
        }

        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto) {
            $this->addFlash('danger', 'El producto no existe.');
            return $this->redirectToRoute('admin_products');
        }

        $form = $this->createForm(EditPoductoTypeForm::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Si el producto está en oferta pero no tiene precio anterior, lo asignamos
            if ($producto->isOferta() && !$producto->getPrecioAnterior()) {
                $producto->setPrecioAnterior($producto->getPrecio());
            }

            $entityManager->flush();

            $this->addFlash('success', 'Producto actualizado correctamente.');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/admin_edit_product.html.twig', [
            'form' => $form->createView(),
            'producto' => $producto,
        ]);
    }

    #[Route('/admin/product/delete/{id}', name: 'admin_delete_product', methods: ['POST'])]
    public function deleteProduct(int $id, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden eliminar productos.');
            return $this->redirectToRoute('home');
        }

        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto) {
            $this->addFlash('danger', 'El producto no existe.');
            return $this->redirectToRoute('admin_products');
        }

        $entityManager->remove($producto);
        $entityManager->flush();

        $this->addFlash('success', 'Producto eliminado correctamente.');
        return $this->redirectToRoute('admin_products');
    }

    #[Route('/admin/product/toggle/{id}', name: 'admin_toggle_status')]
    public function toggleProductStatus(EntityManagerInterface $entityManager, int $id): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden modificar el estado de los productos.');
            return $this->redirectToRoute('home');
        }

        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto) {
            $this->addFlash('danger', 'Producto no encontrado.');
            return $this->redirectToRoute('admin_products');
        }

        // ✅ Alternar estado activo/inactivo
        $producto->setActivo(!$producto->isActivo());
        $entityManager->flush();

        $this->addFlash('success', 'Estado del producto actualizado correctamente.');
        return $this->redirectToRoute('admin_products');
    }
}
