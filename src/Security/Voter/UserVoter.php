<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const DELETE = 'USER_DELETE';

    public function __construct(private readonly Security $security) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::DELETE && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof User) {
            return false;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('Logged user is not an instance of App\Entity\User.');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        if (in_array('ROLE_CLIENT', $user->getRoles(), true)) {
            return $subject->getClient()?->getId() === $user->getClient()?->getId();
        }

        return false;
    }
}
