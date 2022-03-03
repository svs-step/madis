<?php

namespace App\Domain\Notification\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NotificationsSendCommand extends Command
{
    protected static $defaultName = 'notifications:send';
    protected static $defaultDescription = 'Send notifications by email to users that have requested it.';

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get all users
        // Foreach user that has email notifications enabled
        // check the date of the last email they were sent
        // Get all notifications generated after that date
        // Send them an email with those notifications

        return 0;
    }
}
