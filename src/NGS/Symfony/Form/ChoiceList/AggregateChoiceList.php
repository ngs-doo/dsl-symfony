<?php
namespace NGS\Symfony\Form\ChoiceList;

use NGS\Cache\AggregateCache;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

class AggregateChoiceList extends ObjectChoiceList
{
    private $className;
    private static $cachedChoices;

    /**
     * @param string
     */
    public function __construct($className, $labelPath = null, array $preferredChoices = array(), $groupPath = null, $valuePath = 'URI')
    {
        if(!is_string($className))
            throw new \InvalidArgumentException('Class name was not a string');

        $this->className = $className;

        parent::__construct($this->getItems(), $labelPath, $preferredChoices, $groupPath, $valuePath);
    }

    // sets choices to exact instances of $this->choices to avoid
    // problems with strict comparison === in ObjectChoiceList::getValuesForChoices
    protected function fixChoices(array $choices)
    {
        $fixed = array();

        foreach ($this->getChoices() as $i => $choice)
            foreach ($choices as $j => $givenChoice)
                if ($choice == $givenChoice)
                    $fixed[] = $choice;

        return $fixed;
    }

    private function getItems()
    {
        if($items = $this->getCachedChoices())
            return $items;

        $class = $this->className;
        $items = $class::findAll();
        $this->setCachedChoices($items);
        return $items;
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
