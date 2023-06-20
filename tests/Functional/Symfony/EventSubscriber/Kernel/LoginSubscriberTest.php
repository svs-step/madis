<?php

declare(strict_types=1);

namespace App\Tests\Functional\Symfony\EventSubscriber\Kernel;

use App\Domain\Reporting\Model\LogJournal;
use App\Domain\Reporting\Repository\LogJournal as LogJournalRepository;
use App\Domain\User\Repository\User as UserRepository;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginSubscriberTest extends WebTestCase
{
    use RecreateDatabaseTrait;

    public function testLoginWithNoExistingUserRedirects()
    {
        $client = static::createClient();
        self::populateDatabase();

        $ljr = static::getContainer()->get(LogJournalRepository::class);

        $logs = $ljr->findAll();

        $this->assertEquals(0, count($logs));

        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $email          = 'test@example.org';

        $url = $client->getContainer()->get('router')->generate('login', [], UrlGeneratorInterface::RELATIVE_PATH);
        // user is now logged in, so you can test protected resources
        $client->request('POST', $url, ['_username' => $email, '_password' => '']);

        $this->assertResponseRedirects($client->getContainer()->get('router')->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $logs = $ljr->findAll();

        $this->assertEquals(0, count($logs));
    }

    public function testLoginWithWrongPasswordRedirects()
    {
        $client = static::createClient();
        self::populateDatabase();

        $ljr = static::getContainer()->get(LogJournalRepository::class);

        $logs = $ljr->findAll();

        $this->assertEquals(0, count($logs));

        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('lecteur@awkan.fr');
        $email          = $testUser->getEmail();

        $url = $client->getContainer()->get('router')->generate('login', [], UrlGeneratorInterface::RELATIVE_PATH);
        // user is now logged in, so you can test protected resources
        $client->request('POST', $url, ['_username' => $email, '_password' => '123456']);

        $this->assertResponseRedirects($client->getContainer()->get('router')->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $logs = $ljr->findAll();

        $this->assertEquals(0, count($logs));
    }

    public function testLoginWithExistingUserSuccess()
    {
        $client = static::createClient();
        self::populateDatabase();

        $ljr            = static::getContainer()->get(LogJournalRepository::class);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('lecteur@awkan.fr');

        $logs = $ljr->findAll();

        $this->assertEquals(0, count($logs));

        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $email          = $testUser->getEmail();

        $url = $client->getContainer()->get('router')->generate('login', [], UrlGeneratorInterface::RELATIVE_PATH);
        // user is now logged in, so you can test protected resources
        $client->request('POST', $url, ['_username' => $email, '_password' => '111111']);

        $this->assertResponseRedirects($client->getContainer()->get('router')->generate('reporting_dashboard_index', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $logs = $ljr->findAll();

        $this->assertEquals(1, count($logs));
        /**
         * @var LogJournal $log
         */
        $log = $logs[0];
        $this->assertEquals('login', $log->getAction());
        $this->assertEquals('user_user', $log->getSubjectType());
        $this->assertEquals($testUser->getFullName(), $log->getUserFullName());
        $this->assertEquals($testUser->getEmail(), $log->getUserEmail());
        $this->assertEquals($testUser->getId(), $log->getSubjectId());
        $this->assertEquals($testUser->getCollectivity(), $log->getCollectivity());
    }
}
