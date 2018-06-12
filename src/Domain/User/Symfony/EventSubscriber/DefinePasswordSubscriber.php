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

use App\Domain\User\Component\TokenGenerator;
use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class DefinePasswordSubscriber implements EventSubscriber
{
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(
        TokenGenerator $tokenGenerator
    ) {
        $this->tokenGenerator = $tokenGenerator;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
        ];
    }

    /**
     * PrePersist
     * - User : If no plainPassword, randomize it.
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $model = $args->getObject();
        if (!$model instanceof User) {
            return;
        }

        // No password is set, randomize one
        if (\is_null($model->getPlainPassword())) {
            $model->setPlainPassword($this->tokenGenerator->generateToken());
        }
    }
}
