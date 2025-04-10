<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security) {}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($resourceClass !== User::class) {
            return;
        }

        $currentUser = $this->security->getUser();

        if (!$currentUser instanceof User) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (in_array('ROLE_ADMIN', $currentUser->getRoles(), true)) {
            return;
        }

        if (in_array('ROLE_CLIENT', $currentUser->getRoles(), true)) {
            $queryBuilder
                ->andWhere(sprintf('%s.client = :client', $rootAlias))
                ->setParameter('client', $currentUser->getClient());
            return;
        }

        if (in_array('ROLE_USER', $currentUser->getRoles(), true)) {
            $queryBuilder
                ->andWhere(sprintf('%s.id = :user_id', $rootAlias))
                ->setParameter('user_id', $currentUser->getId());
        }
    }
}
