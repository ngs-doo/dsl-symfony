<?php
namespace NGS\Symfony\Form\DataTransformer;

use InvalidArgumentException;
use NGS\Patterns\Identifiable;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UriToValueTransformer implements DataTransformerInterface
{
    /** @var Identifiable */
    private $class;

    private $valueProperty;

    public function __construct($class, $valueProperty)
    {
        if(!class_exists($class))
            throw new InvalidArgumentException('Non-existing class name "'.$class.'" given.');

        $this->class = $class;
        $this->valueProperty = $valueProperty;
    }

    // URI => value
    public function transform($value = null)
    {
        if($value === null)
            return null;

        $class = $this->class;

        try {
            $item = $class::find($value);
            return $item->{$this->valueProperty};
        }
        catch(\Exception $ex) {
            throw new TransformationFailedException('Could not transform URI "'.$value.'" to property value "'.$this->valueProperty.'"');
        }
    }

    // value => URI
    public function reverseTransform($value = null)
    {
        if($value === null)
            return null;

        if($value instanceof Identifiable)
            return $value->URI;

        try {
            $class = $this->class;
            $items = $class::findAll();

            $property = $this->valueProperty;

            foreach($items as $item) {
                // @todo strict comparison
                if($item->{$this->valueProperty} == $value)
                    return $item->URI;
            }
            throw new TransformationFailedException('Could not find Aggregate "'.$this->class.'" with value "'.$value.'" of property "'.$this->valueProperty.'" in AggregateUriTransformer');
        }
        catch (\Exception $e) {
            //return null;
            //TODO log is broken
            throw new TransformationFailedException('Could not convert to URI ("'.$this->class.'") with value "'.$value.'" of property "'.$this->valueProperty.'" in AggregateUriTransformer');
        }
    }
}
