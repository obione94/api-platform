<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\Token\ConfirmationEmailToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmationEmailController extends AbstractController
{
    #[Route('/confirmation/email.html', name: 'app_confirmation_email',)]
    public function index(Request $request, ConfirmationEmailToken $confirmationEmailToken, UserRepository $userRepository): Response
    {
        try {
            if (null === $request->get("token")) {
                return $this->render('invalideToken/index.html.twig', []);
            }

            if (false === $confirmationEmailToken->isValidToken($request->get("token"))) {
                return $this->render('invalideToken/index.html.twig', []);
            }

            if (null === $user = $userRepository->loadUserByIdentifier(
                $confirmationEmailToken->getUserEmail($request->get("token"))
            )) {
                return $this->render('invalideToken/index.html.twig', []);
            }

            $user->setIsVerified(true);
            $userRepository->save($user, true);

        } catch (\Exception $e) {
            return $this->render('invalideToken/index.html.twig', []);
        }

        return $this->render('confirmation_email/index.html.twig', ['controller_name' => $user->getUserName()]);
    }
}
