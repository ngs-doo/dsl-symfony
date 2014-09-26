<?php
namespace NGS\Symfony\Form\Type;

use NGS\Symfony\Form\DataTransformer\IdentifiableToUriTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Extended reference field with custom Transformer for handling Identifiable type
 */
class ReferenceType extends TextType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('class'));
        $resolver->setOptional(array('transformer', 'dsl_client'));
        $resolver->setDefaults(array(
            'compound'   => false,
            'dsl_client' => null,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(isset($options['transformer']))
            $builder->addViewTransformer(new $options['transformer']);
        else
            $builder->addViewTransformer(new IdentifiableToUriTransformer($options['class'], $options['dsl_client']));
    }

    public function getName()
    {
        return 'ngs_reference';
    }

    public function getParent()
    {
        return 'text';
    }
}
