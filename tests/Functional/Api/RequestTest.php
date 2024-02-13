<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Registry\Repository\Request as RequestRepository;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

class RequestTest extends ApiTestCase
{
    use RecreateDatabaseTrait;

    public function testApiGetRequests()
    {
        $client = static::createClient();
        self::populateDatabase();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');
        $client->loginUser($testUser, 'api');

        $url = '/api/requests';

        $res = $client->request('GET', $url);

        $data = $res->toArray();

        $this->assertResponseIsSuccessful();

        $this->assertCount(20, $data['hydra:member']);
        $this->assertEquals(20, $data['hydra:totalItems']);

        $requestRepository = static::getContainer()->get(RequestRepository::class);
        $request           = $requestRepository->findOneById(str_replace('/api/requests/', '', $data['hydra:member'][0]['@id']));

        $ser = static::getContainer()->get('serializer');

        $req = $ser->serialize($request, 'jsonld');

        $d = json_decode($req, true);

        unset($d['@context']);

        foreach ($d as $k => $v) {
            if (null === $v) {
                unset($d[$k]);
            }
        }

        $this->assertEquals(array_keys($d), array_keys($data['hydra:member'][0]));
    }
}
