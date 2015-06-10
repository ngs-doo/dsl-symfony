<?php
namespace NGS\Symfony\Form\DataTransformer;

use NGS\BigDecimal;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DecimalViewTransformer implements DataTransformerInterface
{
    public function __construct($scale = null)
    {
        $this->scale = null;
    }

    public function transform($value)
    {
        if ($value instanceof BigDecimal) {
            return $value->toStringWith($value->getScale());
        }
        return (string)$value;
    }

    public function reverseTransform($value)
    {
        if($value === null) {
            return null;
        }
        try {
            return new BigDecimal($value);
        }
        catch(\InvalidArgumentException $ex) {
            throw new TransformationFailedException($ex->getMessage());
        }
    }
}
