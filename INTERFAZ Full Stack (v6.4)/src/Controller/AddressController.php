<?php

namespace App\Controller;

use App\Entity\Direccion;
use App\Entity\User;
use App\Form\DireccionTypeForm;
use App\Form\EditDireccionTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class AddressController extends AbstractController
{
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

    #[Route('/profile/addresses/add', name: 'add_address')]
    public function addAddress(Request $request, EntityManagerInterface $entityManager)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        $direccion = new Direccion();
        $form = $this->createForm(DireccionTypeForm::class, $direccion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $direccion->setIdUser($user);

            if ($form->get('activo')->getData()) {
                // ✅ Desactivar todas las direcciones y activar la seleccionada
                foreach ($user->getDireccions() as $dir) {
                    $dir->setActivo(false);
                }
                $direccion->setActivo(true);
            } else {
                // ✅ No modificar el estado de las otras direcciones
                $direccion->setActivo(false);
            }

            $entityManager->persist($direccion);
            $entityManager->flush();

            $this->addFlash('success', 'Dirección añadida correctamente.');
            return $this->redirectToRoute('profile_addresses');
        }

        return $this->render('user/add_address.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/addresses/edit/{id}', name: 'edit_address')]
    public function editAddress(Request $request, EntityManagerInterface $entityManager, int $id)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        $direccion = $entityManager->getRepository(Direccion::class)->findOneBy(['id' => $id, 'idUser' => $user]);
        if (!$direccion) {
            $this->addFlash('danger', 'Dirección no encontrada.');
            return $this->redirectToRoute('profile_addresses');
        }

        $form = $this->createForm(EditDireccionTypeForm::class, $direccion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('activo')->getData()) {
                // ✅ Desactivar todas las direcciones y activar la seleccionada
                foreach ($user->getDireccions() as $dir) {
                    $dir->setActivo(false);
                }
                $direccion->setActivo(true);
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'Dirección actualizada correctamente.');
            return $this->redirectToRoute('profile_addresses');
        }

        return $this->render('user/edit_address.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/addresses/set-active/{id}', name: 'set_active_address')]
    public function setActiveAddress(EntityManagerInterface $entityManager, int $id)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        $direccion = $entityManager->getRepository(Direccion::class)->findOneBy(['id' => $id, 'idUser' => $user]);
        if (!$direccion) {
            $this->addFlash('danger', 'Dirección no encontrada.');
            return $this->redirectToRoute('profile_addresses');
        }

        // ✅ Desactivar todas las direcciones y activar la seleccionada.
        foreach ($user->getDireccions() as $dir) {
            $dir->setActivo(false);
        }
        $direccion->setActivo(true);

        $entityManager->flush();
        $this->addFlash('success', 'Dirección establecida como predeterminada.');
        return $this->redirectToRoute('profile_addresses');
    }

    #[Route('/profile/addresses/remove/{id}', name: 'remove_address')]
    public function removeAddress(EntityManagerInterface $entityManager, int $id)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        $direccion = $entityManager->getRepository(Direccion::class)->findOneBy(['id' => $id, 'idUser' => $user]);
        if (!$direccion) {
            $this->addFlash('danger', 'Dirección no encontrada.');
            return $this->redirectToRoute('profile_addresses');
        }

        $entityManager->remove($direccion);
        $entityManager->flush();

        $this->addFlash('success', 'Dirección eliminada correctamente.');
        return $this->redirectToRoute('profile_addresses');
    }
}
