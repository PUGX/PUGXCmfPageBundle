<?php


namespace PUGX\Cmf\PageBundle\Tests\WebTest\DataFixtures\PageAdminTest;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PUGX\Cmf\PageBundle\Document\MenuNode;
use PUGX\Cmf\PageBundle\Document\Page;

class TwoPagesWithMenuNodesFixture implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $page = new Page();
        $page->setParentDocument($manager->find(null, '/cms/content'));
        $page->setName('page-1');
        $page->setTitle('Page 1');
        $page->setText('Lorem ipsum dolor sit amet');
        $page->setPublishable(true);
        $menuNode = new MenuNode();
        $menuNode->setLabel('Page 1');
        $menuNode->setParentDocument($manager->find(null, '/cms/menu/main'));
        $page->addMenuNode($menuNode);
        $manager->persist($page);

        $page = new Page();
        $page->setParentDocument($manager->find(null, '/cms/content'));
        $page->setName('page-2');
        $page->setTitle('Page 2');
        $page->setText('Lorem ipsum dolor sit amet');
        $page->setPublishable(true);
        $menuNode = new MenuNode();
        $menuNode->setLabel('Page 2');
        $menuNode->setParentDocument($manager->find(null, '/cms/menu/main'));
        $page->addMenuNode($menuNode);
        $manager->persist($page);

        $manager->flush();
    }
}
