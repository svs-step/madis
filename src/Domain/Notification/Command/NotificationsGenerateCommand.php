<?php

namespace App\Domain\Notification\Command;

use App\Domain\Maturity\Repository\Survey as SurveyRepository;
use App\Domain\Notification\Event\ConformiteTraitementNeedsAIPDEvent;
use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\LateRequestEvent;
use App\Domain\Notification\Event\LateSurveyEvent;
use App\Domain\Notification\Event\NoLoginEvent;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Registry\Repository\ConformiteTraitement\ConformiteTraitement as ConformiteTraitementRepository;
use App\Infrastructure\ORM\Registry\Repository\Mesurement as MesurementRepository;
use App\Infrastructure\ORM\Registry\Repository\Request as RequestRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotificationsGenerateCommand extends Command
{
    protected static $defaultName        = 'notifications:generate';
    protected static $defaultDescription = 'Generate notifications that depend on elapsed time for the application';

    private EventDispatcherInterface $dispatcher;
    private MesurementRepository $mesurementRepository;
    private RequestRepository $requestRepository;
    private UserRepository $userRepository;
    private SurveyRepository $surveyRepository;
    private ConformiteTraitementRepository $conformiteTraitementRepository;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        MesurementRepository $mesurementRepository,
        RequestRepository $requestRepository,
        UserRepository $userRepository,
        SurveyRepository $surveyRepository,
        ConformiteTraitementRepository $conformiteTraitementRepository
    ) {
        $this->dispatcher                     = $dispatcher;
        $this->mesurementRepository           = $mesurementRepository;
        $this->requestRepository              = $requestRepository;
        $this->userRepository                 = $userRepository;
        $this->surveyRepository               = $surveyRepository;
        $this->conformiteTraitementRepository = $conformiteTraitementRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $cnt = $this->generateNoLoginNotifications();
        $io->success($cnt . ' inactive users notifications generated');
        $cnt = $this->generateLateActionNotifications();
        $io->success($cnt . ' late actions notifications generated');

        $cnt = $this->generateLateRequestNotification();
        $io->success($cnt . ' late requests notifications generated');

        $cnt = $this->generateLateSurveyNotification();
        $io->success($cnt . ' late survey notifications generated');

        return 0;
    }

    protected function generateLateActionNotifications(): int
    {
        $actions = $this->mesurementRepository->findAll();
        $now     = new \DateTime();
        $cnt     = 0;
        foreach ($actions as $action) {
            /**
             * @var Mesurement $action
             */
            if ($action->getPlanificationDate() && $action->getPlanificationDate() < $now) {
                $this->dispatcher->dispatch(new LateActionEvent($action));
                ++$cnt;
            }
        }

        return $cnt;
    }

    protected function generateLateRequestNotification(): int
    {
        $cnt      = 0;
        $requests = $this->requestRepository->findAllLate();
        foreach ($requests as $request) {
            $this->dispatcher->dispatch(new LateRequestEvent($request));
            ++$cnt;
        }

        return $cnt;
    }

    protected function generateLateSurveyNotification(): int
    {
        $cnt = 0;
        // Find all late surveys
        $surveys = $this->surveyRepository->findAllLate();

        foreach ($surveys as $survey) {
            $this->dispatcher->dispatch(new LateSurveyEvent($survey));
            ++$cnt;
        }

        return $cnt;
    }

    protected function generateNoLoginNotifications(): int
    {
        // Get users that have last login null and created_at more than 6 months ago
        $users = $this->userRepository->findAllNoLogin();
        $cnt   = 0;
        foreach ($users as $user) {
            ++$cnt;
            $this->dispatcher->dispatch(new NoLoginEvent($user));
        }

        return $cnt;
    }

    protected function generateTreatmentNeedsAIPDNotification(): int
    {
        // Get users that have last login null and created_at more than 6 months ago
        $cfs = $this->conformiteTraitementRepository->findAll();
        $cnt = 0;
        foreach ($cfs as $conformite) {
            /*
             * @var ConformiteTraitement $conformite
             */
            ++$cnt;
            $this->dispatcher->dispatch(new ConformiteTraitementNeedsAIPDEvent($conformite));
        }

        return $cnt;
    }

    private function getPlanifiedMesurements(ConformiteTraitement $conformiteTraitement): array
    {
        $planifiedMesurementsToBeNotified = [];
        foreach ($conformiteTraitement->getReponses() as $reponse) {
            $mesurements = \iterable_to_array($reponse->getActionProtectionsPlanifiedNotSeens());
            foreach ($mesurements as $mesurement) {
                if (!\in_array($mesurement, $planifiedMesurementsToBeNotified)) {
                    \array_push($planifiedMesurementsToBeNotified, $mesurement);
                }
            }
        }

        return $planifiedMesurementsToBeNotified;
    }
}
