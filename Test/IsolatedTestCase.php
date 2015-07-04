<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 04/07/15
 * Time: 19:06
 */

namespace PUGX\Cmf\PageBundle\Test;

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
}
