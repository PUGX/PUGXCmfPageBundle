<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 04/07/15
 * Time: 19:06
 */

namespace PUGX\Cmf\PageBundle\Test;

use Doctrine\Bundle\PHPCRBundle\DataFixtures\PHPCRExecutor;
use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Doctrine\Common\DataFixtures\Purger\PHPCRPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Cmf\Component\Testing\Functional\DbManager\PHPCR;

class IsolatedTestCase extends BaseTestCase
{
    protected function setUp()
    {
        /** @var PHPCR $dbManger */
        $dbManger = $this->getDbManager('PHPCR');
        $dbManger->purgeRepository(true);
        $dbManger->getOm()->clear();
    }

    protected function loadFixtures(array $fixtures)
    {
        /** @var PHPCR $dbManager */
        $dbManager = $this->getDbManager('PHPCR');

        $loader = new ContainerAwareLoader($this->getContainer());;
        foreach ($fixtures as $className) {
            $dbManager->loadFixtureClass($loader, $className);
        }

        $purger = new PHPCRPurger();
        $executor = new PHPCRExecutor($dbManager->getOm(), $purger);
        $referenceRepository = new ProxyReferenceRepository($dbManager->getOm());
        $executor->setReferenceRepository($referenceRepository);
        $executor->execute($loader->getFixtures(), true);
        $dbManager->getOm()->clear();
    }
}
