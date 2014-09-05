<?php
namespace NGS\Symfony\Form\ChoiceList;

use NGS\Cache\AggregateCache;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

class UriChoiceList extends SimpleChoiceList
{
    private $className;
    private static $cachedChoices;

    /**
     * @param string
     */
    public function __construct($className, $labelPath, array $preferredChoices = array())
    {
        if(!is_string($className))
            throw new \InvalidArgumentException('Class name was not a string');

        $this->className = $className;

        parent::__construct($this->getItems($labelPath), $preferredChoices);
    }

    private static function prepareArray(array $items, $label)
    {
        $result = array();
        foreach($items as $it) {
            $result[$it->URI] = $it->{$label};
        }
        return $result;
    }

    private function getItems($label)
    {
        if($items = $this->getCachedChoices())
            return self::prepareArray($items, $label);

        $class = $this->className;
        $items = $class::findAll();
        $this->setCachedChoices($items);
        return self::prepareArray($items, $label);
    }

    private function getCachedChoices()
    {
        $class = $this->className;
        if(isset(self::$cachedChoices[$class]))
            return self::$cachedChoices[$class];

        $items = AggregateCache::load($class);
        if(!$items)
            $items = array();
        self::$cachedChoices[$class] = $items;
        return $items;
    }

    private function setCachedChoices(array $choices)
    {
        AggregateCache::save($this->className, $choices);
        self::$cachedChoices[$this->className] = $choices;
    }
}
