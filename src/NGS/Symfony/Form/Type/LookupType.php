<?php
namespace NGS\Symfony\Form\Type;

use NGS\Symfony\Form\Type\ReferenceType;
use NGS\Symfony\Form\DataTransformer\IdentifiableToUriTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Extended reference field with custom Transformer for handling Identifiable type
 */
class LookupType extends ReferenceType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setOptional(array('display'));
        $resolver->setOptional(array('type'));

        $resolver->setDefaults(array(
            'actions'    => true,
            'compound'   => false,
            'display' => 'URI',
            'class' => null,
            'type' => 'text',
            'modalContainer' => 'body',
            'url' => null,
        ));
    }

    public function getName()
    {
        return 'ngs_lookup';
    }

    public function getParent()
    {
        return 'text';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // @todo guess transformer?
        return parent::buildForm ($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // parent::buildView($view, $form, $options);

        if ($view->vars['data'] && $view->vars['data']->__get ($options['display']))
            $view->vars['display_value'] = $view->vars['data']->__get ($options['display']);
        else
            $view->vars['display_value'] = $view->vars['value'];

        if ($options['display'] === 'URI') {
            $view->vars['type2'] = 'hidden';
            $view->vars['type1'] = $options['type'];
        } else {
            $view->vars['type1'] = 'hidden';
            $view->vars['type2'] = $options['type'];
        }

        $model = str_replace('\\', '.', $options['class']);
        $view->vars['model'] = $model;
        $view->vars['display'] = $options['display'];
        $view->vars['modalContainer'] = $options['modalContainer'];
    }
}
