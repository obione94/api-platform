<?php

namespace App\Tests\Tools;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\Default\AppFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractApiTestCase extends ApiTestCase
{

    /** @var EntityManager */
    protected $em;

    /**
     * This method is called before each test.
     */
    public function setUp(): void
    {
        $container = self::getContainer();
        $this->em = $container->get('doctrine.orm.entity_manager');
        $fs = new Filesystem();
        $dirFileCacheTest = $container->getParameter('kernel.cache_dir');
        $source = $dirFileCacheTest . '/original_test.db';
        if ($fs->exists($source)) {
            // copy sqlite database
            $fs->copy($source, $dirFileCacheTest . '/app_test.db',true);
            $this->em->clear();
            ($this->em
                ->getConnection()
                ->getNativeConnection()
            )->sqliteCreateFunction('f_unaccent', fn($str) => $str, 1);

            // loading fixtures
            $loader = new Loader();
            $this->addFixtures($loader);

            $executor = new ORMExecutor($this->em,(new ORMPurger()));
            $executor->execute($loader->getFixtures(), true);
        } else {
            throw new \Exception('Origin test.db file does not exist : '.$source);
        }
    }

    protected function addFixtures(Loader $loader): Loader
    {
        $default = new AppFixtures();
        $default->setContainer(self::getContainer());
        $loader->addFixture($default);

        return $loader;
    }



}
