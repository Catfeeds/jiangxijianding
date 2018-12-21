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
use think\Route;





















// route::rule('admin', 'admin/user/login');                  //后台登录页面
// route::rule('adminIndex', 'admin/index/index');             //后台首页
// route::rule('checkOut', 'admin/user/loginOut');            //退出登录
// route::rule('updatepage', 'admin/index/updatepage');            //修改页面 修改密码
// route::rule('infopage', 'admin/index/infopage');            //详细信息页面 修改详细信息
// route::rule('mechanism', 'admin/mechanism/index');            //机构管理首页
// route::rule('addorganize', 'admin/mechanism/addorganize');            //添加机构
// route::rule('delorganize', 'admin/mechanism/delorganize');            //删除机构
// route::rule('upOrganize', 'admin/mechanism/uporganize');            //修改机构
// route::rule('batchOrganize', 'admin/mechanism/batchorganize');            //批量添加

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
