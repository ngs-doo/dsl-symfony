<?php
namespace NGS\Symfony\Form\DataTransformer;

use NGS\BigDecimal;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DecimalViewTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if($value instanceof BigDecimal) {
            //TODO temp hack
            $dv = new BigDecimal($value->value, 2);
            return (string)$dv;
        }

        return $value;
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
