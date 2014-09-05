<?php
namespace NGS\Symfony\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType as BaseMoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use NGS\Symfony\Form\DataTransformer\MoneyToLocalizedStringTransformer;

/**
 * Extended money field with custom Transformer for handling NGS\Money type
 */
class MoneyType extends BaseMoneyType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->resetModelTransformers()
            ->resetViewTransformers()
            ->addViewTransformer(new MoneyToLocalizedStringTransformer(
                $options['precision'],
                $options['grouping'],
                null,
                $options['divisor']
            ))
            ->setAttribute('currency', $options['currency'])
        ;
    }

    public function getName()
    {
        return 'ngs_money';
    }


    public function getParent()
    {
        return 'money';
    }

}
