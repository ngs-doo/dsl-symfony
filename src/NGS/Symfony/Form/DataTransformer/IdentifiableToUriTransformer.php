<?php
namespace NGS\Symfony\Form\DataTransformer;

use InvalidArgumentException;
use NGS\Client\CrudProxy;
use NGS\Client\RestHttp;
use NGS\Patterns\Identifiable;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdentifiableToUriTransformer implements DataTransformerInterface
{
    /** @var Identifiable */
    private $class;

    private $client;

    public function __construct($class, RestHttp $client=null)
    {
        if(!class_exists($class))
            throw new InvalidArgumentException('Non-existing class name "'.$class.'" given.');

        $this->class = $class;
        $this->client = $client;
    }

    // Aggregate root instance => URI
    public function transform($value = null)
    {
        if($value === null)
            return null;

        if(!$value instanceof Identifiable)
            throw new TransformationFailedException('Value was not instance of Identifiable, type was: "'.\NGS\Utils::getType($value).'"');

        return $value->URI;
    }

    // URI => Aggregate root instance
    public function reverseTransform($value = null)
    {
        if($value === null)
            return null;

        if($value instanceof Identifiable)
            return $value;

        try {
            $proxy = new CrudProxy($this->client);
            return $proxy->read($this->class, $value);
        }
        catch (\Exception $e) {
            return null;
            //TODO log is broken
            //throw new TransformationFailedException('Could not find Aggregate "'.$this->class.'" with URI "'.$value.'" in AggregateUriTransformer');
        }
    }
}
