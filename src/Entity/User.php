<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Client;
use App\Entity\Trait\Timestampable;
use App\Repository\UserRepository;
use App\State\UserStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_CLIENT') or is_granted('ROLE_ADMIN')"),
        new Get(security: "object == user or object.getClient() == user.getClient() or is_granted('ROLE_ADMIN')"),
        new Post(
            denormalizationContext: ['groups' => ['user:write']],
            security: "is_granted('ROLE_CLIENT')",
            processor: UserStateProcessor::class
        ),
        new Post(
            uriTemplate: '/users/admin',
            denormalizationContext: ['groups' => ['admin:user:write']],
            security: "is_granted('ROLE_ADMIN')",
            processor: UserStateProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => ['user:write']],
            security: "object == user or object.getClient() == user.getClient()"
        ),

        new Patch(
            uriTemplate: '/users/{id}/admin',
            denormalizationContext: ['groups' => ['admin:user:write']],
            security: "is_granted('ROLE_ADMIN')",
            name: 'admin_patch_user',
            processor: UserStateProcessor::class,
        ),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getClient() == user.getClient()")
    ],
    normalizationContext: ['groups' => ['user:read', 'default:read']],
    denormalizationContext: ['groups' => ['user:write', 'default:write']]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', message: 'This email is already used')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[Assert\NotBlank(groups: ['admin:user:write'])]
    #[Assert\Email(groups: ['admin:user:write'])]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['user:read', 'user:write', 'admin:user:write'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    #[Groups(['user:read', 'admin:user:write'])]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[Assert\NotBlank(groups: ['admin:user:write'])]
    #[ORM\Column(type: 'string')]
    #[Groups(['user:write', 'admin:user:write'])]
    private ?string $password = null;

    #[Assert\Length(min: 3, max: 255, groups: ['user:write', 'admin:user:write'])]
    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'admin:user:write'])]
    private ?string $firstname = null;

    #[Assert\Length(min: 3, max: 255, groups: ['user:write', 'admin:user:write'])]
    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'admin:user:write'])]
    private ?string $lastname = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'users')]
    #[Groups(['user:read', 'admin:user:write'])]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }
}
