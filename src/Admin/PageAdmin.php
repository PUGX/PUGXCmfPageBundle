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
                ->add(
                    'parent',
                    'doctrine_phpcr_odm_tree',
                    array('root_node' => $this->getRootPath(), 'choice_list' => array(), 'select_root_node' => true)
                )
                ->add('title', 'text')
                ->add('content', 'textarea')
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
        $list->addIdentifier('title');
    }


}
