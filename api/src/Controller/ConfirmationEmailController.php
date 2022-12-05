<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\Encoder\NixillaJWTEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class ConfirmationEmailController extends AbstractController
{
    #[Route('/confirmation/email.html', name: 'app_confirmation_email',)]
    public function index(Request $request, NixillaJWTEncoder $nixillaJWTEncoder, UserRepository $userRepository): Response
    {
        $data = $nixillaJWTEncoder->decode($request->get("token"));
        if (null === $uservalidateEmail = $userRepository->loadUserByIdentifier($data['userName'])) {
            throw new UserNotFoundException(sprintf('There is no user with email "%s".', $data['userName']));
        }

        $uservalidateEmail->setIsVerified(true);
        $userRepository->save($uservalidateEmail, true);
        return $this->render('confirmation_email/index.html.twig', [
            'controller_name' => $uservalidateEmail->getUserName(),
        ]);
    }
}
