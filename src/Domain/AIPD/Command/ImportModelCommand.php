<?php

namespace App\Domain\AIPD\Command;

use App\Domain\AIPD\Model\ModeleAnalyse;
use App\Domain\AIPD\Model\ModeleScenarioMenace;
use App\Domain\Maturity\Repository\Survey as SurveyRepository;
use App\Domain\Notification\Event\ConformiteTraitementNeedsAIPDEvent;
use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\LateRequestEvent;
use App\Domain\Notification\Event\LateSurveyEvent;
use App\Domain\Notification\Event\NoLoginEvent;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\TreatmentDataCategory;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Registry\Repository\ConformiteTraitement\ConformiteTraitement as ConformiteTraitementRepository;
use App\Infrastructure\ORM\Registry\Repository\Mesurement as MesurementRepository;
use App\Infrastructure\ORM\Registry\Repository\Request as RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportModelCommand extends Command
{
    protected static $defaultName        = 'aipd:model:import';
    protected static $defaultDescription = 'Import an AIPD model';

    private SymfonyStyle $io;
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the file to import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->importAIPDModel($input->getArgument('file'));

        return Command::SUCCESS;
    }

    protected function importAIPDModel(string $file): int
    {
        $this->io->title('Importing AIPD model from '.$file);
        $content    = file_get_contents($file);
        $serializer = SerializerBuilder::create()->build();
        /** @var ModeleAnalyse $object */
        $object = $serializer->deserialize($content, ModeleAnalyse::class, 'xml');
        $object->deserialize();

        $existing = $this->entityManager->getRepository(ModeleAnalyse::class)->findBy(['nom' => $object->getNom(), 'description' => $object->getDescription()]);

        if (count($existing)) {
            $this->io->warning('AIPD model "'.$object->getNom(). '" already exists');
            return 0;
        }

        $sm = [];
        foreach ($object->getScenarioMenaces() as $scenarioMenace) {
            $this->io->writeln('Importing scenario menace '.$scenarioMenace->getNom());
            /** @var ModeleScenarioMenace $scenarioMenace */
            $mesures = [];
            foreach ($scenarioMenace->getMesuresProtections() as $mesureProtection) {
                $this->io->writeln('Importing $mesureProtection '.$mesureProtection->getNom());
                // Check if this mesure already exists
                $mm = $this->entityManager->find(\App\Domain\AIPD\Model\ModeleMesureProtection::class, $mesureProtection->getId());
                if ($mm) {
                    $mesures[] = $mm;
                } else {
                    // If not, save it now
                    $this->entityManager->persist($mesureProtection);
                    $mesures[] = $mesureProtection;
                }
            }
            $scenarioMenace->setMesuresProtections($mesures);
            $sm[] = $scenarioMenace;
        }

        $object->setScenarioMenaces($sm);
        $object->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($object);
        $this->entityManager->flush();

        $this->io->success('AIPD model imported');

        return 0;
    }
}
