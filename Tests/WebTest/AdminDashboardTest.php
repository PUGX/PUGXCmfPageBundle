<?php
namespace PUGX\Cmf\Tests\WebTest;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 21/06/15
 * Time: 11:54
 */

class AdminDashboardTest extends BaseTestCase
{

    public function testDashboardPage()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/dashboard');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
