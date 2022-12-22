<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\Notification;

use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Documentation\Repository\Document;
use App\Infrastructure\ORM\Notification\Repository\Notification;
use App\Infrastructure\ORM\Registry\Repository\Treatment;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TreatmentNotificationGenerationTest extends WebTestCase
{
    use RecreateDatabaseTrait;

    public function testGenerateNotificationForNewTreatment()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser);
        $url = $client->getContainer()->get('router')->generate('registry_treatment_create', [], UrlGeneratorInterface::RELATIVE_PATH);

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('treatment');
        $client->request('POST', $url, [
            'treatment' => [
                'name'                      => 'nouveau traitement',
                'author'                    => 'processing_manager',
                'active'                    => '1',
                'legalBasis'                => 'consent',
                'concernedPeopleParticular' => [
                    'check'   => '1',
                    'comment' => 'comment',
                ],
                'delay'                     => [
                    'period' => 'day',
                    'number' => '1',
                ],
                '_token'                    => $csrfToken,
                // 'uploadedFile' => $uploadedFile,
            ],
        ]);

        $this->assertResponseRedirects('/traitements/liste');

        $treatmentRepository = static::getContainer()->get(Treatment::class);
        $t                   = $treatmentRepository->findOneOrNullLastUpdateByCollectivity($testUser->getCollectivity());

        $this->assertEquals('nouveau traitement', $t->getName());
        $notifRepository = static::getContainer()->get(Notification::class);
        $notif           = $notifRepository->findOneBy([
            'name'   => 'nouveau traitement',
            'module' => 'notification.modules.treatment',
            'action' => 'notification.actions.create',
        ]);

        $this->assertNotNull($notif);
        // Check that the notification was created and is not linked to any users (only for DPO)
        $this->assertEquals($t->getCollectivity(), $notif->getCollectivity());
        $this->assertEquals('nouveau traitement', $notif->getName());
        $this->assertCount(0, $notif->getNotificationUsers());
    }

    public function testGenerateNotificationForUpdatedTreatment()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser);
        $treatmentRepository = static::getContainer()->get(Treatment::class);
        $t                   = $treatmentRepository->findOneOrNullLastUpdateByCollectivity($testUser->getCollectivity());

        $url = $client->getContainer()->get('router')->generate('registry_treatment_edit', ['id' => $t->getId()->__toString()], UrlGeneratorInterface::RELATIVE_PATH);

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('treatment');

        $client->request('POST', $url, [
            'treatment' => [
                'name' => 'updated treatment',
                'active' => '1',
                '_token' => $csrfToken,
                'concernedPeoplePartner' => [
                    'check'   => '1',
                    'comment' => 'ttt',
                ],
                'author' => 'processing_manager',
            ],
        ]);

        $this->assertResponseRedirects('/traitements/liste');

        $t = $treatmentRepository->findOneById($t->getId()->__toString());
        $this->assertEquals('updated treatment', $t->getName());

        $notifRepository = static::getContainer()->get(Notification::class);
        $notif           = $notifRepository->findOneBy([
            'name'   => 'updated treatment',
            'module' => 'notification.modules.treatment',
            'action' => 'notification.actions.update',
        ]);

        $this->assertNotNull($notif);
        // Check that the notification was created and is not linked to any users (only for DPO)
        $this->assertEquals($t->getCollectivity(), $notif->getCollectivity());
        $this->assertEquals('updated treatment', $notif->getName());
        $this->assertCount(0, $notif->getNotificationUsers());
    }

    public function testGenerateNotificationForDeletedTreatment()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser);
        $treatmentRepository = static::getContainer()->get(Treatment::class);
        $t                   = $treatmentRepository->findOneOrNullLastUpdateByCollectivity($testUser->getCollectivity());

        $url = $client->getContainer()->get('router')->generate('registry_treatment_delete_confirm', ['id' => $t->getId()->__toString()], UrlGeneratorInterface::RELATIVE_PATH);

        $client->request('GET', $url);

        $this->assertResponseRedirects('/traitements/liste');

        $treatmentName         = $t->getName();
        $treatmentCollectivity = $t->getcollectivity();

        $t = $treatmentRepository->findOneById($t->getId()->__toString());

        $this->assertNull($t);

        $notifRepository = static::getContainer()->get(Notification::class);
        $notif           = $notifRepository->findOneBy([
            'module' => 'notification.modules.treatment',
            'action' => 'notification.actions.delete',
        ]);

        $this->assertNotNull($notif);
        // Check that the notification was created and is not linked to any users (only for DPO)
        $this->assertEquals($treatmentCollectivity, $notif->getCollectivity());
        $this->assertEquals($treatmentName, $notif->getName());
        $this->assertCount(0, $notif->getNotificationUsers());
    }
}
