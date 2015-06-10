<?php
namespace NGS\Symfony\Form\Type;

use NGS\ByteStream;
use NGS\Symfony\Form\DataTransformer\UploadToBytestreamTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BytestreamType extends FileType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getData();
        $view->vars = array_replace($view->vars, array(
            'type' => 'file',
            'value' => '',
            'size' => $data instanceof ByteStream ? $data->size() : null,
        ));
    }

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
