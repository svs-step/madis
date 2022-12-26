<?php

declare(strict_types=1);

namespace App\Tests\Functional\Domain\Notification;

use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Documentation\Repository\Document;
use App\Infrastructure\ORM\Notification\Repository\Notification;
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
}
