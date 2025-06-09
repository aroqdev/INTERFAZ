<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
        //try {
            if ($this->getUser()) {
                $this->addFlash('success', '¡Bienvenido de nuevo! Has iniciado sesión correctamente.');
                return $this->redirectToRoute('home');
            }

            // Obtener el error de autenticación, si existe
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            if ($error) {
                // ⚠️ Mensaje de error específico en caso de fallo en el login
                $this->addFlash('danger', 'Error al iniciar sesión: credenciales incorrectas. Verifica tu correo y contraseña.');
            }

            return $this->render('login/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        //} catch (\Exception $e) {
        //    // ⚠️ Mensaje de error en caso de fallo inesperado en la autenticación
        //    $this->addFlash('danger', 'Ocurrió un problema al intentar iniciar sesión. Inténtalo nuevamente más tarde.');
        //    return $this->redirectToRoute('app_login');
        //}
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout () {}
}
