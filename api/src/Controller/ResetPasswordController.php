<?php

namespace App\Controller;

use App\DTO\User\EmailDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Form\Type\ChangePasswordType;
use App\Security\Token\ChangePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

class ResetPasswordController extends AbstractController
{
    public function __construct(
        readonly UserPasswordHasherInterface $passwordHasher
    ){}

    #[Route('/reset/password.html', name: 'app_reset_password')]
    public function index(Request $request, ChangePasswordToken $changePasswordToken, UserRepository $userRepository): Response
    {
        if (null === $request->get("token")) {
            throw new NotFoundHttpException("Page introuvable");
        }

        if (false === $changePasswordToken->isValidToken($request->get("token"))) {
            return $this->render('invalideToken/index.html.twig', []);
        }

        $userEmail = $changePasswordToken->getUserEmail($request->get("token"));

        $user = $userRepository->loadUserByIdentifier($userEmail);
        $form = $this->createForm(ChangePasswordType::class,
            $user,
            [
                'action' => "/reset/password.html?token=".$request->get("token"),
                'method' => 'POST',
            ]
        );
        $form->get('_token')->setData($request->get("token"));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->all()["_token"]->getData() === $request->get("token")) {
                $userChange = $form->getData();
                $userRepository->upgradePassword(
                    $userChange,
                    $this->passwordHasher->hashPassword($userChange,$form->all()["plainPassword"]->getData())
                );

                return $this->redirectToRoute('app_password_changed');
            }

            return $this->render('invalideToken/index.html.twig', []);
        }

        return $this->render(
            'reset_password/index.html.twig',
            [
                'controller_name' => 'ResetPasswordController',
                'token' => $request->get("token"),
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/api/sendToken', name: 'send_token_password')]
    public function sendTokenChangePassword(Request $request, EmailDTO $emailDTO): Response
    {
        return new Response(json_encode(["response"=> "string"]));
    }

    #[Route('/password/changed.html', name: 'app_password_changed')]
    public function passwordIsChanged(): Response
    {
        return $this->render("reset_password/isChanged.html.twig");
    }
}
