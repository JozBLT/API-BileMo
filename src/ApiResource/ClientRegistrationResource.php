<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\ClientRegistrationInput;
use App\States\ClientRegistrationProcessor;

#[ApiResource(
    shortName: 'ClientRegistration',
    operations: [
        new Post(
            uriTemplate: '/register',
            processor: ClientRegistrationProcessor::class,
        )
    ],
    input: ClientRegistrationInput::class,
    output: false
)]
final class ClientRegistrationResource {}
