<?php

/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 28/05/15
 * Time: 19:45.
 */

namespace PUGX\Cmf\PageBundle\Initializer;

use Doctrine\Bundle\PHPCRBundle\Initializer\InitializerInterface;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use Doctrine\ODM\PHPCR\Document\Generic;
use Doctrine\ODM\PHPCR\DocumentManager;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Cmf\Bundle\MenuBundle\Provider\PhpcrMenuProvider;

class MenuInitializer implements InitializerInterface
{
    const MENU_DOCUMENT_CLASS_NAME = 'Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu';

    /**
     * @var PhpcrMenuProvider
     */
    protected $menuProvider;

    /**
     * @var array
     */
    protected $menus;

    /**
     * @param $menuProvider
     * @param array $menus
     */
    public function __construct(PhpcrMenuProvider $menuProvider, array $menus = array())
    {
        $this->menuProvider = $menuProvider;
        $this->menus = $menus;
    }

    /**
     * This method should be used to establish the requisite
     * structure needed by the application or bundle of the
     * content repository.
     *
     * @param ManagerRegistry $registry
     */
    public function init(ManagerRegistry $registry)
    {
        $className = self::MENU_DOCUMENT_CLASS_NAME;
        /** @var DocumentManager $dm */
        $dm = $registry->getManagerForClass($className);
        /** @var Generic $parent */
        $menuRoot = $this->menuProvider->getMenuRoot();
        $parent = $dm->find(null, $menuRoot);

        if (!$parent) {
            throw new \InvalidArgumentException("Cannot find menu base path '{$this->menuProvider}'.");
        }

        foreach ($this->menus as $menuName => $menuLabel) {
            /** @var Menu $menu */
            $menu = $dm->find($className, $menuRoot .'/'.$menuName);
            if (!$menu) {
                $menu = new $className();
            }
            $menu->setName($menuName);
            $menu->setLabel($menuLabel);
            $menu->setParentDocument($parent);
            $dm->persist($menu);
        }

        $dm->flush();
    }

    /**
     * Return a name which can be used to identify this initializer.
     *
     * @return string
     */
    public function getName()
    {
        return 'pugx_cmf_page_bundle_menu_initializer';
    }
}
