<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\Notification;

use App\Domain\AIPD\Dictionary\ReponseAvisDictionary;
use App\Domain\AIPD\Dictionary\StatutAnalyseImpactDictionary;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\Notification\Model\NotificationUser;
use App\Domain\Registry\Dictionary\RequestCivilityDictionary;
use App\Domain\Registry\Dictionary\RequestStateDictionary;
use App\Domain\Registry\Model\Request;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\AIPD\Repository\AnalyseImpact as AnalyseImpactRepository;
use App\Infrastructure\ORM\Documentation\Repository\Document;
use App\Infrastructure\ORM\Notification\Repository\Notification;
use App\Infrastructure\ORM\Registry\Repository\Request as RequestRepository;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotificationGenerationTest extends WebTestCase
{
    use RecreateDatabaseTrait;

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

    public function testGenerateNotificationForRequestStepChange()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser);

        /**
         * @var RequestRepository $requestRepository
         */
        $requestRepository = $client->getContainer()->get(RequestRepository::class);
        $requests          = $requestRepository->findBy([
            'state' => RequestStateDictionary::STATE_TO_TREAT,
        ]);
        $this->assertNotEmpty($requests);
        /**
         * @var Request $request
         */
        $request = $requests[0];

        $this->assertNotNull($request);

        $url = $client->getContainer()->get('router')->generate('registry_request_edit', ['id' => $request->getId()], UrlGeneratorInterface::RELATIVE_PATH);

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('request');
        $client->request('POST', $url, [
            'request' => [
                'object'      => $request->getObject(),
                'otherObject' => $request->getOtherObject(),
                'applicant'   => [
                    'firstName'       => 'firstname',
                    'lastName'        => 'lastname',
                    'civility'        => RequestCivilityDictionary::CIVILITY_MISS,
                    'mail'            => 'test1@example.org',
                    'address'         => $request->getApplicant()->getAddress(),
                    'concernedPeople' => 1,
                ],
                'date'            => date('d/m/Y'),
                'concernedPeople' => [
                    'firstName' => 'first',
                    'lastName'  => 'last',
                    'civility'  => RequestCivilityDictionary::CIVILITY_MISS,
                    'mail'      => 'test@example.org',
                ],
                'stateRejectionReason' => 'N/A',
                'state'                => RequestStateDictionary::STATE_AWAITING_SERVICE,
                '_token'               => $csrfToken,
            ],
        ]);

        $this->assertResponseRedirects('/demandes-des-personnes-concernees/liste');

        $request = $requestRepository->findOneById($request->getId()->__toString());

        $this->assertNotNull($request);

        $this->assertEquals(RequestStateDictionary::STATE_AWAITING_SERVICE, $request->getState());

        // Check that a notification has been created for collectivity users
        $notifRepository = static::getContainer()->get(Notification::class);
        $notif           = $notifRepository->findOneBy([
            'name'   => $request->__toString(),
            'module' => 'notification.modules.request',
            'action' => 'notification.actions.state_change',
        ]);

        $this->assertEquals($request->getCollectivity(), $notif->getCollectivity());
        $this->assertEquals($request->__toString(), $notif->getName());

        $this->assertEquals(0, count($notif->getNotificationUsers()));
    }

    public function testGenerateNotificationForAIPDStatusChange()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser);

        /**
         * @var AnalyseImpactRepository $aipdRepository
         */
        $aipdRepository = $client->getContainer()->get(AnalyseImpactRepository::class);
        $aipds          = $aipdRepository->findAll();
        $this->assertNotEmpty($aipds);
        /**
         * @var AnalyseImpact $aipd
         */
        $aipd = $aipds[0];

        $url = $client->getContainer()->get('router')->generate('aipd_analyse_impact_validation', ['id' => $aipd->getId()], UrlGeneratorInterface::RELATIVE_PATH);

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('analyse_avis');

        $client->request('POST', $url, [
            'analyse_avis' => [
                'avisReferent' => [
                    'date'    => date('d/m/Y'),
                    'reponse' => ReponseAvisDictionary::REPONSE_FAVORABLE,
                    'detail'  => 'ok',
                ],
                'avisDpd' => [
                    'date'    => date('d/m/Y'),
                    'reponse' => ReponseAvisDictionary::REPONSE_FAVORABLE,
                    'detail'  => 'ok',
                ],
                'avisRepresentant' => [
                    'date'    => date('d/m/Y'),
                    'reponse' => ReponseAvisDictionary::REPONSE_FAVORABLE,
                    'detail'  => 'ok',
                ],
                'avisResponsable' => [
                    'date'    => date('d/m/Y'),
                    'reponse' => ReponseAvisDictionary::REPONSE_FAVORABLE,
                    'detail'  => 'ok',
                ],
                '_token' => $csrfToken,
                // 'uploadedFile' => $uploadedFile,
            ],
        ]);

        $this->assertResponseRedirects('/analyse-impact/liste');

        $aipd = $aipdRepository->findOneById($aipd->getId()->__toString());

        $this->assertNotNull($aipd);

        $this->assertEquals(StatutAnalyseImpactDictionary::FAVORABLE, $aipd->getStatut());

        // Check that a notification has been created for collectivity users
        $notifRepository = static::getContainer()->get(Notification::class);
        $notif           = $notifRepository->findOneBy([
            'name'   => $aipd->__toString(),
            'module' => 'notification.modules.aipd',
            'action' => 'notification.actions.state_change',
        ]);

        $this->assertNotNull($notif);

        $this->assertEquals($aipd->__toString(), $notif->getName());

        $this->assertEquals(2, count($notif->getNotificationUsers()));

        foreach ($notif->getNotificationUsers() as $nu) {
            /*
             * @var NotificationUser $nu
             */
            $this->assertEquals(false, $nu->getActive());
            $this->assertEquals(false, $nu->getSent());
            $this->assertEquals($nu->getMail(), $nu->getUser()->getEmail());
            $this->assertTrue(in_array('ROLE_ADMIN', $nu->getUser()->getRoles()) || in_array('ROLE_REFERENT', $nu->getUser()->getRoles()));
        }
    }

    public function testGenerateNotificationForAIPDValidation()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser);

        /**
         * @var AnalyseImpactRepository $aipdRepository
         */
        $aipdRepository = $client->getContainer()->get(AnalyseImpactRepository::class);
        $aipds          = $aipdRepository->findAll();
        $this->assertNotEmpty($aipds);
        /**
         * @var AnalyseImpact $aipd
         */
        $aipd = $aipds[0];

        $url = $client->getContainer()->get('router')->generate('aipd_analyse_impact_validation', ['id' => $aipd->getId()], UrlGeneratorInterface::RELATIVE_PATH);

        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();

        /**
         * @var AnalyseImpact $aipd
         */
        $aipd = $aipdRepository->findOneById($aipd->getId()->__toString());

        $this->assertNotNull($aipd);

        $this->assertEquals(true, $aipd->isReadyForValidation());

        // Check that a notification has been created for collectivity users
        $notifRepository = static::getContainer()->get(Notification::class);
        $notifs          = $notifRepository->findBy([
            'name'   => $aipd->__toString(),
            'module' => 'notification.modules.aipd',
            'action' => 'notification.actions.validation',
        ]);

        $this->assertNotNull($notifs);

        $this->assertCount(2, $notifs);

        $notif = $notifs[0];

        $this->assertEquals($aipd->__toString(), $notif->getName());

        $this->assertEquals(2, count($notif->getNotificationUsers()));

        foreach ($notif->getNotificationUsers() as $nu) {
            /*
             * @var NotificationUser $nu
             */
            $this->assertEquals(false, $nu->getActive());
            $this->assertEquals(false, $nu->getSent());
            $this->assertEquals($nu->getMail(), $nu->getUser()->getEmail());
            $this->assertTrue(in_array('ROLE_ADMIN', $nu->getUser()->getRoles()) || in_array('ROLE_REFERENT', $nu->getUser()->getRoles()));
        }

        $notif = $notifs[1];

        $this->assertEquals($aipd->__toString(), $notif->getName());

        $this->assertEquals(4, count($notif->getNotificationUsers()));

        foreach ($notif->getNotificationUsers() as $nu) {
            /*
             * @var NotificationUser $nu
             */
            $this->assertEquals(true, $nu->getActive());
            $this->assertEquals(false, $nu->getSent());
            $this->assertNull($nu->getMail());
        }
    }
}
