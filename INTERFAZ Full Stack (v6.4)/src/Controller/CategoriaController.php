<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Categoria;
use App\Entity\Producto;
use App\Form\CategoriaTypeForm;
use App\Form\EditCategoriaTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class CategoriaController extends AbstractController
{
    #[Route('/admin/category/add', name: 'admin_add_category')]
    public function addCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden agregar categorías.');
            return $this->redirectToRoute('home');
        }

        $categoria = new Categoria();
        $form = $this->createForm(CategoriaTypeForm::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoria);
            $entityManager->flush();

            $this->addFlash('success', 'Categoría agregada correctamente.');
            return $this->redirectToRoute('home');
        }

        return $this->render('admin/admin_add_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/category/edit/{id}', name: 'admin_edit_category')]
    public function editCategory(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden modificar categorías.');
            return $this->redirectToRoute('home');
        }

        $categoria = $entityManager->getRepository(Categoria::class)->find($id);

        if (!$categoria) {
            $this->addFlash('danger', 'La categoría no existe.');
            return $this->redirectToRoute('admin_categories');
        }

        $form = $this->createForm(EditCategoriaTypeForm::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Categoría actualizada correctamente.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/admin_edit_category.html.twig', [
            'form' => $form->createView(),
            'categoria' => $categoria,
        ]);
    }

    #[Route('/admin/category/delete/{id}', name: 'admin_delete_category', methods: ['POST'])]
    public function deleteCategory(int $id, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden eliminar categorías.');
            return $this->redirectToRoute('home');
        }

        $categoria = $entityManager->getRepository(Categoria::class)->find($id);

        if (!$categoria) {
            $this->addFlash('danger', 'La categoría no existe.');
            return $this->redirectToRoute('admin_categories');
        }

        // Verificar si hay productos asociados a esta categoría
        $productosAsociados = $entityManager->getRepository(Producto::class)->findBy(['idCat' => $categoria]);

        if ($productosAsociados) {
            $this->addFlash('warning', 'No se puede eliminar esta categoría porque tiene productos asociados.');
            return $this->redirectToRoute('admin_categories');
        }

        $entityManager->remove($categoria);
        $entityManager->flush();

        $this->addFlash('success', 'Categoría eliminada correctamente.');
        return $this->redirectToRoute('admin_categories');
    }
}
