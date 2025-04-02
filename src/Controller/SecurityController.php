<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityController extends AbstractController
{
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function login()
    {
        // Intercepted by LexikJWTAuthenticationBundle.
        throw new LogicException('This method should not be reached directly.');
    }
}
