<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\Producto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $entityManager)
    {
        // 🔹 Obtener todos los productos activos de la base de datos
        $productos = $entityManager->getRepository(Producto::class)->findBy(['activo' => true]);

        // 🔹 Obtener todas las categorías
        $categorias = $entityManager->getRepository(Categoria::class)->findAll();

        // 🔹 Pasar los productos a la plantilla
        return $this->render('index.html.twig', [
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }

    #[Route('/producto/{id}', name: 'product_details')]
    public function details(EntityManagerInterface $entityManager, int $id)
    {
        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto  || !$producto->isActivo()) {
            $this->addFlash('warning', 'Producto no encontrado.');
            return $this->redirectToRoute('home');
        }

        return $this->render('details.html.twig', [
            'producto' => $producto,
        ]);
    }

    #[Route('/categoria/{id}', name: 'category_products')]
    public function category(EntityManagerInterface $entityManager, int $id)
    {
        // ✅ Obtener la categoría y sus productos directamente
        $categoria = $entityManager->getRepository(Categoria::class)->find($id);

        if (!$categoria) {
            throw $this->createNotFoundException('Categoría no encontrada.');
        }

        // ✅ Filtrar solo productos activos
        $productos = $entityManager->getRepository(Producto::class)->findBy(['idCat'=>$categoria->getId(),'activo'=>true]);

        // ✅ Obtener todas las categorías para el sidebar
        $categorias = $entityManager->getRepository(Categoria::class)->findAll();

        return $this->render('category.html.twig', [
            'categoria' => $categoria,
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }

    #[Route('/buscar', name: 'search', methods: ['GET'])]
    public function search(EntityManagerInterface $entityManager, Request $request)
    {
        $query = $request->query->get('query');

        if (!$query) {
            return $this->redirectToRoute('home');
        }

        // ✅ Buscar solo productos activos que coincidan con el término
        $productos = $entityManager->getRepository(Producto::class)->createQueryBuilder('p')
            ->where('(p.nombre LIKE :query OR p.descripcion LIKE :query) AND p.activo = true')
            ->setParameter('query', "%{$query}%")
            ->getQuery()
            ->getResult();

        $categorias = $entityManager->getRepository(Categoria::class)->createQueryBuilder('c')
            ->where('c.nombre LIKE :query')
            ->setParameter('query', "%{$query}%")
            ->getQuery()
            ->getResult();

        return $this->render('search.html.twig', [
            'query' => $query,
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }
}
