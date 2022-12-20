<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Mail\MailResetPassword;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SendLinkResetPassword extends AbstractController
{

    private UserRepository $userRepository;
    private MailResetPassword $mailResetPassword;

    public function __construct(UserRepository $userRepository, MailResetPassword $mailResetPassword)
    {
        $this->userRepository = $userRepository;
        $this->mailResetPassword = $mailResetPassword;
    }

    public function __invoke(Request $request): User
    {
        if (null !== ($user = $this->userRepository->loadUserByIdentifier($request->get("userName")))) {
            $this->mailResetPassword->send($user, 600);
        }

        return $user;
    }

}
