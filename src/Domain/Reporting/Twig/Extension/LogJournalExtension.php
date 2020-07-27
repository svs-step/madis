<?php

namespace App\Domain\Reporting\Twig\Extension;

use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Generator\LogJournalLinkGenerator;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\User\Model\User;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LogJournalExtension extends AbstractExtension
{
    /**
     * @var LogJournalLinkGenerator
     */
    private $logJournalLinkGenerator;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        LogJournalLinkGenerator $logJournalLinkGenerator,
        Security $security,
        RouterInterface $router
    ) {
        $this->logJournalLinkGenerator = $logJournalLinkGenerator;
        $this->security                = $security;
        $this->router                  = $router;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getLogJournalLink', [$this, 'getLogJournalLink']),
        ];
    }

    public function getLogJournalLink(LogJournal $logJournal): ?string
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $link = $this->logJournalLinkGenerator->getLink($logJournal);
        switch ($logJournal->getSubjectType()) {
            case LogJournalSubjectDictionary::USER_EMAIL:
            case LogJournalSubjectDictionary::USER_LASTNAME:
            case LogJournalSubjectDictionary::USER_FIRSTNAME:
            case LogJournalSubjectDictionary::USER_USER:
            case LogJournalSubjectDictionary::USER_PASSWORD:
            case LogJournalSubjectDictionary::USER_COLLECTIVITY:
                return null;
                break;
            case LogJournalSubjectDictionary::REGISTRY_CONFORMITE_TRAITEMENT:
                if (!$user->getCollectivity()->isHasModuleConformiteTraitement()) {
                    return null;
                }
                break;
            case LogJournalSubjectDictionary::REGISTRY_CONFORMITE_ORGANISATION_EVALUATION:
                if (!$user->getCollectivity()->isHasModuleConformiteOrganisation()) {
                    return null;
                }
                break;
        }

        return $link;
    }
}
