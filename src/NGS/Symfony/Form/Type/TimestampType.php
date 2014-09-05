<?php
namespace NGS\Symfony\Form\Type;

use Symfony\Component\Form\AbstractType;
use NGS\Symfony\Form\DataTransformer\TimestampToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Extended timestamp field with custom Transformer for handling NGS\Timestamp type
 */
class TimestampType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new TimestampToStringTransformer($options['format']))
        ;
    }

    public function setDefaultOptions (OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions ($resolver);
        $resolver->setOptional (array("format"));

        $resolver->setDefaults (array(
            "format" => 'Y-m-d H:i:s'
        ));
    }

    public function getName()
    {
        return 'ngs_timestamp';
    }

    public function getParent()
    {
        return 'text';
    }
}
