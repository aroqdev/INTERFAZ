<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\RegisterFormTypeForm;
use App\Form\RegisterUserTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                //try {
                $rolesSeleccionados = [$form->get('roles')->getData()];
                $user->setRoles($rolesSeleccionados);

                // Codificar la contraseña antes de guardarla
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                // ✅ Mensaje de éxito
                $this->addFlash('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
                
                return $this->redirectToRoute('app_login');
            //} catch (\Exception $e) {
                // ⚠️ Mensaje de error en caso de fallo de base de datos
            //    $this->addFlash('danger', 'Ocurrió un error al registrar el usuario. Inténtalo nuevamente más tarde.');
            //}
            }
            else {
            // ⚠️ Mensaje de error si el formulario no es válido
            $this->addFlash('danger', 'Error en el registro. Revisa los datos ingresados e intenta nuevamente.');
            }
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/registerAdmin', name: 'app_registerAdmin')]
    public function registerAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $admin = new User();
        $form = $this->createForm(RegisterFormTypeForm::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                //try {
                    // Asignar el rol de administrador
                    $admin->setRoles(['ROLE_ADMIN']);

                    // Codificar la contraseña antes de guardarla
                    $hashedPassword = $passwordHasher->hashPassword($admin, $admin->getPassword());
                    $admin->setPassword($hashedPassword);

                    $entityManager->persist($admin);
                    $entityManager->flush();

                    // ✅ Mensaje de éxito
                    $this->addFlash('success', '¡Registro de administrador exitoso! Ahora puedes iniciar sesión.');

                    return $this->redirectToRoute('app_login');
                //} catch (\Exception $e) {
                //    // ⚠️ Mensaje de error en caso de fallo de base de datos
                //    $this->addFlash('danger', 'Error al registrar el administrador. Inténtalo nuevamente más tarde.');
                //}
            } else {
                // ⚠️ Mensaje de error si el formulario no es válido
                $this->addFlash('danger', 'Error en el registro. Verifica los datos ingresados.');
            }
        }

        return $this->render('registration/registerAdmin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
