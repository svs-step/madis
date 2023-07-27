<?php

declare(strict_types=1);

namespace App\Domain\Notification\Twig\Extension;

use App\Domain\Documentation\Model\Document;
use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\Notification as NotificationModel;
use App\Domain\Registry\Dictionary\ProofTypeDictionary;
use App\Domain\Registry\Dictionary\ViolationNatureDictionary;
use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Violation;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NotificationExtension extends AbstractExtension
{
    protected TranslatorInterface $translator;
    protected RouterInterface $router;
    protected \App\Domain\Notification\Repository\Notification $repository;
    protected Security $security;

    protected string $requestDays;
    protected string $surveyDays;

    public function __construct(TranslatorInterface $translator, RouterInterface $router, Security $security, \App\Domain\Notification\Repository\Notification $repository, string $requestDays, string $surveyDays)
    {
        $this->translator  = $translator;
        $this->router      = $router;
        $this->security    = $security;
        $this->repository  = $repository;
        $this->requestDays = $requestDays;
        $this->surveyDays  = $surveyDays;
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
        $sentence = '<strong>[' . $this->translator->trans($notification->getModule()) . ']</strong>';

        if ($notification->getModule() === 'notification.modules.' . NotificationModel::MODULES[Document::class]) {
            $sentence .= ' Nouveau document déposé par le DPD&nbsp;';
        }

        switch ($notification->getAction()) {
            case 'notifications.actions.late_request':
            case 'notification.actions.late_request':
                $link = $this->getObjectLink($notification);
                $sentence .= $this->translator->trans('notifications.sentence.late_request', [
                    '%name%' => '<a href="' . $link . '">' . $notification->getName() . '</a> ',
                    '%days%' => $this->requestDays,
                ]) . ' ';
                break;
            case 'notifications.actions.late_survey':
            case 'notification.actions.late_survey':
                $sentence .= ' ' . $this->translator->trans('notifications.sentence.late_survey', [
                    '%days%' => $this->surveyDays,
                ]) . ' ';
                break;
            case 'notifications.actions.delete':
            case 'notification.actions.delete':
                $sentence .= $this->translator->trans($notification->getAction()) . ' ';
                $sentence .= ' : ' .
                    '<span>' . $notification->getName() . '</span> '
                ;
                break;
            default:
                if ($notification->getModule() !== 'notification.modules.' . NotificationModel::MODULES[Document::class]) {
                    $sentence .= $this->translator->trans($notification->getAction()) . ' ';
                }
                if ('notification.modules.action_plan' !== $notification->getModule()) {
                    $sentence .= '&nbsp;: ';
                }
                $link = $this->getObjectLink($notification);
                if ($this->repository->objectExists($notification)) {
                    $sentence .= '<a href="' . $link . '">' . $notification->getName() . '</a> '
                    ;
                } else {
                    $sentence .= '<span>' . $notification->getName() . '</span> '
                    ;
                }
        }

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
        if ('notification.modules.action_plan' === $notification->getModule()) {
            $sentence .= ' pour le <strong>' . (new \DateTime($notification->getObject()->planificationDate))->format('d/m/Y') . '</strong> est en retard ';
        }

        if ($notification->getCollectivity() && $this->security->getToken() && $this->security->isGranted('ROLE_REFERENT')) {
            $sentence .= $this->translator->trans('label.par') . ' <strong>' . $notification->getCollectivity()->getName() . '</strong>';
        }

        return $sentence;
    }

    public function getObjectLink(Notification $notification): string
    {
        try {
            if ('notification.modules.aipd' === $notification->getModule() && 'notification.actions.validation' === $notification->getAction()) {
                return $this->router->generate('aipd_analyse_impact_validation', ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            if ('notification.modules.aipd' === $notification->getModule() && 'notifications.actions.treatment_needs_aipd' === $notification->getAction()) {
                return $this->router->generate('registry_conformite_traitement_start_aipd', ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            if ('notification.modules.aipd' === $notification->getModule() && 'notification.actions.state_change' === $notification->getAction()) {
                return $this->router->generate('aipd_analyse_impact_evaluation', ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            if ('notification.modules.document' === $notification->getModule() && 'notification.actions.delete' !== $notification->getAction()) {
                return $notification->getObject()->url;
            }
            if ('notification.modules.action_plan' === $notification->getModule()) {
                return $this->router->generate('registry_mesurement_show', ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
            }

            return $this->router->generate($this->getRouteForModule($notification->getModule()), ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
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
            case 'notification.modules.protect_action':
                return 'registry_mesurement_show';
            case 'notification.modules.aipd':
                return 'aipd_analyse_impact_edit';
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
