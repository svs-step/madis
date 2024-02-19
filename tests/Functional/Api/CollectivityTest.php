<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Domain\User\Repository\User as UserRepository;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

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

        $this->assertContains('@id', array_keys($data['hydra:member'][0]));
        $this->assertContains('@type', array_keys($data['hydra:member'][0]));
        $this->assertContains('name', array_keys($data['hydra:member'][0]));
        $this->assertContains('type', array_keys($data['hydra:member'][0]));
    }
}
