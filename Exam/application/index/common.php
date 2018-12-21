<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/12/10
 * Time: 3:41 PM
 */

/**
 * 创建订单号
 * @return string
 * @user 李海江 2018/12/18~11:52 AM
 */
function make_order_num()
{
    $order_num = 'JX' . date('ymd_His') . rand(10000, 99999);
    return $order_num;
}

/**
 * 响应参数转换成数组
 * @param $respdata
 * @return array
 * @user 李海江 2018/12/18~11:51 AM
 */
function getresp($respdata)
{
    $result = explode("\n", $respdata);
    $output = array();
    foreach ($result as $data) {
        $arr             = explode('=', $data);
        $output[$arr[0]] = urldecode($arr[1]);
    }
    return $output;
}

/**
 * 生成本地签名hmac(不适用于回调通知)
 * @param $data
 * @return string
 * @user 李海江 2018/12/18~11:51 AM
 */
function HmacLocal($data)
{
    $text = "";
    while (list($key, $value) = each($data)) {
        if (isset($key) && $key != "hmac" && $key != "hmac_safe") {
            $text .= $value;
        }
    }
    return HmacMd5($text, config('yeepay.merchantKey'));
}

/**
 * 生成本地的安全签名数据
 * @param $data
 * @return string
 * @user 李海江 2018/12/18~11:52 AM
 */
function gethamc_safe($data)
{
    $text = "";
    while (list($key, $value) = each($data)) {
        if ($key != "hmac" && $key != "hmac_safe" && $value != null) {

            $text .= $value . "#";
        }
    }
    $text1 = rtrim(trim($text), '#');;
    return HmacMd5($text1, config('yeepay.merchantKey'));
}

/**
 * 生成hmac
 * @param $data
 * @param $key
 * @return string
 * @user 李海江 2018/12/18~11:51 AM
 */
function HmacMd5($data, $key)
{
    $key  = iconv("GBK", "UTF-8", $key);
    $data = iconv("GBK", "UTF-8", $data);
    $b    = 64; // byte length for md5
    if (strlen($key) > $b) {
        $key = pack("H*", md5($key));
    }
    $key    = str_pad($key, $b, chr(0x00));
    $ipad   = str_pad('', $b, chr(0x36));
    $opad   = str_pad('', $b, chr(0x5c));
    $k_ipad = $key ^ $ipad;
    $k_opad = $key ^ $opad;
    return md5($k_opad . pack("H*", md5($k_ipad . $data)));
}