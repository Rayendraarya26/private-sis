<?php

namespace Modules\System\Http\Traits;

trait MenuTraits
{
    function buildTree(array $elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                    //$element['menu_name'] = sprintf("<i class='fas %s'></i> %s", $element['menu_icon'], $element['menu_name']);
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
