<?php

namespace App\Domain\Notification\Command;

use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Registry\Model\Mesurement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotificationsGenerateCommand extends Command
{
    protected static $defaultName = 'notifications:generate';
    protected static $defaultDescription = 'Generate notifications that depend on elapsed time for the application';

    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
//        $arg1 = $input->getArgument('arg1');
//
//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }

        $this->generateLateActionNotifications();
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }

    protected function generateLateActionNotifications()
    {
        $actions = $this->entityManager->getRepository(Mesurement::class)->findAll();
        $now = new \DateTime();
        foreach ($actions as $action) {
            /**
             * @var Mesurement $action
             */


            if ($action->getPlanificationDate() && $action->getPlanificationDate() < $now) {
                $this->dispatcher->dispatch(new LateActionEvent($action));
            }
        }
    }
}
