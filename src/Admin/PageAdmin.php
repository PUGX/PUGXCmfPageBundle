<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 23/05/15
 * Time: 20:48
 */

namespace Webgriffe\Cmf\PageBundle\Admin;


use Cocur\Slugify\Slugify;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webgriffe\Cmf\PageBundle\Document\Page;

class PageAdmin extends Admin
{
    protected $translationDomain = 'WebgriffeCmfPageBundle';

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('form.group_general')
                ->add('title', 'text')
                ->add('content', 'textarea')
            ->end()
            ->with('form.group_menu')
                ->add(
                    'menuNodes',
                    'sonata_type_collection',
                    array(),
                    array('edit' => 'inline', 'inline' => 'table', 'admin_code' => 'cmf_menu.node_admin')
                )
            ->end()
            ->getFormBuilder()
            ->addEventListener(
                FormEvents::SUBMIT,
                function (FormEvent $event) {
                    /** @var Page $page */
                    $page = $event->getData();
                    if ($page->getTitle()) {
                        $slugify = Slugify::create();
                        $page->setName($slugify->slugify($page->getTitle()));
                    }
                }
            )
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('title')
            ->add('menuNodes')
            ->add(
                'routes',
                null,
                array(
                    'associated_property' => 'path',
                    'template' => 'WebgriffeCmfPageBundle:CRUD:list_routes.html.twig'
                )
            )
        ;
    }

    public function getNewInstance()
    {
        /** @var Page $page */
        $page = parent::getNewInstance();
        $root = $this->getModelManager()->find(null, $this->getRootPath());
        $page->setParentDocument($root);
        return $page;
    }
}
