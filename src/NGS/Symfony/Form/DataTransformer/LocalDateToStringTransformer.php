<?php
namespace NGS\Symfony\Form\DataTransformer;

use InvalidArgumentException;
use NGS\LocalDate;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;

class LocalDateToStringTransformer implements DataTransformerInterface
{
    protected $format;

    public function __construct($format = null)
    {
        if ($format === null)
            $this->format = 'Y-m-d';
        else
            $this->format = $format;
    }

    public function transform($value)
    {
        if($value instanceof LocalDate) {
            $date = $value->toDateTime();
            return $date->format($this->format);
        } else if(is_string($value))
            return $value;
        // By convention, transform() should return an empty string if NULL is passed.
        else if($value === null)
            return "";
        else
            throw new TransformationFailedException('Could not convert LocalDate value to string from type:"'.\NGS\Utils::getType($value).'"');
    }

    public function reverseTransform($value)
    {
        $dateTime = \DateTime::createFromFormat ($this->format, $value);
        if ($dateTime)
            $value = $dateTime->format("Y-m-d");

        if($value === null || $value === "") {
            return null;
        }
        try {
            return new LocalDate($value);
        }
        catch(InvalidArgumentException $ex) {
            throw new TransformationFailedException($ex->getMessage());
        }
    }
}
