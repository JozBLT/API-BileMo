<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private ProcessorInterface $decorated,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof User) {
            return $this->decorated->process($data, $operation, $uriVariables, $context);
        }

        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof User) {
            throw new \LogicException('Logged user is not an instance of App\\Entity\\User.');
        }

        if (in_array('ROLE_CLIENT', $currentUser->getRoles(), true)) {
            $data->setClient($currentUser->getClient());
            $data->setRoles(['ROLE_USER']);
        }

        if ($data->getPassword()) {
            $hashed = $this->passwordHasher->hashPassword($data, $data->getPassword());
            $data->setPassword($hashed);
        }

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
