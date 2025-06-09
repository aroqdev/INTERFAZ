<?php

namespace App\Controller;

use App\Entity\Pago;
use App\Entity\User;
use App\Form\PagoTarjetaTypeForm;
use App\Form\PagoCuentaTypeForm;
use App\Form\PagoPaypalTypeForm;
use App\Form\PagoRegaloTypeForm;
use App\Form\EditTarjetaTypeForm;
use App\Form\EditCuentaTypeForm;
use App\Form\EditPaypalTypeForm;
use App\Form\EditRegaloTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class PagoController extends AbstractController
{
#[Route('/profile/payments/add/card', name: 'add_card_payment')]
    public function addCardPayment(Request $request, EntityManagerInterface $entityManager)
    {
        return $this->handlePaymentForm($request, $entityManager, new Pago(), PagoTarjetaTypeForm::class, 'user/add_card_payment.html.twig');
    }

    #[Route('/profile/payments/add/bank', name: 'add_bank_payment')]
    public function addBankPayment(Request $request, EntityManagerInterface $entityManager)
    {
        return $this->handlePaymentForm($request, $entityManager, new Pago(), PagoCuentaTypeForm::class, 'user/add_bank_payment.html.twig');
    }

    #[Route('/profile/payments/add/paypal', name: 'add_paypal_payment')]
    public function addPaypalPayment(Request $request, EntityManagerInterface $entityManager)
    {
        return $this->handlePaymentForm($request, $entityManager, new Pago(), PagoPaypalTypeForm::class, 'user/add_paypal_payment.html.twig');
    }

    #[Route('/profile/payments/add/gift', name: 'add_gift_payment')]
    public function addGiftPayment(Request $request, EntityManagerInterface $entityManager)
    {
        return $this->handlePaymentForm($request, $entityManager, new Pago(), PagoRegaloTypeForm::class, 'user/add_gift_payment.html.twig');
    }

    private function handlePaymentForm(Request $request, EntityManagerInterface $entityManager, Pago $pago, string $formType, string $template)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $form = $this->createForm($formType, $pago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pago->setIdUser($user);

            if ($form->get('activo')->getData()) {
                // ✅ Desactivar todos los métodos de pago y activar el nuevo
                foreach ($user->getPagos() as $pay) {
                    $pay->setActivo(false);
                }
                $pago->setActivo(true);
            } else {
                // ✅ No modificar el estado de los otros métodos
                $pago->setActivo(false);
            }

            $entityManager->persist($pago);
            $entityManager->flush();

            $this->addFlash('success', 'Método de pago añadido correctamente.');
            return $this->redirectToRoute('profile_payments');
        }

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/payments/edit/card/{id}', name: 'edit_card_payment')]
    public function editCardPayment(Request $request, EntityManagerInterface $entityManager, int $id)
    {
        return $this->handleEditPayment($request, $entityManager, $id, EditTarjetaTypeForm::class, 'user/edit_card_payment.html.twig');
    }

    #[Route('/profile/payments/edit/bank/{id}', name: 'edit_bank_payment')]
    public function editBankPayment(Request $request, EntityManagerInterface $entityManager, int $id)
    {
        return $this->handleEditPayment($request, $entityManager, $id, EditCuentaTypeForm::class, 'user/edit_bank_payment.html.twig');
    }

    #[Route('/profile/payments/edit/paypal/{id}', name: 'edit_paypal_payment')]
    public function editPaypalPayment(Request $request, EntityManagerInterface $entityManager, int $id)
    {
        return $this->handleEditPayment($request, $entityManager, $id, EditPaypalTypeForm::class, 'user/edit_paypal_payment.html.twig');
    }

    #[Route('/profile/payments/edit/gift/{id}', name: 'edit_gift_payment')]
    public function editGiftPayment(Request $request, EntityManagerInterface $entityManager, int $id)
    {
        return $this->handleEditPayment($request, $entityManager, $id, EditRegaloTypeForm::class, 'user/edit_gift_payment.html.twig');
    }

    private function handleEditPayment(Request $request, EntityManagerInterface $entityManager, int $id, string $formType, string $template)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $pago = $entityManager->getRepository(Pago::class)->findOneBy(['id' => $id, 'idUser' => $user]);

        if (!$pago) {
            $this->addFlash('danger', 'Método de pago no encontrado.');
            return $this->redirectToRoute('profile_payments');
        }

        $form = $this->createForm($formType, $pago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('activo')->getData()) {
                // ✅ Desactivar todos los métodos de pago y activar el nuevo
                foreach ($user->getPagos() as $pay) {
                    $pay->setActivo(false);
                }
                $pago->setActivo(true);
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'Método de pago actualizado correctamente.');
            return $this->redirectToRoute('profile_payments');
        }

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/payments/set-active/{id}', name: 'set_active_payment')]
    public function setActivePayment(EntityManagerInterface $entityManager, int $id)
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Debes iniciar sesión para acceder a tu perfil.');
            return $this->redirectToRoute('app_login');
        }

        // Obtener el usuario desde el repositorio
        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        $pago = $entityManager->getRepository(Pago::class)->findOneBy(['id' => $id, 'idUser' => $user]);
        if (!$pago) {
            $this->addFlash('danger', 'Método de pago no encontrado.');
            return $this->redirectToRoute('profile_payments');
        }

        // ✅ Desactivar todos los métodos de pago y activar el seleccionado
        foreach ($user->getPagos() as $pay) {
            $pay->setActivo(false);
        }
        $pago->setActivo(true);

        $entityManager->flush();
        $this->addFlash('success', 'Método de pago establecido como predeterminado.');
        return $this->redirectToRoute('profile_payments');
    }

    #[Route('/profile/payments/remove/{id}', name: 'remove_payment')]
    public function removePayment(EntityManagerInterface $entityManager, int $id)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $pago = $entityManager->getRepository(Pago::class)->findOneBy(['id' => $id, 'idUser' => $user]);
        if (!$pago) {
            $this->addFlash('danger', 'Método de pago no encontrado.');
            return $this->redirectToRoute('profile_payments');
        }

        $entityManager->remove($pago);
        $entityManager->flush();

        $this->addFlash('success', 'Método de pago eliminado correctamente.');
        return $this->redirectToRoute('profile_payments');
    }
}
