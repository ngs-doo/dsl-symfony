<?php
namespace NGS\Symfony\Form\DataTransformer;

use InvalidArgumentException;
use NGS\Timestamp;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TimestampToStringTransformer implements DataTransformerInterface
{
    private $format;

    public function __construct ($format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    public function transform($value)
    {
        if($value instanceof Timestamp)
            return $value->format ($this->format);

        if(is_string($value))
            return $value;

        // By convention, transform() should return an empty string if NULL is passed.
        if($value === null)
            return '';

        throw new TransformationFailedException('Could not transform value to string, was expecting NGS\Timestamp, but given value was type :"'.\NGS\Utils::getType($value).'"');
    }

    public function reverseTransform($value)
    {
        // By convention, reverseTransform() should return NULL if an empty string is passed.
        if($value === null || $value==='')
            return null;

        try {
            return new Timestamp($value, $this->format);
        }
        catch(InvalidArgumentException $ex) {
            throw new TransformationFailedException($ex->getMessage());
        }
    }
}
