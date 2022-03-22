<?php

declare(strict_types=1);

namespace App\Domain\Notification\Twig\Extension;

use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\Notification as NotificationModel;
use App\Domain\Registry\Model\Violation;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NotificationExtension extends AbstractExtension
{
    protected TranslatorInterface $translator;
    protected RouterInterface $router;

    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router     = $router;
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
            '<a href="' . $this->router->generate('registry_treatment_show', ['id' => $notification->getObject()->id]) . '">' . $notification->getName() . '</a> '
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
