<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class RegistrationConfirmationController extends AbstractController
{

    public function __construct(private UserRepository $userRepository)
    {

    }

    public function __invoke(Request $request)
    {
        $userName = "user@example.com";
        if (null === $uservalidateEmail = $this->userRepository->loadUserByIdentifier($userName)) {
            throw new UserNotFoundException(sprintf('There is no user with email "%s".', $userName));
        }

        return $uservalidateEmail;
    }

}
