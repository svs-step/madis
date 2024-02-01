<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

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
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class CollectivityTest extends ApiTestCase
{
    use RecreateDatabaseTrait;

    public function testApiGetCollectivities()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser, 'api');

        $url = '/api/collectivities';

        $res = $client->request('GET', $url);

        $data = $res->toArray();

        $this->assertResponseIsSuccessful();

        $this->assertCount(3, $data['hydra:member']);
        $this->assertEquals(3, $data['hydra:totalItems']);

        $keys =  [
            "@id",
            "@type",
            "name",
            "type",
            "siren",
            "active",
            "address",
            "legalManager",
            "referent",
            "finessGeo",
        ];

        $this->assertEquals($keys, array_keys($data['hydra:member'][0]));
    }

}
