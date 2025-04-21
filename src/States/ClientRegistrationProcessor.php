<?php

namespace App\States;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ClientRegistrationInput;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class ClientRegistrationProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!$data instanceof ClientRegistrationInput) {
            throw new \InvalidArgumentException('Invalid input');
        }

        $client = new Client();
        $client->setName($data->clientName);
        $this->em->persist($client);

        $user = new User();
        $user->setEmail($data->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data->password));
        $user->setFirstname($data->firstname);
        $user->setLastname($data->lastname);
        $user->setRoles(['ROLE_CLIENT']);
        $user->setClient($client);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
