<?php

namespace OCPlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationType extends AdvertType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('date');

    }
    
   
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ocplatformbundle_advert_edit';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return new AdvertType() ;
    }

}
