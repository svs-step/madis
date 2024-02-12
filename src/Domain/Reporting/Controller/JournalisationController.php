<?php

namespace App\Domain\Reporting\Controller;

use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Generator\LogJournalLinkGenerator;
use App\Domain\Reporting\Model\LogJournal as LogModel;
use App\Domain\Reporting\Repository\LogJournal;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class JournalisationController extends AbstractController
{
    use ServersideDatatablesTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LogJournalLinkGenerator
     */
    private $logJournalLinkGenerator;

    public function __construct(LogJournal $logRepository, TranslatorInterface $translator, RouterInterface $router, LogJournalLinkGenerator $logJournalLinkGenerator)
    {
        $this->translator              = $translator;
        $this->repository              = $logRepository;
        $this->router                  = $router;
        $this->logJournalLinkGenerator = $logJournalLinkGenerator;
    }

    public function indexAction()
    {
        return $this->render('Reporting/Journalisation/list.html.twig', [
            'totalItem' => $this->repository->count(),
            'route'     => $this->router->generate('reporting_journalisation_list_datatables'),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        /** @var Paginator $logs */
        $logs = $this->getResults($request);

        $reponse = $this->getBaseDataTablesResponse($request, $logs);

        /** @var LogModel $log */
        foreach ($logs as $log) {
            $reponse['data'][] = [
                'subjectId'    => $log->getSubjectId(),
                'userFullName' => $log->getUserFullName(),
                'userEmail'    => $log->getUserEmail(),
                'collectivite' => $log->getCollectivity()->getName(),
                'date'         => date_format($log->getDate(), 'd-m-Y H:i'),
                'subject'      => LogJournalSubjectDictionary::getSubjectLabelFromSubjectType($log->getSubjectType()),
                'action'       => LogJournalActionDictionary::getActions()[$log->getAction()],
                'subjectName'  => $log->getSubjectName(),
                'link'         => $this->generateLinkCellContent($log),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($reponse));

        return $jsonResponse;
    }

    private function generateLinkCellContent(LogModel $log)
    {
        if (LogJournalSubjectDictionary::ADMIN_DUPLICATION === $log->getSubjectType()) {
            return;
        }

        $content = $this->logJournalLinkGenerator->getLink($log);

        if (LogJournalLinkGenerator::DELETE_LABEL === $content) {
            return $content;
        }

        return '<a href="' . $content . '">' . $this->translator->trans('reporting.journalisation.action.check') . '</a>';
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            '0' => 'subjectId',
            '1' => 'userFullName',
            '2' => 'userEmail',
            '3' => 'collectivite',
            '4' => 'date',
            '5' => 'subject',
            '6' => 'action',
            '7' => 'subjectName',
            '8' => 'link',
        ];
    }
}
