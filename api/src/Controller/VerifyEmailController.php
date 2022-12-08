<?php

namespace App\Controller;

use App\DTO\User\UserConfirmationEmailDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class VerifyEmailController extends AbstractController
{

    public function __construct(private readonly UserRepository $userRepository)
    {

    }

    public function __invoke(UserConfirmationEmailDTO $token, Request $request): UserConfirmationEmailDTO
    {
        dump($request->attributes->all());
        return $token;
    }

}
