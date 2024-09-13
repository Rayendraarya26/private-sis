<?php

namespace Modules\Auth\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                } else {
                    $element->children = [];
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public function setAccess(int $groupID, string $groupName)
    {
        $group_selected = $groupID;
        $group_selected_name = $groupName;
        $dataMenu = DB::select(DB::RAW("
                    SELECT DISTINCT menu_name, menu_id, menu_parent_id, menu_icon, sma.action_controller
                    FROM sys_menu
                             JOIN sys_menu_action sma ON sys_menu.menu_id = sma.actiON_menu_id AND sma.action_name = 'index'
                             JOIN sys_group_permission sgp ON sma.action_id = sgp.action_id
                    WHERE sgp.group_id = '$group_selected' AND menu_is_active = 'yes'
                    ORDER BY menu_parent_id, menu_order, menu_name
                "));
        $menuAction = [];
        $permission = DB::select(DB::RAW("
                    SELECT action_controller FROM sys_group_permission
                    JOIN sys_menu_action sma ON sys_group_permission.action_id = sma.action_id
                    WHERE group_id = '$group_selected'
                "));
        foreach ($permission as $p) {
            $menuAction[] = $p->action_controller;
        }
        $goupAvailable = [];
        foreach (Auth::user()->user_group as $g) {
            $goupAvailable[] = [
                'group_id'   => $g->ug_group_id,
                'group_name' => $g->group->group_name,
            ];
        }

        $dataSession = [
            'group_selected' => $group_selected,
            'group_selected_name' => $group_selected_name,
            'group_available' => $goupAvailable,
            'permission' => $menuAction,
            'menu' => $this->buildTree($dataMenu),
        ];

        session($dataSession);
    }
}
