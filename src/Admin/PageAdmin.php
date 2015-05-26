<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 23/05/15
 * Time: 20:48
 */

namespace Webgriffe\Cmf\PageBundle\Admin;


use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;

class PageAdmin extends Admin
{
    protected $translationDomain = 'WebgriffeCmfPageBundle';

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('form.group_general')
                ->add(
                    'parentDocument',
                    'doctrine_phpcr_odm_tree',
                    array('root_node' => $this->getRootPath(), 'choice_list' => array(), 'select_root_node' => true)
                )
//                ->add('name', 'text')
                ->add('title', 'text')
                ->add('content', 'textarea')
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name');
    }


}
