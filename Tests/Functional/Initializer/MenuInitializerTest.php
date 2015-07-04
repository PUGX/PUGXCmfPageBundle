<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 04/07/15
 * Time: 15:02
 */

namespace PUGX\Cmf\Tests\Functional\Initializer;


use Doctrine\ODM\PHPCR\ChildrenCollection;
use Doctrine\ODM\PHPCR\DocumentManager;
use PUGX\Cmf\PageBundle\Initializer\MenuInitializer;
use PUGX\Cmf\PageBundle\Test\IsolatedTestCase;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Cmf\Bundle\MenuBundle\Provider\PhpcrMenuProvider;

class MenuInitializerTest extends IsolatedTestCase
{
    public function testInitCreatesConfiguredMenus()
    {
        $container = $this->getContainer();
        /** @var PhpcrMenuProvider $menuProvider */
        $menuProvider = $container->get('cmf_menu.provider');
        $this->removeInitializedMenus($menuProvider);

        $menus = array('main' => 'Main Menu', 'footer' => 'Footer Menu');
        /** @var MenuInitializer $menuInitializer */
        $menuInitializer = new MenuInitializer($menuProvider, $menus);
        $menuInitializer->init($this->getDbManager('PHPCR')->getRegistry());

        /** @var DocumentManager $documentManager */
        $documentManager = $this->getDbManager('PHPCR')->getOm();
        $documentManager->clear();
        $menuBasePath = $menuProvider->getMenuRoot();
        /** @var Menu $menuBase */
        $menuBase = $documentManager->find(null, $menuBasePath);
        /** @var ChildrenCollection $menus */
        $menus = $menuBase->getChildren();
        $this->assertCount(2, $menus);
        /** @var Menu $menu */
        $menu = $menus->get('main');
        $this->assertEquals('main', $menu->getName());
        $this->assertEquals('Main Menu', $menu->getLabel());
        /** @var Menu $menu */
        $menu = $menus->get('footer');
        $this->assertEquals('footer', $menu->getName());
        $this->assertEquals('Footer Menu', $menu->getLabel());
    }

    /**
     * @param $menuProvider
     * @return \Knp\Menu\NodeInterface[]
     * @throws \Doctrine\ODM\PHPCR\PHPCRException
     */
    private function removeInitializedMenus($menuProvider)
    {
        /** @var DocumentManager $documentManager */
        $documentManager = $this->getDbManager('PHPCR')->getOm();
        $menuBasePath = $menuProvider->getMenuRoot();
        /** @var Menu $menuBase */
        $menuBase = $documentManager->find(null, $menuBasePath);
        $this->assertNotNull($menuBase);
        $menus = $menuBase->getChildren();
        $this->assertCount(2, $menus);
        foreach ($menus as $child) {
            $documentManager->remove($child);
        }
        $documentManager->flush();
        /** @var Menu $menuBase */
        $documentManager->clear();
        $menuBase = $documentManager->find(null, $menuBasePath);
        $menus = $menuBase->getChildren();
        $this->assertCount(0, $menus);
    }
}
