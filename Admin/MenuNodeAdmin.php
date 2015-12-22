<?php

/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 27/05/15
 * Time: 22:07.
 */

namespace PUGX\Cmf\PageBundle\Admin;

use Cocur\Slugify\Slugify;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MenuNodeAdmin extends \Symfony\Cmf\Bundle\MenuBundle\Admin\MenuNodeAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        parent::configureFormFields($form);
        $form
            ->remove('name')
            ->remove('linkType')
            ->remove('route')
            ->with('form.group_general')
                ->add(
                    'parent',
                    'doctrine_phpcr_odm_tree',
                    array(
                        'root_node' => $this->menuRoot,
                        'select_root_node' => false, // <- Rewritten select root node to false
                        'choice_list' => array(),
                    )
                )
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('parentPathLabel', 'text')
            ->addIdentifier('label')
        ;
    }
}
