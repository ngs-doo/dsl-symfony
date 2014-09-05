<?php
namespace NGS\Symfony\Form\Type;

use NGS\Symfony\Form\DataTransformer\UploadToBytestreamTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class BytestreamType extends FileType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new UploadToBytestreamTransformer());;
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
