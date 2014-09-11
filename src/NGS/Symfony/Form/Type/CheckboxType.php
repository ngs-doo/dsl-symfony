<?php
namespace NGS\Symfony\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CheckboxType extends AbstractType
{
    public function getName()
    {
        return 'ngs_checkbox';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /* prevent putting required attribute in html5; with required
        * checkbox must be checked to submit form */
        $view->vars['required'] = false;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            /* prevent putting required attribute in html5; with required
             * checkbox must be checked to submit form */
            //'required' => false,
        ));
    }

    public function getParent()
    {
        return 'checkbox';
    }
}
