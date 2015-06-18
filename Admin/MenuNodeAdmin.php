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
            ->getFormBuilder()
            ->addEventListener(
                FormEvents::SUBMIT,
                function (FormEvent $event) {
                    /** @var MenuNode $menuNode */
                    $menuNode = $event->getData();
                    if ($menuNode->getLabel()) {
                        $slugify = Slugify::create();
                        $menuNode->setName($slugify->slugify($menuNode->getLabel()));
                    }
                }
            );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('parentPathLabel', 'text')
            ->addIdentifier('label')
        ;
    }
}
