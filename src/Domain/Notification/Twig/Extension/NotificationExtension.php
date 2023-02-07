<?php

declare(strict_types=1);

namespace App\Domain\Notification\Twig\Extension;

use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\Notification as NotificationModel;
use App\Domain\Registry\Dictionary\ProofTypeDictionary;
use App\Domain\Registry\Dictionary\ViolationNatureDictionary;
use App\Domain\Registry\Model\Proof;
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
            new TwigFilter('link', [$this, 'getObjectLink']),
        ];
    }

    public function getSentence(Notification $notification): string
    {
        $sentence = '<strong>[' . $this->translator->trans($notification->getModule()) . ']</strong> ' .
            $this->translator->trans($notification->getAction()) . ' ';

        $sentence .= $this->translator->trans('label.de') . ' ' .
            '<a href="' . $this->getObjectLink($notification) . '">' . $notification->getName() . '</a> '
        ;

        if ($notification->getModule() === 'notification.modules.' . NotificationModel::MODULES[Violation::class]) {
            $natures = [];

            if ($notification->getObject()->violationNatures) {
                $raw = $notification->getObject()->violationNatures;
                if (is_string($raw)) {
                    $raw = explode(',', $raw);
                }
                $natures = array_map(function ($n) {return ViolationNatureDictionary::getNatures()[trim($n)]; }, (array) $raw);
            }
            $sentence .= '<strong>(' . join(', ', $natures) . ')</strong> ';
        }
        if ($notification->getModule() === 'notification.modules.' . NotificationModel::MODULES[Proof::class]) {
            $sentence .= '<strong>(' . ProofTypeDictionary::getTypes()[$notification->getObject()->type] . ')</strong> ';
        }
        if ($notification->getCollectivity()) {
            $sentence .= $this->translator->trans('label.par') . ' <strong>' . $notification->getCollectivity()->getName() . '</strong>';
        }

        return $sentence;
    }

    public function getObjectLink(Notification $notification): string
    {
        try {
            return $this->router->generate($this->getRouteForModule($notification->getModule()), ['id' => $notification->getObject()->id]);
        } catch (\Exception $e) {
            return '';
        }
    }

    private function getRouteForModule($module): string
    {
        switch ($module) {
            case 'notification.modules.treatment':
                return 'registry_treatment_show';
            case 'notification.modules.subcontractor':
            case 'notification.modules.contractor':
                return 'registry_contractor_show';
            case 'notification.modules.violation':
                return 'registry_violation_show';
            case 'notification.modules.proof':
                return 'registry_proof_edit';
            case 'notification.modules.action':
            case 'notification.modules.protect_action':
                return 'registry_mesurement_show';
            case 'notification.modules.request':
                return 'registry_request_show';
            case 'notification.modules.user':
                return 'user_user_edit';
            case 'notification.modules.documentation':
            case 'notification.modules.document':
                return 'documentation_document_edit';
            case 'notification.modules.maturity':
                return 'maturity_survey_edit';
        }

        return '';
    }
}
