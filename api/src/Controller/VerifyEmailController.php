<?php

namespace App\Controller;

use App\DTO\User\UserConfirmationEmailDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class VerifyEmailController extends AbstractController
{

    public function __invoke(UserConfirmationEmailDTO $token, Request $request): UserConfirmationEmailDTO
    {
        return $token;
    }

}
