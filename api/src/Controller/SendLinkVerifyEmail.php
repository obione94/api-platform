<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\ProductNotFoundException;
use App\Repository\UserRepository;
use App\Service\Mail\MailVerifyEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SendLinkVerifyEmail extends AbstractController
{

    private UserRepository $userRepository;
    private MailVerifyEmail $mailVerifyEmail;

    public function __construct(UserRepository $userRepository, MailVerifyEmail $mailVerifyEmail)
    {
        $this->userRepository = $userRepository;
        $this->mailVerifyEmail = $mailVerifyEmail;
    }

    /**
     * @throws ProductNotFoundException
     */
    public function __invoke(Request $request): User
    {
        if (null !== ($user = $this->userRepository->loadUserByIdentifier($request->get("userName")))) {
            $this->mailVerifyEmail->sendWelcomeEmail($user, 600);

            return $user;
        }

        throw new ProductNotFoundException(sprintf('The mail "%s" does not exist.', $request->get("userName")));
    }

}
