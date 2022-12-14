<?php

namespace App\DataFixtures\Default;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppFixtures extends Fixture implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUserName("test@example.com");
        $password = $this->container->get('security.password_hasher')->hashPassword($user, '$3CR3T');
        $user->setPassword($password);
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setIsVerified(true);

        $manager->persist($user);

        $user = new User();
        $user->setUserName("test@example2.com");
        $password = $this->container->get('security.password_hasher')->hashPassword($user, '$3CR3T');
        $user->setPassword($password);
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setIsVerified(false);

        $manager->persist($user);
        $manager->flush();
    }

    protected function getFixtures()
    {
        return  array(
            __DIR__ . '../fixtures/greenting.yaml',
        );
    }
}
