<?php
namespace PUGX\Cmf\Tests\WebTest;

use PUGX\Cmf\PageBundle\Test\IsolatedTestCase;

/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 21/06/15
 * Time: 11:54
 */

class AdminDashboardTest extends IsolatedTestCase
{

    public function testDashboardPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/dashboard');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $cmsBox = $crawler->filter('.right-side .cms-block');
        $this->assertEquals('Content Management System', $cmsBox->filter('.box-header h3')->text());
        $this->assertCount(1, $cmsBox->filter('.box-body table tr'));
        $this->assertContains('Page', $cmsBox->filter('.box-body table tr')->eq(0)->filter('td')->eq(0)->text());
    }
}
