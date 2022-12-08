<?php

namespace App\Controller;

use App\Security\Token\ChangePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset/password.html', name: 'app_reset_password')]
    public function index(Request $request, ChangePasswordToken $changePasswordToken): Response
    {
        $template = 'reset_password/index.html.twig';
        $data = ['controller_name' => 'ResetPasswordController','token' => $request->get("token")];

        if (false === $changePasswordToken->isValidToken($request->get("token"))) {
            $template = 'invalideToken/index.html.twig';
            $data = [];
        }

        return $this->render($template, $data);
    }
}
