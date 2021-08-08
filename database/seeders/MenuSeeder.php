<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prefixSystem = 'Modules\System\Http\Controllers';
        $prefixCertification = 'Modules\Certification\Http\Controllers';
        $data_menu = [
            [
                'name' => 'System',
                'parent' => NULL,
                'desc' => 'Management System',
                'active' => 'yes',
                'order' => '99',
                'icon' => 'fa-cog',
                'action' => [
                    ['name' => 'index', 'controller' => '#'],
                ]
            ],
            [
                'name' => 'Manage User',
                'parent' => 1,
                'desc' => 'Mengatur crud user',
                'active' => 'yes',
                'order' => '1',
                'icon' => 'fa-users',
                'action' => [
                    ['name' => 'index', 'controller' => $prefixSystem . '\ManageUserController@index'],
                    ['name' => 'add', 'controller' => $prefixSystem . '\ManageUserController@create'],
                    ['name' => 'store', 'controller' => $prefixSystem . '\ManageUserController@store'],
                    ['name' => 'detail', 'controller' => $prefixSystem . '\ManageUserController@detail'],
                    ['name' => 'edit', 'controller' => $prefixSystem . '\ManageUserController@edit'],
                    ['name' => 'update', 'controller' => $prefixSystem . '\ManageUserController@update'],
                    ['name' => 'delete', 'controller' => $prefixSystem . '\ManageUserController@delete'],
                    ['name' => 'ajax_datagrid', 'controller' => $prefixSystem . '\ManageUserController@ajaxDatagrid'],
                    ['name' => 'ajax_banned', 'controller' => $prefixSystem . '\ManageUserController@ajaxBanned'],
                ]
            ],
            [
                'name' => 'Manage Group',
                'parent' => 1,
                'desc' => 'Mengatur Group dan permission',
                'active' => 'yes',
                'order' => '2',
                'icon' => 'fa-user-friends',
                'action' => [
                    ['name' => 'index', 'controller' => $prefixSystem . '\ManageGroupController@index'],
                    ['name' => 'add', 'controller' => $prefixSystem . '\ManageGroupController@create'],
                    ['name' => 'store', 'controller' => $prefixSystem . '\ManageGroupController@store'],
                    ['name' => 'detail', 'controller' => $prefixSystem . '\ManageGroupController@detail'],
                    ['name' => 'edit', 'controller' => $prefixSystem . '\ManageGroupController@edit'],
                    ['name' => 'update', 'controller' => $prefixSystem . '\ManageGroupController@update'],
                    ['name' => 'delete', 'controller' => $prefixSystem . '\ManageGroupController@delete'],
                    ['name' => 'ajax_datagrid', 'controller' => $prefixSystem . '\ManageGroupController@ajaxDatagrid'],
                    ['name' => 'ajax_treegrid', 'controller' => $prefixSystem . '\ManageGroupController@ajaxTreegrid'],
                    ['name' => 'ajax_active', 'controller' => $prefixSystem . '\ManageGroupController@ajaxActive'],
                ]
            ],
            [
                'name' => 'Manage Menu',
                'parent' => 1,
                'desc' => 'Mengatur Menu',
                'active' => 'yes',
                'order' => '3',
                'icon' => 'fa-bars',
                'action' => [
                    ['name' => 'index', 'controller' => $prefixSystem . '\ManageMenuController@index'],
                    ['name' => 'add', 'controller' => $prefixSystem . '\ManageMenuController@create'],
                    ['name' => 'store', 'controller' => $prefixSystem . '\ManageMenuController@store'],
                    ['name' => 'detail', 'controller' => $prefixSystem . '\ManageMenuController@detail'],
                    ['name' => 'edit', 'controller' => $prefixSystem . '\ManageMenuController@edit'],
                    ['name' => 'update', 'controller' => $prefixSystem . '\ManageMenuController@update'],
                    ['name' => 'delete', 'controller' => $prefixSystem . '\ManageMenuController@delete'],
                    ['name' => 'ajax_treegrid', 'controller' => $prefixSystem . '\ManageMenuController@ajaxTreegrid'],
                    ['name' => 'ajax_active', 'controller' => $prefixSystem . '\ManageMenuController@ajaxActive'],
                ]
            ],
            [
                'name' => 'Manage Menu Action',
                'parent' => 1,
                'desc' => 'Mengatur Menu Aksi controller',
                'active' => 'no',
                'order' => '3',
                'icon' => NULL,
                'action' => [
                    ['name' => 'index', 'controller' => $prefixSystem . '\ManageMenuActionController@index'],
                    ['name' => 'add', 'controller' => $prefixSystem . '\ManageMenuActionController@create'],
                    ['name' => 'store', 'controller' => $prefixSystem . '\ManageMenuActionController@store'],
                    ['name' => 'detail', 'controller' => $prefixSystem . '\ManageMenuActionController@detail'],
                    ['name' => 'edit', 'controller' => $prefixSystem . '\ManageMenuActionController@edit'],
                    ['name' => 'update', 'controller' => $prefixSystem . '\ManageMenuActionController@update'],
                    ['name' => 'delete', 'controller' => $prefixSystem . '\ManageMenuActionController@delete'],
                    ['name' => 'ajax_datagrid', 'controller' => $prefixSystem . '\ManageMenuActionController@ajaxDatagrid'],
                    ['name' => 'ajax_active', 'controller' => $prefixSystem . '\ManageMenuActionController@ajaxActive'],
                ]
            ],
            [
                'name' => 'Certification',
                'parent' => NULL,
                'desc' => 'Modul Sertifikasi',
                'active' => 'yes',
                'order' => '20',
                'icon' => 'fa-certificate',
                'action' => [
                    ['name' => 'index', 'controller' => '#'],
                ]
            ],
            [
                'name' => 'Permohonan Sertifikasi',
                'parent' => 6,
                'desc' => 'Untuk User',
                'active' => 'yes',
                'order' => '1',
                'icon' => 'fa-newspaper',
                'action' => [
                    ['name' => 'index', 'controller' => $prefixCertification . '\RequestCertificateController@index'],
                ]
            ],
            [
                'name' => 'Verifikasi Sertifikasi',
                'parent' => 6,
                'desc' => 'Untuk Verifikator',
                'active' => 'yes',
                'order' => '2',
                'icon' => 'fa-check-circle',
                'action' => [
                    ['name' => 'index', 'controller' => $prefixCertification . '\VerifCertificateController@index'],
                ]
            ],
            //[
            //    'name' => 'Notification',
            //    'parent' => NULL,
            //    'desc' => 'Membaca notifikasi',
            //    'active' => 'no',
            //    'order' => '100',
            //    'icon' => NULL,
            //    'action' => [
            //        ['name' => 'index', 'controller' => '/notification'],
            //        ['name' => 'open', 'controller' => '/notification/open/{id}'],
            //        ['name' => 'readAll', 'controller' => '/notification/mark-as-read'],
            //    ]
            //],
            //[
            //    'name' => 'Account',
            //    'parent' => NULL,
            //    'desc' => 'Melihat detail bio',
            //    'active' => 'no',
            //    'order' => '100',
            //    'icon' => NULL,
            //    'action' => [
            //        ['name' => 'index', 'controller' => '/account/profile'],
            //        ['name' => 'changePassword', 'controller' => '/account/change-password'],
            //    ]
            //],
        ];
        DB::transaction(function () use ($data_menu) {
            foreach ($data_menu as $dm) {
                DB::table('sys_menu')->insert([
                    'menu_parent_id' => $dm['parent'],
                    'menu_name' => $dm['name'],
                    'menu_desc' => $dm['desc'],
                    'menu_is_active' => $dm['active'],
                    'menu_icon' => $dm['icon'],
                    'menu_order' => $dm['order'],
                    'menu_created_at' => date("Y-m-d H:i:s"),
                ]);

                $menu_id = DB::getPdo()->lastInsertId();

                foreach ($dm['action'] as $aksi) {
                    DB::table('sys_menu_action')->insert([
                        'action_menu_id' => $menu_id,
                        'action_name' => $aksi['name'],
                        'action_controller' => $aksi['controller'],
                    ]);
                }
            }

        });
    }
}
