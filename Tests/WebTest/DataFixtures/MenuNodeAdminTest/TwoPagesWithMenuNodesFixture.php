<?php


namespace PUGX\Cmf\PageBundle\Tests\WebTest\DataFixtures\MenuNodeAdminTest;


use Cocur\Slugify\Slugify;
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
        $page->setTitle('Page 1');
        $page->setText('Lorem ipsum dolor sit amet.');
        $page->setPublishable(true);
        $page->addMenuNode($this->createMenuNodeForPage('Page 1', $page, $manager));
        $manager->persist($page);

        $page = new Page();
        $page->setTitle('Page 2');
        $page->setText('Lorem ipsum dolor sit amet.');
        $page->setPublishable(true);
        $page->addMenuNode($this->createMenuNodeForPage('Page 2', $page, $manager));
        $manager->persist($page);

        $manager->flush();
    }

    /**
     * @param $label
     * @param $page
     * @param ObjectManager $manager
     * @return MenuNode
     */
    private function createMenuNodeForPage($label, $page, ObjectManager $manager)
    {
        $slugify = Slugify::create();
        $menuNode = new MenuNode();
        $menuNode->setName($slugify->slugify($label));
        $menuNode->setLabel($label);
        $menuNode->setContent($page);
        $menuNode->setParentDocument($manager->find(null, '/cms/menu/main'));
        return $menuNode;
    }
}
