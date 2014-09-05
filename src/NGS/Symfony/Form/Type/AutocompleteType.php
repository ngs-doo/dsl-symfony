<?php
namespace NGS\Symfony\Form\Type;

use NGS\Symfony\Form\Type\ReferenceType;
use NGS\Symfony\Form\DataTransformer\IdentifiableToUriTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extended reference field with custom Transformer for handling Identifiable type
 */
class AutocompleteType extends ReferenceType
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(array('controller'));
        $resolver->setOptional(array('class'));
        $resolver->setOptional(array('transformer'));
        $resolver->setOptional(array('path'));
        $resolver->setOptional(array('display'));

        $resolver->setDefaults(array(
            'compound' => false,
            'class' => null,
            'transformer' => null,
            'path' => null,
            'display' => 'URI'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['class'] === null) {
            $controller = new $options['controller'];
            $options['class'] = $controller->getClass();
        }

        parent::buildForm ($builder, $options);
    }

    public function getName()
    {
        return 'ngs_autocomplete';
    }

    public function getParent()
    {
        return 'text';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['path'] === null) {
            $controller = new $options['controller'];
            $options['path'] = $controller->getSphinxPath();
        }

        if (is_object($view->vars['data']) && $view->vars['data']->__get ($options['display']))
            $view->vars['display_value'] = $view->vars['data']->__get ($options['display']);
        else
            $view->vars['display_value'] = $view->vars['value'];

        $view->vars['display'] = $options['display'];
        $view->vars['path'] = $options['path'];
    }
}
