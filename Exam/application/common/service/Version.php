<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/23
 * Time: 5:26 PM
 */

namespace app\common\service;

use app\common\model\Version as MVersion;

/**
 * Class Version
 * @package app\common\service
 */
class Version extends MVersion
{

    /**
     * 检查app版本
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/23~5:45 PM
     */
    function checkversion($data, $field = [], $order = 'num desc')
    {
        $version = $this->BaseFind(['type' => $data['type'], 'app_type' => $data['app_type']], $field, $order);
        if ($version->num > $data['num']) {
            $data = array(
                'latestName' => $version['name'],
                'latestVersion' => $version->num,
                'update' => true,
                'url' => $version->url,
                'mustupdate' => empty($version->mustupdate) ? false : true,
            );
            return ['flag' => true, 'data' => $data];
        } else {
            return ['flag' => false];
        }
    }
}