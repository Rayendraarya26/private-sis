<?php

namespace Modules\System\Http\Traits;

use App\Models\BbkkpSis\SysGroupPermission;

trait GroupTraits{
    function buildTree($elements, $parentId = 0, $groupId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element->menu_parent_id == $parentId) {
                if ($element->menu_parent_id != 0) {
                    $element->state = 'closed';
                }
                $children = $this->buildTree($elements, $element->menu_id, $groupId);
                if ($children) {
                    $element->children = $children;
                } else {
                    $children = [];
                    foreach ($element->action as $action) {
                        $x = [
                            'menu_id' => $element->menu_id . '-' . $action->action_id,
                            'menu_parent_id' => $element->menu_parent_id,
                            'menu_name' => $action->action_name,
                            'menu_controller' => $action->action_controller,
                            'action_id' => $action->action_id,
                        ];
                        if ($groupId != 0) {
                            $exist = SysGroupPermission::where("group_id", $groupId)->where("action_id", $action->action_id)->first();
                            //dump($exist);
                            if ($exist) {
                                $x['checked'] = true;
                            }
                        }
                        array_push($children, $x);
                    }
                    $element->state = 'closed';
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
