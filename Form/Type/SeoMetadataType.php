<?php

namespace PUGX\Cmf\PageBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Cmf\Bundle\SeoBundle\Form\Type\SeoMetadataType as BaseType;

class SeoMetadataType extends BaseType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'form.label_title', 'sonata_help' => 'form.help_title'))
            ->add(
                'metaDescription',
                'textarea',
                array('label' => 'form.label_metaDescription', 'sonata_help' => 'form.help_metaDescription')
            )
            ->add(
                'metaKeywords',
                'textarea',
                array('label' => 'form.label_metaKeywords', 'sonata_help' => 'form.help_metaKeywords')
            )
        ;
    }
}
