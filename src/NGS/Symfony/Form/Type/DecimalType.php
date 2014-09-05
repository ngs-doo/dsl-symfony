<?php
namespace NGS\Symfony\Form\Type;

use Symfony\Component\Form\AbstractType;
use NGS\Symfony\Form\DataTransformer\DecimalViewTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extended decimal field with custom Transformer for handling NGS\Decimal type
 */
class DecimalType extends NumberType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->resetModelTransformers()
            ->resetViewTransformers()
            ->addViewTransformer(new DecimalViewTransformer());
        ;
    }

    public function getName()
    {
        return 'ngs_decimal';
    }

    public function getParent()
    {
        return 'number';
    }
}
