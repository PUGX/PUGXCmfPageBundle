<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 24/11/15
 * Time: 08:08
 */

namespace PUGX\Cmf\PageBundle\Admin\Extension;

use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Cmf\Bundle\SeoBundle\Admin\Extension\SeoContentAdminExtension as BaseExtension;

class SeoContentAdminExtension extends BaseExtension
{
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab($this->formGroup, array('translation_domain' => 'CmfSeoBundle'))
                ->with('form.group_general')
                    ->add('seoMetadata', 'seo_metadata', array('label' => false))
                ->end()
            ->end()
        ;
    }
}
