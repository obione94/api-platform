<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\User\UserConfirmationEmailDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Token\ConfirmationEmailToken;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;

class UserConfirmationEmailProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly ProcessorInterface $persistProcessor,
        private readonly UserRepository $userRepository,
        private readonly ConfirmationEmailToken $confirmationEmailToken
    )
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (false === $this->confirmationEmailToken->isValidToken($data->getToken())) {
            throw new InvalidTokenException('Invalid JWT Token');
        }

        $user = $this->userRepository->loadUserByIdentifier($this->confirmationEmailToken->getUserEmail($data->getToken()));
        $user->setIsVerified(true);
        $this->persistProcessor->process($user, $operation, $uriVariables, $context);

        return $user;
    }
}
