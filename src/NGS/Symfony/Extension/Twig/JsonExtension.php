<?php
namespace NGS\Symfony\Extension;

/**
 * 'json' filter properly converts nested arrays of NGS objects
 * Native json_encode returns empty NGS objects
 */
class JsonExtension extends \Twig_Extension
{
    public function getFilters() {
        return array(
            'json'  => new \Twig_Filter_Method($this, 'toJsonFilter',
                // turns off auto-escaping html entities in html templates
                // otherwise json is unusable
                array('is_safe'=>array('html'))),
        );
    }

    public function toJsonFilter($data)
    {
        if(is_array($data)) {
            foreach($data as $key=>$item) {
                if(is_array($item)) {
                    $data[$key] = $this->toJsonFilter($item);
                }
                else if(is_object($item) && method_exists($item, 'toArray')) {
                    $data[$key] = $item->toArray();
                }
            }
        }
        return json_encode($data);
    }

    public function getName()
    {
        return 'json_extension';
    }
}
