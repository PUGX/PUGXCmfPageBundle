<?php


namespace PUGX\Cmf\Tests\WebTest;


use PUGX\Cmf\PageBundle\Test\IsolatedTestCase;

class MenuNodeAdminTest extends IsolatedTestCase
{
    public function testChangeMenuLabelShouldNotChangeMenuNodeOrder()
    {
        $this->loadFixtures(
            array('PUGX\Cmf\PageBundle\Tests\WebTest\DataFixtures\MenuNodeAdminTest\TwoPagesWithMenuNodesFixture')
        );

        $client = static::createClient();
        $crawler = $client->request('GET', '/page-1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $mainMenu = $crawler->filter('nav#menu-main ul')->eq(0);
        $this->assertCount(2, $mainMenu->filter('li'));
        $this->assertContains('Page 1', $mainMenu->filter('li')->eq(0)->text());
        $this->assertContains('Page 2', $mainMenu->filter('li')->eq(1)->text());

        $crawler = $client->request('GET', '/admin/cmf/page/menunode/cms/menu/main/page-1/edit?uniqid=menu_node');
        $editForm = $crawler->selectButton('Update')->form();
        $editForm['menu_node[label]'] = 'Page 1 Edit';
        $client->submit($editForm);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertContains(
            "Item \"Page 1 Edit\" has been successfully updated.",
            $crawler->filter('.alert.alert-success')->text()
        );

        $crawler = $client->request('GET', '/page-1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $mainMenu = $crawler->filter('nav#menu-main ul')->eq(0);
        $this->assertCount(2, $mainMenu->filter('li'));
        $this->assertContains('Page 1', $mainMenu->filter('li')->eq(0)->text());
        $this->assertContains('Page 2', $mainMenu->filter('li')->eq(1)->text());
    }
}
