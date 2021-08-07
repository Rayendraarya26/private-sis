<?php

namespace Modules\Auth\Http\Traits;

trait AuthTraits
{
    public function buildTree(array $elements, $parentId = 0): array
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element->menu_parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->menu_id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
