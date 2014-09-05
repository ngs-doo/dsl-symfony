<?php
namespace NGS\Symfony\Util;

use NGS\Symfony\Util\Paginator;
use Symfony\Component\HttpFoundation\Request;
use NGS\Patterns\Specification;

abstract class Grid
{
    // Helper for generating grid with paginator/search from specification
    public static function fromSpecification(Specification $specification, Request $request, $forceSearch = false)
    {
        if($forceSearch === false && count($request->query) == 0)
            return array(
                'search'    => $specification->toArray(),
                'items'     => array()
            );

        if ($request->query->get('_pagination') === 'off')
            return array(
                'search' => $specification->toArray(),
                'items' => $specification->search()
            );

        $paginator = new Paginator(
            null,
            $request->get('page'),
            $request->get('items'));

        $items = $specification->search($paginator->getPerPage(), $paginator->getStart());

        $paginator->setCount($specification->count());

        return array(
            'search'    => $specification->toArray(),
            'paginator' => $paginator,
            'items'     => $items
        );
    }
}
