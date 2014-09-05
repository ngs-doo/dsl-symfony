<?php
namespace NGS\Symfony\Form\DataTransformer;

use NGS\Money;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer as BaseTransformer;

/**
 * Transformer extended for handling NGS\Money type
 */
class MoneyToLocalizedStringTransformer extends BaseTransformer
{
    public function transform($value)
    {
        if($value === null || (is_string($value)&&trim($value)===''))
            return null;
        if ($value instanceof Money) {
            $value = (string)$value;
        }
        return parent::transform($value);
    }

    public function reverseTransform($value)
    {
        if($value === null || (is_string($value)&&trim($value)==='')) {
            return null;
        }
        try {
            // number format should ideally be handled through config
            $value = str_replace(array(' ', ','), array('', '.'), $value);
            return new Money($value);
        }
        catch(\InvalidArgumentException $ex) {
            throw new TransformationFailedException($ex->getMessage());
        }
    }
}
