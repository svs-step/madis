<?php

namespace App\Domain\Reporting\Controller;

use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Model\LogJournal as LogModel;
use App\Domain\Reporting\Repository\LogJournal;
use App\Domain\User\Model\User;
use Doctrine\Common\Persistence\Proxy;
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
        $orders     = $request->request->get('order');
        $columns    = $request->request->get('columns');

        $orderColumn = $this->getCorrespondingLabelFromkey($orders[0]['column']);
        $orderDir    = $orders[0]['dir'];

        /** @var Paginator $logs */
        $logs  = $this->logRepository->findPaginated($first, $maxResults, $orderColumn, $orderDir);
        $count = $this->logRepository->countLogs();

        $reponse = [
            'draw'            => $draw,
            'recordsTotal'    => $count,
            'recordsFiltered' => $count,
        ];

        /** @var LogModel $log */
        foreach ($logs as $log) {
            $reponse['data'][] = [
                'utilisateur'  => $log->getUser()->getFullName(),
                'collectivite' => $log->getCollectivity()->getName(),
                'date'         => date_format($log->getDate(), 'd-m-Y H:i:s'),
                'subject'      => LogJournalSubjectDictionary::getSubjectLabelFromSubjectType($log->getSubjectType()),
                'action'       => LogJournalActionDictionary::getActions()[$log->getAction()],
                'subjectId'    => $log->getLastKnownName(),
                'link'         => $this->getLinkForLog($log),
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

        $classname = \get_class($log->getSubject());
        $subject   = $log->getSubject();

        /* Sometimes doctrine retrieve a Proxy object instead of a true object for the subject for an unknown reason
           To avoid any issue we retrieve the classname of the parent if the subject is a proxy */
        if ($subject instanceof Proxy) {
            $reflect   = new \ReflectionClass($subject);
            $parent    = $reflect->getParentClass();
            $classname = $parent->getName();
        }

        switch ($classname) {
            case User::class:
                return $this->router->generate('user_user_edit', ['id' => $log->getSubject()->getId()]);
            case ConformiteTraitement::class:
                return $this->router->generate($log->getSubjectType() . '_edit', ['id' => $log->getSubject()->getId()]);
            default:
                return $this->router->generate($log->getSubjectType() . '_show', ['id' => $log->getSubject()->getId()]);
        }
    }

    private function getCorrespondingLabelFromkey(string $key)
    {
        $array = [
            '0' => 'utilisateur',
            '1' => 'collectivite',
            '2' => 'date',
            '3' => 'subject',
            '4' => 'action',
            '5' => 'subjectId',
            '6' => 'link',
        ];

        return \array_key_exists($key, $array) ? $array[$key] : null;
    }
}
