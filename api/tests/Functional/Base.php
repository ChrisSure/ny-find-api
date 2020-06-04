<?php

namespace App\Tests\Functional;

use App\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class Base extends WebTestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Login a test user for secure routes
     *
     * @param string $role
     */
    protected function signIn(string $role): void
    {
        $session = $this->client->getContainer()->get('session');

        $documentManager = self::$container->get('doctrine');
        $user = $documentManager->getRepository(User::class)->findOneBy(['email' => 'admin@gmail.com']);
        $user->setRoles($role);

        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new PostAuthenticationGuardToken($user, $firewallName, $user->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

}