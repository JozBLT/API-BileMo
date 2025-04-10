<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserDataPersister implements ProcessorInterface
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

        // 1. Client's association
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof User) {
            throw new \LogicException('Logged user is not an instance of App\Entity\User.');
        }

        $client = $currentUser->getClient();
        if ($client) {
            $data->setClient($client);
        }

        // User create from Client
        if (!in_array('ROLE_ADMIN', $currentUser->getRoles(), true)) {
            $data->setRoles(['ROLE_USER']);
            $data->setClient($currentUser->getClient());
        }

        // 2. Password hash
        if ($data->getPassword()) {
            $hashed = $this->passwordHasher->hashPassword($data, $data->getPassword());
            $data->setPassword($hashed);
        }

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
