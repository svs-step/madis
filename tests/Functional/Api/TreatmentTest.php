<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Domain\User\Repository\User as UserRepository;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

class TreatmentTest extends ApiTestCase
{
    use RecreateDatabaseTrait;

    public function testApiGetTreatments()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser, 'api');

        $url = '/api/treatments';

        $res = $client->request('GET', $url);

        $data = $res->toArray();

        $this->assertResponseIsSuccessful();

        $this->assertCount(30, $data['hydra:member']);
        $this->assertEquals(40, $data['hydra:totalItems']);

        $this->assertNotEmpty(array_keys($data['hydra:member'][0]));
    }
}
