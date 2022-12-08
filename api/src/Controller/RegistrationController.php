<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class RegistrationController extends AbstractController
{

    public function __construct(
        readonly UserPasswordHasherInterface $passwordHasher
    ){}

    public function __invoke(User $user, Request $request): User
    {
        dump($request->attributes->all());
        $user->setIsVerified(false);
        $user->setRoles(["ROLE_USER",]);
        $plainPassword = $user->getPassword();
        $password = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);

        if (false === $this->passwordHasher->isPasswordValid($user, $plainPassword)) {
            throw new BadCredentialsException('Password is not valid ');
        }

        return $user;
    }

}
