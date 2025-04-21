<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ClientRegistrationInput
{
    #[Assert\NotBlank]
    public string $clientName;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password;

    #[Assert\NotBlank]
    public string $firstname;

    #[Assert\NotBlank]
    public string $lastname;
}
