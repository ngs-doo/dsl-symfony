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
class LookupType extends ReferenceType
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
        $resolver->setOptional(array('actions'));
        $resolver->setOptional(array('display'));
        $resolver->setOptional(array('type'));
        $resolver->setOptional(array('modalContainer'));

        $resolver->setDefaults(array(
            'actions'    => true,
            'compound'   => false,
            'display' => 'URI',
            'class' => null,
            'type' => 'text',
            'modalContainer' => 'body'
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
        if ($options['class'] === null) {
            $controller = new $options['controller'];
            $options['class'] = $controller->getClass();
        }

        parent::buildForm ($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $controller = new $options['controller'];
        $controller->setContainer($this->container);

        // @todo getRoutePrefix needs fixing
        $chunks = explode('\\', get_class($controller));
        $routePrefix = strtolower(implode('_', $chunks));
        $routePrefix = str_replace(array('controller_', 'controller', 'bundle'), '', $routePrefix);

        $view->vars['route_prefix'] = $routePrefix.'_';

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

        $view->vars['display'] = $options['display'];
        $view->vars['modalContainer'] = $options['modalContainer'];
    }
}
