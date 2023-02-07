<?php

namespace App\Domain\Notification\Command;

use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\NotificationUser;
use App\Domain\User\Model\EmailNotificationPreference;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Notification\Repository\NotificationUser as NotificationUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationsSendCommand extends Command
{
    protected static $defaultName        = 'notifications:send';
    protected static $defaultDescription = 'Send notifications by email to users that have requested it.';

    protected UserRepository $userRepository;
    protected NotificationUserRepository $notificationUserRepository;

    protected MailerInterface $mailer;
    protected TranslatorInterface $translator;

    public function __construct(
        UserRepository $userRepository,
        NotificationUserRepository $notificationUserRepository,
        MailerInterface $mailer,
        TranslatorInterface $translator,
        string $name = null
    ) {
        parent::__construct($name);
        $this->userRepository             = $userRepository;
        $this->mailer                     = $mailer;
        $this->notificationUserRepository = $notificationUserRepository;
        $this->translator                 = $translator;
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

        // Get all users
        $users = new ArrayCollection($this->userRepository->findAll());
        // Foreach user that has email notifications enabled

        // Get all user notifications that have not been sent yet

        $qb = $this->notificationUserRepository->getQueryBuilder();
        $qb->where('o.active = 0');
        $qb->andWhere('o.sent = 0');
        $qb->andWhere('o.mail IS NOT NULL');

        $notificationUsers = $qb->getQuery()->getResult();

        $output->writeln(count($notificationUsers) . ' notifications to send');

        $groupedByMail = [];

        foreach ($notificationUsers as $notificationUser) {
            if (!isset($groupedByMail[$notificationUser->getMail()])) {
                $groupedByMail[$notificationUser->getMail()] = [];
            }
            $groupedByMail[$notificationUser->getMail()][] = $notificationUser;
        }

        foreach ($groupedByMail as $mail => $notifUsers) {
            $notifications = [];
            /**
             * @var User $user
             */
            $user = $notifUsers[0]->getUser();

            $output->writeln('Prepare notifications for ' . $mail);

            // If not a registered user, everything will be sent by email
            $prefs = new EmailNotificationPreference();
            $prefs->setFrequency(EmailNotificationPreference::FREQUENCY_EACH);
            $prefs->setEnabled(true);
            $prefs->setLastSent(new \DateTime('2012-01-01 00:00:00'));
            $prefs->setNotificationMask(0x7FF);

            if ($user && $user->getEmailNotificationPreference()) {
                // if a registered user, respect their preferences.
                $prefs = $user->getEmailNotificationPreference();
            }

            if (
                !$prefs ||
                !$prefs->getEnabled() ||
                EmailNotificationPreference::FREQUENCY_NONE == $prefs->getFrequency() ||
                0 === $prefs->getNotificationMask()
            ) {
                // Exit if user has email notifications disabled
                $output->writeln('User ' . $mail . ' has disabled notifications');
                $output->writeln(json_encode($prefs));
                continue;
            }

            $nextTimeToSend = $this->getNextTimeToSendFromPreferences($prefs);

            if ($nextTimeToSend->format('Ymdhi') !== date('Ymdhi')) {
                $output->writeln('Not the time to send. Programmed time is ' . $nextTimeToSend->format('Ymdhi') . 'a dnc urrent time is ' . date('Ymdhi'));
                // Now is not the time to send for this user, abort
                continue;
            }

            $lastSent = $prefs->getLastSent();
            foreach ($notifUsers as $nu) {
                /*
                 * @var NotificationUser $notifUser
                 */

                if ($nu->getCreatedAt() > $lastSent) {
                    /**
                     * @var Notification $notif
                     */
                    $notif = $nu->getNotification();
                    // Check if the notification module is is the user preferences mask

                    // Get the raw name of the module
                    $mod = str_replace('notification.modules.', '', $notif->getModule());

                    $availableModules = array_keys(EmailNotificationPreference::MODULES);
                    if (in_array($mod, $availableModules) && (EmailNotificationPreference::MODULES[$mod] & $prefs->getNotificationMask())) {
                        // Current notification module is enabled in user preferences
                        $notifications[] = $nu->getNotification();
                    } else {
                        $output->writeln('User ' . $mail . ' does not have notifications active for module ' . $mod);
                    }
                }
            }

            if (0 === count($notifications)) {
                $output->writeln('No notifications to send to ' . $mail);
                continue;
            }

            $email = new TemplatedEmail();
            $email->subject($this->translator->trans('notification.email.subject'));
            $email->to(new Address($mail));
            $email->htmlTemplate('Notification/Mail/notifications.html.twig');
            $email->context([
                'notifications' => $notifications,
                'user'          => $user,
            ]);
            try {
                $this->mailer->send($email);

                if ($user) {
                    // $prefs->setLastSent((new \DateTime()));
                    // $user->setEmailNotificationPreference($prefs);
                    $this->userRepository->update($user);
                }

                foreach ($notifUsers as $nu) {
                    /*
                     * @var NotificationUser $notifUser
                     */
                    if ($nu->getCreatedAt() > $lastSent) {
                        $nu->setSent(true);
                        $this->notificationUserRepository->update($nu);
                    }
                }

                $output->writeln('Notifications email sent to ' . $mail);
            } catch (\Exception $e) {
                dump($e);
                $output->writeln('Could not send email to ' . $mail);
            }
        }

        // check the date of the last email they were sent
        // Get all notifications generated after that date
        // Send them an email with those notifications

        return 0;
    }

    private function getNextTimeToSendFromPreferences(EmailNotificationPreference $prefs): \DateTime
    {
        switch ($prefs->getFrequency()) {
            case EmailNotificationPreference::FREQUENCY_EACH:
                return new \DateTime();
            case EmailNotificationPreference::FREQUENCY_HOUR:
                $lastSent = $prefs->getLastSent();

                return $lastSent->add(new \DateInterval('PT' . $prefs->getHour() . 'H'));
            case EmailNotificationPreference::FREQUENCY_DAY:
                // set cutoff date to today at the specified time
                return (new \DateTime())->setTime($prefs->getHour(), 0);
            case EmailNotificationPreference::FREQUENCY_WEEK:
                $now = new \DateTime();
                // set cutoff date to this week at the specified day and time
                return (new \DateTime())->setTime($prefs->getHour(), 0)->setISODate($now->format('Y'), $now->format('W'), $prefs->getDay());
            case EmailNotificationPreference::FREQUENCY_MONTH:
                // set cutoff date to this months selected day and week
                return (new \DateTime())->modify($this->getEnglishNumber($prefs->getWeek()) . ' ' . $this->getEnglishDayFromNumber($prefs->getDay()) . ' of this month')->setTime($prefs->getHour(), 0);
        }

        return new \DateTime();
    }

    private function getEnglishDayFromNumber(int $day): string
    {
        return match ($day) {
            default => 'monday',
            2       => 'tuesday',
            3       => 'wednesday',
            4       => 'thursday',
            5       => 'friday',
            6       => 'saturday',
            7       => 'sunday',
        };
    }

    private function getEnglishNumber(int $num): string
    {
        return match ($num) {
            default => 'first',
            2       => 'second',
            3       => 'third',
            4       => 'fourth',
        };
    }
}
