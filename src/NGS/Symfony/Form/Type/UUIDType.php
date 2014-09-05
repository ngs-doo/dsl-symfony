<?php
namespace NGS\Symfony\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UUIDType extends FormType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'NGS\UUID'
        ));
    }

    public function getName()
    {
        return 'ngs_uuid';
    }

    public function getParent()
    {
        return 'hidden';
    }
}
