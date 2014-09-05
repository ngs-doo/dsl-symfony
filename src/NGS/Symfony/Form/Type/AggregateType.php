<?php
namespace NGS\Symfony\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AggregateType extends FormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('URI',   'hidden',
                array('property_path'=>false)
            );
    }

    public function getParent()
    {
        return 'form';
    }
}
