<?php

namespace App\Domain\Reporting\Controller;

use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Model\LogJournal as LogModel;
use App\Domain\Reporting\Repository\LogJournal;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class JournalisationController extends AbstractController
{
    /**
     * @var LogJournal
     */
    private $logRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(LogJournal $logRepository, RouterInterface $router)
    {
        $this->logRepository = $logRepository;
        $this->router        = $router;
    }

    public function indexAction()
    {
        return $this->render('Reporting/Journalisation/list.html.twig', [
            'totalLogs' => $this->logRepository->countLogs(),
            'route'     => $this->router->generate('reporting_journalisation_list_datatables'),
        ]);
    }

    public function listDataTables(Request $request)
    {
        $draw       = $request->request->get('draw');
        $first      = $request->request->get('start');
        $maxResults = $request->request->get('length');

        /** @var Paginator $logs */
        $logs  = $this->logRepository->findPaginated($first, $maxResults);
        $count = $this->logRepository->countLogs();

        $reponse = [
            'draw'            => $draw,
            'recordsTotal'    => $count,
            'recordsFiltered' => $count,
        ];

        /** @var LogModel $log */
        foreach ($logs as $log) {
            $reponse['data'][] = [
                '0' => $log->getUser()->getFullName(),
                '1' => $log->getCollectivity()->getName(),
                '2' => date_format($log->getDate(), 'd-m-Y H:i:s'),
                '3' => LogJournalSubjectDictionary::getSubjectLabelFromSubjectType($log->getSubjectType()),
                '4' => LogJournalActionDictionary::getActions()[$log->getAction()],
                '5' => $log->getLastKnownName(),
                '6' => $this->getLinkForLog($log),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($reponse));

        return $jsonResponse;
    }

    private function getLinkForLog(LogModel $log)
    {
        if (null === $log->getSubject()) {
            return 'Supprimé';
        }
        switch ($log->getSubjectType()) {
            case LogJournalSubjectDictionary::USER_USER:
            case LogJournalSubjectDictionary::REGISTRY_CONFORMITE_TRAITEMENT:
                return $this->router->generate($log->getSubjectType() . '_edit', ['id' => $log->getSubject()->getId()]);
            default:
                return $this->router->generate($log->getSubjectType() . '_show', ['id' => $log->getSubject()->getId()]);
        }
    }
}
