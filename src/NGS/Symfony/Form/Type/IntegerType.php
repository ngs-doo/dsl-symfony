<?php
namespace NGS\Symfony\Form\Type;

class IntegerType extends \Symfony\Component\Form\Extension\Core\Type\IntegerType
{
    public function getName()
    {
        return 'ngs_integer';
    }
}
