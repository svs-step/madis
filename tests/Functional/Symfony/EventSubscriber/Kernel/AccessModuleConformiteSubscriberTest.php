<?php

declare(strict_types=1);

namespace App\Tests\Functional\Symfony\EventSubscriber\Kernel;

use App\Application\Symfony\EventSubscriber\Kernel\AccessModuleConformiteSubscriber;
use App\Domain\Registry\Controller\ConformiteTraitementController;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\User as UserRepository;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class AccessModuleConformiteSubscriberTest extends WebTestCase
{
    use RecreateDatabaseTrait;

    public function testDenyAccessToConformiteTraitementForNonAdminUsers()
    {
        $client = static::createClient();

        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneOrNullByEmail('collectivite@awkan.fr');

        dd($userRepository->findAll());

        $client->loginUser($testUser);
        $url = $client->getContainer()->get('router')->generate('registry_conformite_traitement_list', array(), false);
        // user is now logged in, so you can test protected resources
        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello Username!');
    }

}
