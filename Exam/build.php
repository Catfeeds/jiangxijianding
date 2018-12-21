<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 生成应用公共文件
    '__file__' => ['common.php', 'config.php', 'database.php'],

    // 定义生成所有模块的model
    'common'    =>[
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'view','model'],
        'controller' => [],
        'view' => [],
        'model' => [
            'Article',
            'ArticleLog',
            'ExamPlan',
            'ExamWork',
            'ExamTask',
            'ExamApply',
            'ExamApplyWork',
            'ExamEnroll',
            'ExamOrder',
            'ExamOrderDetail',
            'ExamOrderPay',
            'ExamStaffLog',
            'Link',
            'Subject',
            'SubjectLevel',
            'Work',
            'WorkDirection',
            'WorkLevel',
            'WorkType',
            'Admin',
            'AdminRole',
            'Expert',
            'ExpertWorkLevel',
            'Organize',
            'OrganizeWorkLevel',
            'Role',
            'RoleMenu',
            'ApplyApprove',
            'Instructor',
            'Inventory',
            'Range',
            'RangePoint',
            'Jury',
            'Menu',
        ],
    ],

    // 定义生成所有controller
    'api'    =>[
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'view'],
        'view' => [],
        'controller' => [
            'ArticleController',
            'ArticleLogController',
            'ExamPlanController',
            'ExamWorkController',
            'ExamTaskController',
            'ExamApplyController',
            'ExamApplyWorkController',
            'ExamEnrollController',
            'ExamOrderController',
            'ExamOrderDetailController',
            'ExamOrderPayController',
            'ExamStaffLogController',
            'LinkController',
            'SubjectController',
            'SubjectLevelController',
            'WorkController',
            'WorkDirectionController',
            'WorkLevelController',
            'WorkTypeController',
            'AdminController',
            'AdminRoleController',
            'ExpertController',
            'ExpertWorkLevelController',
            'OrganizeController',
            'OrganizeWorkLevelController',
            'RoleController',
            'RoleMenuController',
            'ApplyApproveController',
            'InstructorController',
            'InventoryController',
            'RangeController',
            'RangePointController',
            'JuryController',
            'MenuController',
        ],
    ],

    // 定义发布考核计划模块生成controller
//    'examPlan'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['controller', 'view'],
//        'controller' => [
//            'Organize',
//            'OrganizeWorkLevel',
//            'ExamApply',
//            'ExamApplyWork',
//            'ApplyApprove',
//            'ExamPlan',
//            'ExamWork',
//            'Subject',
//            'SubjectLevel',
//            'WorkType',
//            'Work',
//            'WorkDirection',
//            'WorkLevel',
//            'Article',
//            'ArticleLog',
//        ],
//        'view'       => [],
//    ],
//
//    // 定义报名模块生成controller
//    'enroll'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['controller', 'view'],
//        'controller' => [
//            'ExamEnroll',
//            'UserLogin',
//        ],
//        'view'       => [],
//    ],
//
//    // 定义资质审核模块生成controller
//    'authorization'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['controller', 'view'],
//        'controller' => [
//            'WorkEnroll',
//            'WorkEnrollLog',
//        ],
//        'view'       => [],
//    ],
//
//    // 定义考试缴费模块生成controller
//    'examCharge'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['controller', 'view'],
//        'controller' => [
//            'ExamEnroll',
//            'ExamOrder',
//            'ExamOrderDetail',
//            'ExamOrderPay',
//        ],
//        'view'       => [],
//    ],
//
//    // 定义编排考场模块生成controller
//    'examArrange'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['controller', 'view'],
//        'controller' => [
//            'Inventory',
//            'Range',
//            'RangePoint',
//        ],
//        'view'       => [],
//    ],
//
//    // 定义组织册编排模块生成controller
//    'adminArrange'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['controller', 'view'],
//        'controller' => [
//            'ExamTask',
//            'Admin',
//            'AdminRole',
//            'Expert',
//            'ExpertWorkLevel',
//            'Jury',
//            'Instructor',
//            'ExamStaffLog',
//        ],
//        'view'       => [],
//    ],
];
