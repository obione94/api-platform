<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\User\UserConfirmationEmailDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Token\ConfirmationEmailToken;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserConfirmationEmailProcessor implements ProcessorInterface
{

    public function __construct(private ProcessorInterface $persistProcessor, private readonly UserRepository $userRepository, private readonly ConfirmationEmailToken $confirmationEmailToken)
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!$data instanceof UserConfirmationEmailDTO) {
            throw new InvalidArgumentException(sprintf('instanceof %s' . "UserConfirmationEmailDTO"));
        }

        if (false === $this->confirmationEmailToken->isValidToken($data->getToken())) {
            throw new InvalidTokenException('Invalid JWT Token');
        }

        if (null === ($user = $this->userRepository->loadUserByIdentifier($this->confirmationEmailToken->decode($data->getToken())["userName"]))) {
            throw new UserNotFoundException('Utilisateur du token est introuvable');
        }
        $user->setIsVerified(true);
        $this->persistProcessor->process($user, $operation, $uriVariables, $context);

        return $user;
    }
}
