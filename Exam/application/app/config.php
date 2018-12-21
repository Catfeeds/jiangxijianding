<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/24
 * Time: 9:32 AM
 */

return [
    'APP_PATH' => 'http://local.jiangxi.com',
    'OFFICE_PATH' => 'http://view.officeapps.live.com/op/view.aspx?src=',

    //不需要Token验证的controller . action    注意全小写
    'noToken' => array(
        //文件
        'learning/file',
        //登录
        'userlogin/index',
        'cms/content',
        //关于我们
        'about/us',
        //轮播图
        'index/advert',
        //首页新闻
        'cms/news',
        //首页新闻
        'cms/lists',
        //考试计划
        'cms/headerlist',
        //报考查询
        'work/lists',
        'work/dirandlevel',
        'work/search',
        'work/getrole',
        //忘记密码
        'user/password',
        //常见问题
        'about/faq',
        'index/sendmessage',
        'index/checkmessage',
        'index/version',
    ),
    //不需要post请求的
    'noPost' => array(
        //h5新闻详情页
        'cms/content',
        //关于我们
        'about/us',
        'learning/file',
    ),

    //最新通知栏目id
    'news' => 7,
    'newslimit' => 2,
    //新闻动态栏目id
    'dynamic' => 9,
    'dynamiclimit' => 3,
    //检定计划栏目id
    'jdjh' => 37,
    //C类工种的id
    'type_c' => 3,
];