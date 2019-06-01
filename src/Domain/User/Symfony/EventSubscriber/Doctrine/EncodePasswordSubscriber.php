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

namespace App\Domain\User\Symfony\EventSubscriber\Doctrine;

use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class EncodePasswordSubscriber implements EventSubscriber
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * PrePersist
     * - User : If plainPassword is set, hash it and set password.
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        if (!$args->getObject() instanceof User) {
            return;
        }

        $this->encodePassword($args);
    }

    /**
     * PreUpdate
     * - User : If plainPassword is set, hash it and set password.
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        if (!$args->getObject() instanceof User) {
            return;
        }

        $this->encodePassword($args);
    }

    /**
     * Encode plainPassword in password field.
     *
     * @param LifecycleEventArgs $args
     */
    public function encodePassword(LifecycleEventArgs $args): void
    {
        $model = $args->getObject();

        if (!$model instanceof User) {
            return;
        }

        if (\is_null($model->getPlainPassword())) {
            return;
        }

        $encoder = $this->encoderFactory->getEncoder($model);
        $model->setPassword($encoder->encodePassword($model->getPlainPassword(), '')); // No salt with bcrypt
        $model->eraseCredentials();
    }
}
