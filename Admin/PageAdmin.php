<?php

/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 23/05/15
 * Time: 20:48.
 */

namespace PUGX\Cmf\PageBundle\Admin;

use Cocur\Slugify\Slugify;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use PUGX\Cmf\PageBundle\Document\Page;

class PageAdmin extends Admin
{
    protected $translationDomain = 'PUGXCmfPageBundle';

    protected function configureFormFields(FormMapper $form)
    {
        $that = $this;
        $form
            ->with('form.group_general')
                ->add('title', 'text')
                ->add('text', 'textarea')
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
                function (FormEvent $event) use ($that) {
                    $that->generatePageNodeName($event->getData());
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
                    'template' => 'PUGXCmfPageBundle:CRUD:list_routes.html.twig',
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

    public function generatePageNodeName(Page $page)
    {
        if ($page->getTitle()) {
            $slugify = Slugify::create();
            $slug = $slugify->slugify($page->getTitle());
            $slug = $this->getAvailableSlug($page, $slug);
            $page->setName($slug);
        }
    }

    private function getAvailableSlug(Page $page, $slug)
    {
        $path = rtrim($page->getParentDocument()->getId(), '/');
        if (!$this->getModelManager()->find(null, $path . '/' . $slug)) {
            return $slug;
        }
        $matches = array();
        if (preg_match('/(.*?)-(\d+)/', $slug, $matches)) {
            $slug = $matches[1];
            $increment = $matches[2];
            return $this->getAvailableSlug($page, $slug . '-' . ++$increment);
        }
        return $this->getAvailableSlug($page, $slug . '-1');
    }
}
