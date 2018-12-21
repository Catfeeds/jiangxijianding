<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/25
 * Time: 4:46 PM
 */

namespace app\common\validate;

use think\Validate;

/**
 * APP验证
 * Class User
 * @package app\api\validate
 */
class App extends Validate
{
    /** 规则 **/
    protected $rule = [
        ['id_card', 'require', '40004'],
        ['password', 'require|length:32', '40005|40006'],
        ['phone', '/^1[3456789][0-9]{9}$/', '40013'],
    ];
    /** 场景设置 **/
    protected $scene = [
        'login' => ['id_card', 'password'],
        'appSendMsg' => ['phone'],
    ];
}