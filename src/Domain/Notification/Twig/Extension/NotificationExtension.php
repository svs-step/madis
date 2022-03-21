<?php

declare(strict_types=1);

namespace App\Domain\Notification\Twig\Extension;

use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\Notification as NotificationModel;
use App\Domain\Registry\Model\Violation;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NotificationExtension extends AbstractExtension
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('sentence', [$this, 'getSentence']),
        ];
    }

    public function getSentence(Notification $notification): string
    {
        $sentence =  '[' . $this->translator->trans($notification->getModule()) . '] ' .
            $this->translator->trans($notification->getAction()) . ' ';

        $sentence .= $this->translator->trans('label.de') . ' ' .
            $notification->getName() . ' '
        ;
        if ($notification->getModule() === 'notification.modules.' . NotificationModel::MODULES[Violation::class]) {
            $sentence .= $this->translator->trans('label.du') . ' ' .
                $notification->getObject()->getDate()->format('d/m/Y')
                . ' ';
        }
        if ($notification->getCollectivity()) {
            $sentence .= $this->translator->trans('label.par') . ' ' . $notification->getCollectivity()->getName();
        }

        return $sentence;
    }
}
