<?php

namespace App\Controller;

use App\Security\Token\ConfirmationEmailToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmationEmailController extends AbstractController
{
    #[Route('/confirmation/email.html', name: 'app_confirmation_email',)]
    public function index(Request $request, ConfirmationEmailToken $confirmationEmailToken): Response
    {
        if (false === $confirmationEmailToken->isValidToken($request->get("token"))) {
            return $this->render('invalideToken/index.html.twig', []);
        }

        return $this->render('confirmation_email/index.html.twig', [
            'controller_name' => $confirmationEmailToken->decode($request->get("token"))['userName'],
        ]);
    }
}
