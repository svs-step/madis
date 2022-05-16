<?php

namespace App\Domain\User\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Application\Interfaces\CollectivityRelated;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Treatment;
use App\Domain\User\Model\Collectivity;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class UserBelongsToCollectivityExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
            // Return all elements because user is admin
            return;
        }

        if (Request::class === $resourceClass) {
            //Handle treatment case
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.collectivity = :user_collectivity', $rootAlias));
            $queryBuilder->setParameter(
                'user_collectivity',
                $user instanceof CollectivityRelated ? $user->getCollectivity() : null
            );
        }

        if (Treatment::class === $resourceClass) {
            //Handle treatment case
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.collectivity = :user_collectivity', $rootAlias));
            $queryBuilder->setParameter(
                'user_collectivity',
                $user instanceof CollectivityRelated ? $user->getCollectivity() : null
            );
        }

        if (Collectivity::class === $resourceClass) {
            // Handle collectivity case
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.id = :user_collectivity', $rootAlias));
            $queryBuilder->setParameter(
                'user_collectivity',
                $user instanceof CollectivityRelated ? $user->getCollectivity()->getId() : null
            );
        }
    }
}
