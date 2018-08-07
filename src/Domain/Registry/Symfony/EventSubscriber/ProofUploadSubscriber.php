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

namespace App\Domain\Registry\Symfony\EventSubscriber;

use App\Domain\Registry\Model\Proof;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Gaufrette\FilesystemInterface;

class ProofUploadSubscriber implements EventSubscriber
{
    /**
     * @var FilesystemInterface
     */
    private $documentFilesystem;

    public function __construct(FilesystemInterface $documentFilesystem)
    {
        $this->documentFilesystem = $documentFilesystem;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->process($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->process($args);
    }

    private function process(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Proof) {
            return;
        }
    }
}
