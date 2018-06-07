<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\User\Symfony\EventSubscriber;

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

        $this->process($args);
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

        dump('ENCODE');

        $this->process($args);
    }

    /**
     * Encode plainPassword in password field.
     *
     * @param LifecycleEventArgs $args
     */
    private function process(LifecycleEventArgs $args): void
    {
        /**
         * @var User
         */
        $model = $args->getObject();

        if (\is_null($model->getPlainPassword())) {
            return;
        }

        $encoder = $this->encoderFactory->getEncoder($model);
        $model->setPassword($encoder->encodePassword($model->getPlainPassword(), '')); // No salt with bcrypt
        $model->eraseCredentials();
    }
}
