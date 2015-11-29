<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 19/07/15
 * Time: 16:03
 */

namespace PUGX\Cmf\PageBundle\Tests\WebTest\DataFixtures\PageAdminTest;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use PUGX\Cmf\PageBundle\Document\Page;

class TestEditPageFixture implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (!$manager instanceof DocumentManager) {
            $class = get_class($manager);
            throw new \RuntimeException(
                "Fixtures requires PHPCR ODM DocumentManager instance, instance of '$class' given.'"
            );
        }
        $page = new Page();
        $page->setTitle('To be edited');
        $page->setText('This page has to be edited soon.');
        $manager->persist($page);
        $manager->flush();
    }
}
