<?php
namespace NGS\Symfony\Form\Type;

use NGS\Symfony\Form\DataTransformer\UploadToBytestreamTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BytestreamType extends FileType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        /*$resolver->setDefaults(array(
            'data_class' => 'NGS\ByteStream',
        ));*/
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new UploadToBytestreamTransformer());
    }

    public function getName()
    {
        return 'ngs_bytestream';
    }

    public function getParent()
    {
        return 'file';
    }
}
