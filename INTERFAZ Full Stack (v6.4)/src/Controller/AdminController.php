<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Entity\Categoria;
use App\Entity\Producto;
use App\Entity\Carrito;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\UserFormType;
use App\Form\RegisterUserTypeForm;
use App\Form\EditPoductoSellerTypeForm;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        //try {
            if (!$this->getUser()) {
                $this->addFlash('danger', 'Debes iniciar sesión para acceder al panel de administración.');
                return $this->redirectToRoute('app_login');
            }

            if (!$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden entrar al panel.');
                return $this->redirectToRoute('home');
            }

            return $this->render('admin/index.html.twig');
        //} catch (\Exception $e) {
        //    $this->addFlash('danger', 'Ocurrió un error al cargar el panel de administración. Inténtalo nuevamente más tarde.');
        //    return $this->redirectToRoute('app_login');
        //}
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager)
    {
        $totalUsuarios = $entityManager->getRepository(User::class)->count([]);
        $totalProductos = $entityManager->getRepository(Producto::class)->count(['activo' => true]);
        $totalVentas = $entityManager->getRepository(Carrito::class)->createQueryBuilder('c')
            ->select('SUM(c.precio_total)')
            ->where('c.comprado = true')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        return $this->render('admin/dashboard.html.twig', [
            'totalUsuarios' => $totalUsuarios,
            'totalProductos' => $totalProductos,
            'totalVentas' => $totalVentas ?? 0,
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('search');

        // Obtener la lista de usuarios no administradores
        $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.roles NOT LIKE :adminRole')
            ->setParameter('adminRole', '%ROLE_ADMIN%');

        if ($searchTerm) {
            $queryBuilder
                ->andWhere('u.nombre LIKE :searchTerm OR u.apellido LIKE :searchTerm OR u.email LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $users = $queryBuilder->getQuery()->getResult();

        // Validaciones
        if (!$users && !$searchTerm) {
            $this->addFlash('warning', 'No hay usuarios registrados en el sistema.');
        } elseif (!$users) {
            $this->addFlash('warning', 'No se encontraron usuarios con ese criterio.');
        }

        return $this->render('admin/admin_users.html.twig', [
            'users' => $users,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/admin/registerUser', name: 'app_registerUser')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden registrar usuarios.');
                return $this->redirectToRoute('home');
            }

            $user = new User();
            $form = $this->createForm(RegisterUserTypeForm::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    try {
                        $rolesSeleccionados = $form->get('roles')->getData();
                        $user->setRoles([$rolesSeleccionados]);

                        // Codificar la contraseña antes de guardarla
                        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                        $user->setPassword($hashedPassword);

                        $entityManager->persist($user);
                        $entityManager->flush();

                        // ✅ Mensaje de éxito
                        $this->addFlash('success', '¡Usuario registrado correctamente!');
                        
                        return $this->redirectToRoute('admin_users');
                    } catch (\Exception $e) {
                        // ⚠️ Mensaje de error en caso de fallo de base de datos
                        $this->addFlash('danger', 'Error al registrar el usuario. Inténtalo nuevamente más tarde.');
                    }
                } else {
                    // ⚠️ Mensaje de error si el formulario no es válido
                    $this->addFlash('danger', 'Error en el registro. Verifica los datos ingresados.');
                }
            }

            return $this->render('admin/register.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Ocurrió un error inesperado. Inténtalo nuevamente más tarde.');
            return $this->redirectToRoute('admin_users');
        }
    }

    #[Route('/admin/modify/{id}', name: 'app_modifyUser')]
    public function modifyUser(int $id, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        //try {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden modificar usuarios.');
                return $this->redirectToRoute('home');
            }

            if (!$this->getUser()) {
                $this->addFlash('danger', 'Debes iniciar sesión para modificar usuarios.');
                return $this->redirectToRoute('app_login');
            }

            $user = $entityManager->getRepository(User::class)->find($id);

            if (!$user) {
                $this->addFlash('danger', 'Error: Usuario no encontrado.');
                return $this->redirectToRoute('admin_users');
            }

            $form = $this->createForm(UserFormType::class, $user);
            $form->get('telefono')->setData($user->getTelefono() ?? '');
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    try {
                        if ($form->get('roles')->getData()) {
                            $user->setRoles([$form->get('roles')->getData()]);
                        }

                        $newPassword = $form->get('password')->getData();
                        if (!empty($newPassword)) { // ✅ Evitamos null
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

                        $this->addFlash('success', '¡Usuario modificado correctamente!');
                        return $this->redirectToRoute('admin_users');
                    } catch (\Exception $e) {
                        $this->addFlash('danger', 'Error al modificar el usuario. Inténtalo nuevamente más tarde.');
                    }
                } else {
                    $this->addFlash('danger', 'Error en la modificación. Verifica los datos ingresados.');
                }
            }

            return $this->render('admin/modifyUser.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        //} catch (\Exception $e) {
        //    $this->addFlash('danger', 'Ocurrió un error inesperado. Inténtalo nuevamente más tarde.');
        //    return $this->redirectToRoute('admin');
        //}
    }

    #[Route('/admin/delete/{id}', name: 'app_deleteUser')]
    public function deleteUser(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        //try {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden eliminar usuarios.');
                return $this->redirectToRoute('home');
            }

            if (!$this->getUser()) {
                $this->addFlash('danger', 'Debes iniciar sesión para eliminar usuarios.');
                return $this->redirectToRoute('app_login');
            }

            $user = $entityManager->getRepository(User::class)->find($id);

            if (!$user) {
                $this->addFlash('danger', 'Error: Usuario no encontrado.');
                return $this->redirectToRoute('admin_users');
            }

            try {
                $entityManager->remove($user);
                $entityManager->flush();

                $this->addFlash('success', '¡Usuario eliminado correctamente!');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al eliminar el usuario. Inténtalo nuevamente más tarde.');
            }

            return $this->redirectToRoute('admin_users');
        //} catch (\Exception $e) {
        //    $this->addFlash('danger', 'Ocurrió un error inesperado. Inténtalo nuevamente más tarde.');
        //    return $this->redirectToRoute('admin');
        //}
    }

    #[Route('/admin/sellers_products/{id}', name: 'admin_seller_products')]
    public function sellerProducts(int $id, Request $request, EntityManagerInterface $entityManager)
    {
        // Obtener el usuario seleccionado
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user || !in_array('ROLE_SELLER', $user->getRoles())) {
            $this->addFlash('danger', 'El usuario seleccionado no es un vendedor o no existe.');
            return $this->redirectToRoute('admin_users');
        }

        // Obtener el término de búsqueda
        $searchTerm = $request->query->get('search');

        $queryBuilder = $entityManager->getRepository(Producto::class)->createQueryBuilder('p')
            ->innerJoin('p.idCat', 'c') // 🔹 Relacionamos productos con categorías
            ->where('p.idUser = :seller')
            ->setParameter('seller', $user);

        if ($searchTerm) {
            $queryBuilder
                ->andWhere('p.nombre LIKE :searchTerm OR p.descripcion LIKE :searchTerm OR c.nombre LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $productos = $queryBuilder->getQuery()->getResult();

        // Validaciones
        if (!$productos && !$searchTerm) {
            $this->addFlash('warning', 'No hay productos registrados para este vendedor.');
        } elseif (!$productos) {
            $this->addFlash('warning', 'No se encontraron productos con ese criterio.');
        }

        return $this->render('admin/admin_seller_products.html.twig', [
            'user' => $user,
            'productos' => $productos,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/admin/seller_product/toggle/{id}', name: 'admin_toggle_product_status')]
    public function toggleProductStatus(EntityManagerInterface $entityManager, int $id): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden modificar el estado de los productos.');
            return $this->redirectToRoute('home');
        }

        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto) {
            $this->addFlash('danger', 'Producto no encontrado.');
            return $this->redirectToRoute('admin_users');
        }

        // ✅ Alternar estado activo/inactivo
        $producto->setActivo(!$producto->isActivo());
        $entityManager->flush();

        $this->addFlash('success', 'Estado del producto actualizado correctamente.');
        return $this->redirectToRoute('admin_seller_products', ['id' => $producto->getIdUser()->getId()]);
    }

    #[Route('/admin/seller_product_edit/{id}', name: 'admin_seller_edit_product')]
    public function sellerProductsEdit(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Acceso denegado. Solo los administradores pueden modificar productos.');
            return $this->redirectToRoute('admin_users');
        }

        $producto = $entityManager->getRepository(Producto::class)->find($id);

        if (!$producto) {
            $this->addFlash('danger', 'Producto no encontrado.');
            return $this->redirectToRoute('admin_users');
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
            return $this->redirectToRoute('admin_seller_products', ['id' => $producto->getIdUser()->getId()]);
        }

        return $this->render('admin/admin_seller_product_edit.html.twig', [
            'form' => $form->createView(),
            'producto' => $producto,
        ]);
    }

    #[Route('/admin/categories', name: 'admin_categories')]
    public function categories(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtener el término de búsqueda
        $searchTerm = $request->query->get('search');

        // Crear la consulta base
        $queryBuilder = $entityManager->getRepository(Categoria::class)->createQueryBuilder('c');

        if ($searchTerm) {
            $queryBuilder
                ->where('c.nombre LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $categorias = $queryBuilder->orderBy('c.nombre', 'ASC')->getQuery()->getResult();

        // Validaciones
        if (!$categorias && !$searchTerm) {
            $this->addFlash('warning', 'No hay categorías registradas en el sistema.');
        } elseif (!$categorias) {
            $this->addFlash('warning', 'No se encontraron categorías con ese criterio.');
        }

        return $this->render('admin/admin_categories.html.twig', [
            'categorias' => $categorias,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/admin/products', name: 'admin_products')]
    public function products(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtener el término de búsqueda
        $searchTerm = $request->query->get('search');

        // Crear la consulta base
        $queryBuilder = $entityManager->getRepository(Producto::class)->createQueryBuilder('p')
            ->innerJoin('p.idCat', 'c') // 🔹 Relacionamos productos con categorías
            ->innerJoin('p.idUser', 'u') // 🔹 Relacionamos productos con vendedores
            ->orderBy('p.nombre', 'ASC');

        if ($searchTerm) {
            $queryBuilder
                ->where('p.nombre LIKE :searchTerm OR p.descripcion LIKE :searchTerm OR c.nombre LIKE :searchTerm OR u.email LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $productos = $queryBuilder->getQuery()->getResult();

        // Validaciones
        if (!$productos && !$searchTerm) {
            $this->addFlash('warning', 'No hay productos registrados en el sistema.');
        } elseif (!$productos) {
            $this->addFlash('warning', 'No se encontraron productos con el término ingresado.');
        }

        return $this->render('admin/admin_products.html.twig', [
            'productos' => $productos,
            'searchTerm' => $searchTerm,
        ]);
    }
}
