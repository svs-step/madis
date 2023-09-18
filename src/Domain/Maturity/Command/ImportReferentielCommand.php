<?php

namespace App\Domain\Maturity\Command;

use App\Domain\AIPD\Model\ModeleAnalyse;
use App\Domain\AIPD\Model\ModeleScenarioMenace;
use App\Domain\Maturity\Model\Referentiel;
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
use Symfony\Component\Finder\Finder;

class ImportReferentielCommand extends Command
{
    protected static $defaultName        = 'maturity:referentiel:import';
    protected static $defaultDescription = 'Import a maturity referentiel';

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
            ->addArgument('folder', InputArgument::REQUIRED, 'Path to the folder containing files to import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $finder = new Finder();
        $finder->files()->in( $input->getArgument('folder'));

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $this->importReferentiel($file->getRealPath());
            }
        }


        return Command::SUCCESS;
    }

    protected function importReferentiel(string $file): int
    {
        $this->io->title('Importing Référentiel from '.$file);
        $content    = file_get_contents($file);
        $serializer = SerializerBuilder::create()->build();
        /** @var Referentiel $object */
        $object = $serializer->deserialize($content, Referentiel::class, 'xml');
        $object->deserialize();

        $existing = $this->entityManager->getRepository(Referentiel::class)->findBy(['name' => $object->getName()]);

        if (count($existing)) {
            $this->io->warning('Référentiel "'.$object->getName(). '" already exists');
            return 0;
        }
        $object->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($object);
        $this->entityManager->flush();

        $this->io->success('Référentiel "'.$object->getName(). '" imported');

        return 0;
    }
}
