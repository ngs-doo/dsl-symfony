<?php
namespace NGS\Symfony\Form\Type;

use Symfony\Component\Form\AbstractType;
use NGS\Symfony\Form\DataTransformer\LocalDateToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Extended date field with custom Transformer for handling NGS\LocalDate type
 */
class LocalDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new LocalDateToStringTransformer($options['format']))
        ;
    }

    public function setDefaultOptions (OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions ($resolver);
        $resolver->setOptional (array("format"));

        $resolver->setDefaults (array(
            "format" => 'Y-m-d'
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // @todo
        // $view->vars['date_format'] = $options['format'];
    }

    public function getName()
    {
        return 'ngs_localdate';
    }

    public function getParent()
    {
        return 'text';
    }
}
