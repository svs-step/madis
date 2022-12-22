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

class NotificationGenerationTest extends WebTestCase
{
    use RecreateDatabaseTrait;

    public function testDenyAccessToConformiteTraitementForLecteurUsersOnCollectivityWithNoConformiteModule2()
    {
        $client = static::createClient();
        self::populateDatabase();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('lecteur@awkan.fr');

        $client->loginUser($testUser);
        $url = $client->getContainer()->get('router')->generate('registry_conformite_traitement_list', [], UrlGeneratorInterface::RELATIVE_PATH);

        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testGenerateNotificationForNewDocument()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser);
        $url = $client->getContainer()->get('router')->generate('documentation_document_create', [], UrlGeneratorInterface::RELATIVE_PATH);

        $uploadedFile = new UploadedFile(
            __DIR__ . '/doc.pdf',
            'doc.pdf'
        );

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('document');
        $client->request('POST', $url, [
            'document' => [
                'name'   => 'Document',
                'isLink' => '0',
                '_token' => $csrfToken,
                // 'uploadedFile' => $uploadedFile,
            ],
        ], [
            'document' => [
                'uploadedFile' => $uploadedFile,
            ],
        ]);

        $this->assertResponseRedirects('/espace-documentaire/');

        $docRepository = static::getContainer()->get(Document::class);
        $doc           = $docRepository->findOneBy(['name' => 'Document']);

        $this->assertNotNull($doc);

        $this->assertEquals('Document', $doc->getName());

        // Check that a notification has been created for collectivity users
        $notifRepository = static::getContainer()->get(Notification::class);
        $notif           = $notifRepository->findOneBy([
            'name'   => 'Document',
            'module' => 'notification.modules.document',
            'action' => 'notification.actions.create',
        ]);

        $this->assertEquals(null, $notif->getCollectivity());
        $this->assertEquals('Document', $notif->getName());

        $nonDpoUsers = $userRepository->findNonDpoUsers();

        $this->assertEquals(count($nonDpoUsers), count($notif->getNotificationUsers()));

        // TODO test that an email gets sent to the "référent opérationnel"
    }

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

        $classMetadataFactory = new ClassMetadataFactory(new YamlFileLoader(self::$kernel->getProjectDir() . '/config/api_platform/serialization/treatment.yaml'));
        $normalizer           = new ObjectNormalizer($classMetadataFactory);
        $normalizer2          = new GetSetMethodNormalizer($classMetadataFactory);
        $serializer           = new Serializer([$normalizer2, $normalizer], ['json' => new JsonEncoder()]);

        $at = $serializer->normalize($t, null, ['groups' => 'treatment_read']);

        $at['name']                   = 'updated treatment';
        $at['concernedPeoplePartner'] = [
            'check'   => '1',
            'comment' => 'ttt',
        ];
        $at['_token'] = $csrfToken;
        $at['author'] = 'processing_manager';
        $at['active'] = $at['active'] ? '1' : '0';
        unset($at['completion']);
        unset($at['collectivity']);
        unset($at['template']);
        unset($at['templateIdentifier']);
        unset($at['clonedFrom']);
        unset($at['conformiteTraitement']);
        unset($at['collectingMethod']);
        unset($at['contractors']);
        unset($at['dataCategories']);

        $client->request('POST', $url, [
            'treatment' => $at,
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
