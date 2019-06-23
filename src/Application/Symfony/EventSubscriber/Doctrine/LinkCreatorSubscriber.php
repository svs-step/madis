<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Application\Symfony\EventSubscriber\Doctrine;

use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\Model\CreatorTrait;
use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class LinkCreatorSubscriber
 * Link authenticated user to object.
 */
class LinkCreatorSubscriber implements EventSubscriber
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
        ];
    }

    /**
     * PrePersist.
     *
     * Link creator to object.
     * That is to say that every time you will persist an object,
     * the user will be added to related object.
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        $user   = $this->userProvider->getAuthenticatedUser();
        $uses   = \class_uses($object);

        if (\in_array(CreatorTrait::class, $uses) && $user instanceof User) {
            $object->setCreator($user);
        }
    }
}
