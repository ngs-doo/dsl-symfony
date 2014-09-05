<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NGS\Symfony\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollection;

class CollectionType extends SymfonyCollection
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['controller'])
            $options['type'] = $options['controller']->getFormType();

        if (isset ($options['type']))
            $options['prototype_name'] = '__' . $options['type']->getName() . '__';

        parent::buildForm ($builder, $options);
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView ($view, $form, $options);

        if ($options['controller']) {
            $controller = $options['controller'];
            $controller->setContainer ($this->container);

            $html = $this->container->get('templating')->render (
                $controller->getBundleName().$controller->getEditTemplate(),
                array(
                    'route_prefix' => $controller->getRoutePrefix(),
                    'form' => $form->getConfig()->getAttribute ('prototype')->createView($view),
                    'ajax' => true,
                    'ngs_collection' => true
                )
            );

            $view->vars['html'] = $html;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions ($resolver);

        $resolver->setDefaults(array(
            'controller' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ngs_collection';
    }
}
