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
        $moduleSystem = 'Modules\System\Http\Controllers';
        $moduleCertification = 'Modules\Certification\Http\Controllers';
        $moduleEmail = 'Modules\Email\Http\Controllers';
        $moduleMaster = 'Modules\Master\Http\Controllers';
        $data_menu = [
            [
                'name' => 'System',
                'parent' => NULL,
                'desc' => 'Management System',
                'active' => 'yes',
                'order' => 99999,
                'icon' => 'fas fa-cog',
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
                'icon' => 'fas fa-users',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleSystem . '\ManageUserController@index'],
                    ['name' => 'add', 'controller' => $moduleSystem . '\ManageUserController@create'],
                    ['name' => 'store', 'controller' => $moduleSystem . '\ManageUserController@store'],
                    ['name' => 'detail', 'controller' => $moduleSystem . '\ManageUserController@show'],
                    ['name' => 'edit', 'controller' => $moduleSystem . '\ManageUserController@edit'],
                    ['name' => 'update', 'controller' => $moduleSystem . '\ManageUserController@update'],
                    ['name' => 'delete', 'controller' => $moduleSystem . '\ManageUserController@destroy'],
                    ['name' => 'ajax_datagrid', 'controller' => $moduleSystem . '\ManageUserController@ajaxDatagrid'],
                    ['name' => 'ajax_banned', 'controller' => $moduleSystem . '\ManageUserController@ajaxBanned'],
                ]
            ],
            [
                'name' => 'Manage Group',
                'parent' => 1,
                'desc' => 'Mengatur Group dan permission',
                'active' => 'yes',
                'order' => '2',
                'icon' => 'fas fa-user-friends',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleSystem . '\ManageGroupController@index'],
                    ['name' => 'add', 'controller' => $moduleSystem . '\ManageGroupController@create'],
                    ['name' => 'store', 'controller' => $moduleSystem . '\ManageGroupController@store'],
                    ['name' => 'detail', 'controller' => $moduleSystem . '\ManageGroupController@show'],
                    ['name' => 'edit', 'controller' => $moduleSystem . '\ManageGroupController@edit'],
                    ['name' => 'update', 'controller' => $moduleSystem . '\ManageGroupController@update'],
                    ['name' => 'delete', 'controller' => $moduleSystem . '\ManageGroupController@destroy'],
                    ['name' => 'ajax_datagrid', 'controller' => $moduleSystem . '\ManageGroupController@ajaxDatagrid'],
                    ['name' => 'ajax_treegrid', 'controller' => $moduleSystem . '\ManageGroupController@ajaxTreegrid'],
                    ['name' => 'ajax_active', 'controller' => $moduleSystem . '\ManageGroupController@ajaxActive'],
                ]
            ],
            [
                'name' => 'Manage Menu',
                'parent' => 1,
                'desc' => 'Mengatur Menu',
                'active' => 'yes',
                'order' => '3',
                'icon' => 'fas fa-layer-minus',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleSystem . '\ManageMenuController@index'],
                    ['name' => 'add', 'controller' => $moduleSystem . '\ManageMenuController@create'],
                    ['name' => 'store', 'controller' => $moduleSystem . '\ManageMenuController@store'],
                    ['name' => 'detail', 'controller' => $moduleSystem . '\ManageMenuController@show'],
                    ['name' => 'edit', 'controller' => $moduleSystem . '\ManageMenuController@edit'],
                    ['name' => 'update', 'controller' => $moduleSystem . '\ManageMenuController@update'],
                    ['name' => 'delete', 'controller' => $moduleSystem . '\ManageMenuController@destroy'],
                    ['name' => 'ajax_treegrid', 'controller' => $moduleSystem . '\ManageMenuController@ajaxTreegrid'],
                    ['name' => 'ajax_data_icon', 'controller' => $moduleSystem . '\ManageMenuController@ajaxDataIcon'],
                    ['name' => 'ajax_active', 'controller' => $moduleSystem . '\ManageMenuController@ajaxActive'],
                ]
            ],
            [
                'name' => 'Manage Menu Action',
                'parent' => 1,
                'desc' => 'Mengatur Menu Aksi controller',
                'active' => 'no',
                'order' => '3',
                'icon' => 'fas fa-bars',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleSystem . '\ManageMenuActionController@index'],
                    ['name' => 'add', 'controller' => $moduleSystem . '\ManageMenuActionController@create'],
                    ['name' => 'store', 'controller' => $moduleSystem . '\ManageMenuActionController@store'],
                    ['name' => 'detail', 'controller' => $moduleSystem . '\ManageMenuActionController@show'],
                    ['name' => 'edit', 'controller' => $moduleSystem . '\ManageMenuActionController@edit'],
                    ['name' => 'update', 'controller' => $moduleSystem . '\ManageMenuActionController@update'],
                    ['name' => 'delete', 'controller' => $moduleSystem . '\ManageMenuActionController@destroy'],
                    ['name' => 'ajax_datagrid', 'controller' => $moduleSystem . '\ManageMenuActionController@ajaxDatagrid'],
                ]
            ],
            [
                'name' => 'Certification',
                'parent' => NULL,
                'desc' => 'Modul Sertifikasi',
                'active' => 'yes',
                'order' => '20',
                'icon' => 'fas fa-certificate',
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
                'icon' => 'fas fa-newspaper',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleCertification . '\RequestCertificateController@index'],
                ]
            ],
            [
                'name' => 'Verifikasi Sertifikasi',
                'parent' => 6,
                'desc' => 'Untuk Verifikator',
                'active' => 'yes',
                'order' => '2',
                'icon' => 'fas fa-check-circle',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleCertification . '\VerifCertificateController@index'],
                ]
            ],

            [
                'name' => 'Email',
                'parent' => NULL,
                'desc' => 'Modul Email',
                'active' => 'yes',
                'order' => '80',
                'icon' => 'fas fa-mailbox',
                'action' => [
                    ['name' => 'index', 'controller' => '#'],
                ]
            ],
            [
                'name' => 'Template',
                'parent' => 9,
                'desc' => 'Template scheduler email',
                'active' => 'yes',
                'order' => '1',
                'icon' => 'fas fa-mail-bulk',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleEmail . '\TemplateEmailController@index'],
                    ['name' => 'create', 'controller' => $moduleEmail . '\TemplateEmailController@create'],
                    ['name' => 'store', 'controller' => $moduleEmail . '\TemplateEmailController@store'],
                    ['name' => 'edit', 'controller' => $moduleEmail . '\TemplateEmailController@edit'],
                    ['name' => 'update', 'controller' => $moduleEmail . '\TemplateEmailController@update'],
                    ['name' => 'delete', 'controller' => $moduleEmail . '\TemplateEmailController@destroy'],
                    ['name' => 'preview', 'controller' => $moduleEmail . '\TemplateEmailController@previewEmail'],
                    ['name' => 'ajax', 'controller' => $moduleEmail . '\TemplateEmailController@ajax'],
                ]
            ],
            [
                'name' => 'Outbox',
                'parent' => 9,
                'desc' => 'Email keluar',
                'active' => 'yes',
                'order' => '2',
                'icon' => 'fas fa-inbox-out',
                'action' => [
                    ['name' => 'index', 'controller' => '#'],
                ]
            ],
            [
                'name' => 'System',
                'parent' => 11,
                'desc' => 'Email keluar melalui system otomatis',
                'active' => 'yes',
                'order' => '1',
                'icon' => 'fas fa-paper-plane',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleEmail . '\HistoryEmailSystemController@index'],
                    ['name' => 'ajax', 'controller' => $moduleEmail . '\HistoryEmailSystemController@ajax'],
                    ['name' => 'preview', 'controller' => $moduleEmail . '\HistoryEmailSystemController@previewEmail'],
                ]
            ],
            [
                'name' => 'Scheduler',
                'parent' => 11,
                'desc' => 'Email keluar melalui cronjob',
                'active' => 'yes',
                'order' => '1',
                'icon' => 'fas fa-paper-plane',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleEmail . '\HistoryEmailSchedulerController@index'],
                    ['name' => 'ajax', 'controller' => $moduleEmail . '\HistoryEmailSchedulerController@ajax'],
                    ['name' => 'preview', 'controller' => $moduleEmail . '\HistoryEmailSchedulerController@previewEmail'],
                ]
            ],
            [
                'name' => 'Master',
                'parent' => NULL,
                'desc' => 'Data Master',
                'active' => 'yes',
                'order' => '100',
                'icon' => 'fas fa-asterisk',
                'action' => [
                    ['name' => 'index', 'controller' => '#'],
                ]
            ],
            [
                'name' => 'Badan Hukum',
                'parent' => 14,
                'desc' => 'Data badan hukum',
                'active' => 'yes',
                'order' => '100',
                'icon' => 'fas fa-gavel',
                'action' => [
                    ['name' => 'index', 'controller' => $moduleMaster . '\BadanHukumController@index'],
                    ['name' => 'ajax', 'controller' => $moduleMaster . '\BadanHukumController@ajax'],
                    ['name' => 'create', 'controller' => $moduleMaster . '\BadanHukumController@create'],
                    ['name' => 'store', 'controller' => $moduleMaster . '\BadanHukumController@store'],
                    ['name' => 'edit', 'controller' => $moduleMaster . '\BadanHukumController@edit'],
                    ['name' => 'update', 'controller' => $moduleMaster . '\BadanHukumController@update'],
                    ['name' => 'destroy', 'controller' => $moduleMaster . '\BadanHukumController@destroy'],
                ]
            ],
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
