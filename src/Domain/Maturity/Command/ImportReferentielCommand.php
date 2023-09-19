<?php

namespace App\Domain\Maturity\Command;

use App\Domain\Maturity\Model\Referentiel;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
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
        $finder->files()->in($input->getArgument('folder'));

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $this->importReferentiel($file->getRealPath());
            }
        }

        return Command::SUCCESS;
    }

    protected function importReferentiel(string $file): int
    {
        $this->io->title('Importing Référentiel from ' . $file);
        $content    = file_get_contents($file);
        $serializer = SerializerBuilder::create()->build();
        /** @var Referentiel $object */
        $object = $serializer->deserialize($content, Referentiel::class, 'xml');
        $object->deserialize();

        $existing = $this->entityManager->getRepository(Referentiel::class)->findBy(['name' => $object->getName()]);

        if (count($existing)) {
            $this->io->warning('Référentiel "' . $object->getName() . '" already exists');

            return 0;
        }
        $object->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($object);
        $this->entityManager->flush();

        $this->io->success('Référentiel "' . $object->getName() . '" imported');

        return 0;
    }
}
