<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $now = new DateTimeImmutable();

        // Création de 2 clients
        $client1 = new Client();
        $client1->setName('TechCorp');
        $client1->setCreatedAt($now);
        $client1->setUpdatedAt($now);
        $manager->persist($client1);

        $client2 = new Client();
        $client2->setName('MobilePro');
        $client2->setCreatedAt($now);
        $client2->setUpdatedAt($now);
        $manager->persist($client2);

        // Création de 4 utilisateurs (2 pour chaque client)
        $users = [
            ['email' => 'alice@techcorp.com', 'firstname' => 'Alice', 'lastname' => 'Durand', 'client' => $client1],
            ['email' => 'bob@techcorp.com', 'firstname' => 'Bob', 'lastname' => 'Martin', 'client' => $client1],
            ['email' => 'carla@mobilepro.com', 'firstname' => 'Carla', 'lastname' => 'Lemoine', 'client' => $client2],
            ['email' => 'david@mobilepro.com', 'firstname' => 'David', 'lastname' => 'Petit', 'client' => $client2],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setRoles(['ROLE_USER']);
            $user->setClient($userData['client']);
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $user->setCreatedAt($now);
            $user->setUpdatedAt($now);
            $manager->persist($user);
        }

        // Représentants clients
        $clientAdmins = [
            ['email' => 'admin@techcorp.com', 'firstname' => 'Admin', 'lastname' => 'TechCorp', 'client' => $client1],
            ['email' => 'admin@mobilepro.com', 'firstname' => 'Admin', 'lastname' => 'MobilePro', 'client' => $client2],
        ];

        foreach ($clientAdmins as $data) {
            $admin = new User();
            $admin->setEmail($data['email']);
            $admin->setFirstname($data['firstname']);
            $admin->setLastname($data['lastname']);
            $admin->setRoles(['ROLE_CLIENT']);
            $admin->setClient($data['client']);
            $admin->setPassword($this->hasher->hashPassword($admin, 'password'));
            $admin->setCreatedAt($now);
            $admin->setUpdatedAt($now);
            $manager->persist($admin);
        }

        // Super admin
        $superAdmin = new User();
        $superAdmin->setEmail('admin@bilemo.com');
        $superAdmin->setFirstname('Super');
        $superAdmin->setLastname('Admin');
        $superAdmin->setRoles(['ROLE_ADMIN']);
        $superAdmin->setPassword($this->hasher->hashPassword($superAdmin, 'adminpassword'));
        $superAdmin->setCreatedAt($now);
        $superAdmin->setUpdatedAt($now);
        $manager->persist($superAdmin);

        // Création de 10 produits
        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setName("BileMo Phone $i");
            $product->setBrand('BileMo');
            $product->setDescription("Le BileMo Phone $i est un smartphone haut de gamme avec des performances exceptionnelles.");
            $product->setPrice(499.99 + ($i * 25));
            $product->setCreatedAt(new DateTimeImmutable('-' . rand(1, 100) . ' days'));
            $product->setUpdatedAt($now);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
