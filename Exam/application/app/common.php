<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/24
 * Time: 9:32 AM
 */

/**
 * 如果不是POST请求
 * @param $request
 */
function noIsPost($request, $url)
{
    if (!in_array($url, config('noPost'))) {
        /** 如果不是post请求 **/
        if (!$request->isPost()) {
            //exit 不可以换成return 不然不能正常返回
            result('40001');
        }
    }
}


/**
 * 给url路径加域名前缀的
 * @param array $arr
 * @param string $url url名
 * @param string $num even多维数组 odd一维数组
 * @return mixed 加完路径的数组
 */
function addPath($arr, $url, $num = 'even', $path = 'APP_PATH')
{
    if ($num == 'odd') {
        if (!empty($arr[$url])) {
            $arr[$url] = config($path) . $arr[$url];
        }
    } else {
        for ($i = 0; $i < count($arr); $i++) {
            if (!empty($arr[$i][$url])) {
                $arr[$i][$url] = config($path) . $arr[$i][$url];
            }
        }
    }
    return $arr;
}


/**
 * 获取token
 * @return string
 * @user 李海江 2018/11/2~9:24 AM
 */
function appGetToken()
{
    $token = \think\Request::instance()->header('token');
    return $token;
}

/**
 * 创建token
 * @return string $token
 */
function create_token()
{
    return md5(uniqid() . config('salt'));
}

/**
 * 获取uid
 * @return mixed
 * @throws \think\exception\DbException
 * @user 李海江 2018/11/13~4:44 PM
 */
function appGetUid()
{
    $uid = \app\common\service\UserLogin::getUid(['token' => appGetToken()]);
    return $uid;
}

/**
 * model返回给控制器状态
 * @param int $id
 * @param bool $flag
 * @param string $data
 * @return array
 * @user 李海江 2018/10/25~4:38 PM
 */
function modelReturn($id = '', $flag = false, $data = [])
{
    return ['id' => $id, 'flag' => $flag, 'data' => $data];
}