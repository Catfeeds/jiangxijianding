<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------


/**
 * @param $phone
 * @param string $template
 * @return array
 * @user 李海江 2018/11/23~4:49 PM
 */
function sendMsg($phone, $template = '0')
{
    $res = \SendMsm\Msm::sendMessage($phone, $template);
    return $res;
}

/***
 * 返回状态码信息
 * @param string $code 状态码
 * @param bool $type json类型
 */
function result($code, $type = true)
{

    if ($code == '40002') {
        header('isTokenValid: false');
        exit();
    }
    //exit 不可以换成return 不然不能正常返回
    header('isTokenValid: true');
    exit(json_encode(config('app.' . $code), $type));
}

/**
 * 检测验证码是否和服务器端一致
 * @param $code
 * @param $phone
 * @return bool
 * @user 李海江 2018/11/23~4:49 PM
 */
function check_code($code, $phone)
{

    if ($code != \think\Cache::get($phone) || $code == null) {
        return false;
    } else {
        //当验证码验证通过后从缓存中清除code
        \think\Cache::rm($phone);
        return true;
    }
}

// 应用公共文件
/**
 * 检查条件的规范性
 * @param array $field
 * @param array $param
 * @param array $paginate
 * @return bool|string
 */
function checkArray($field = [], $param = [], $paginate = [])
{
    $layout_array = "条件必须为数组";
    if (!empty($field) && !is_array($field)) {
        return 'field' . $layout_array;
    } elseif (!empty($param) && !is_array($param)) {
        return 'param' . $layout_array;
    } elseif (!empty($paginate) && !is_array($paginate)) {
        return 'paginate' . $layout_array;
    } else {
        return false;
    }
}


/**
 * @param $value
 * @param $array
 * @return bool
 */
function deep_in_array($value, $array)
{
    foreach ($array as $item) {
        if (!is_array($item)) {
            if ($item == $value) {
                return true;
            } else {
                continue;
            }
        }
        if (in_array($value, $item)) {
            return true;
        } else if (deep_in_array($value, $item)) {
            return true;
        }
    }
    return false;
}


/**
 * 返回layui提示信息
 * @param int $code
 * @param string $msg
 * @return array
 * @user 李海江 2018/8/29~下午9:19
 */
function layuiMsg($code = 0, $msg = "", $data = [])
{
    $layui_array = array(
        'code' => (int)$code,
        'msg'  => $msg,
        'data' => $data,
    );

    return $layui_array;
}


/**
 * @param $arr
 * @return array
 */
function repeat_arr($arr)
{
    //数组去重
    $unique_arr = array_unique($arr);
    // 获取重复数据的数组
    $repeat_arr = array_diff_assoc($arr, $unique_arr);
    return $repeat_arr;
}


/**
 * 创建树形数据
 * @param $arr
 * @param int $parent_id
 * @return array
 * @user 李海江 2018/8/31~下午6:57
 */
function getTree($arr, $parent_id = 0)
{
    $temp = array();
    foreach ($arr as $v)
        if ($v['parent_id'] == $parent_id) {
            $t = getTree($arr, $v['id']);
            if ($t) $v['son'] = $t;
            $temp[] = $v;
        }
    return $temp;
}

/**
 * 创建树形数据 带横线
 * @param $data
 * @param int $parent
 * @param int $level
 * @return array
 * @user 李海江 2018/9/2~下午9:33
 */
function getTreeData($data, $parent = 0, $level = 0)
{
    static $return;
    foreach ($data as $v) {
        if ($v['parent_id'] == $parent) {
            $v['level'] = $level;
            $v->title   = '|' . str_repeat('____', $level) . $v->title;
            $return[]   = $v;
            getTreeData($data, $v['id'], $level + 1);
        }
    }
    return $return;
}

/**
 * 判断第一个数组 和第二个数组如果相同返回数组 不同返回false
 *
 * 第一个数组
 * Array ( [0] => Array ( [exam_plan_id] => 2 [title] => 科目一 [wtname] => C类 [work_type] => 3 [exam_work_id] => 3 [wname] => 计算机程序设计员 [work_id] => 5 [wllevel] => 1 [work_level_id] => 6 [wdid] => 5 [wdname] => 计算机设计 )...)
 *
 * 第二个数组
 * Array ( [exam_plan_id] => 2 [wtname] => C类 [wname] => 计算机程序设计员 [wdname] => 计算机设计 [wllevel] => 2 )
 *
 * 成功案例
 * @param $parents
 * @param $searched
 * @return bool|int|string
 */
function multidimensional_search($parents, $searched)
{
    if (empty($searched) || empty($parents)) {
        return false;
    }

    foreach ($parents as $key => $value) {
        $exists = true;
        foreach ($searched as $skey => $svalue) {
            $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
        }
        if ($exists) {
            return $key;
        }
    }
    return false;
}

/** 报名*/
function search($parents, $searched)
{
    if (empty($searched) || empty($parents)) {
        return false;
    }

    foreach ($parents as $key => $value) {
        $exists = true;
        if (($parents[$key]['wname'] == $searched['wname']) && ($parents[$key]['wdname'] === $searched['wdname']) && ($parents[$key]['wllevel'] == $searched['wllevel'])) {
            // echo $key;die;
            return $key;
        }

    }

    return false;
}

/**
 * 根据组织类型获取exam_work和exam_work_type的状态
 */
function getTypeBy($type)
{
    $Where = [];
    switch ($type) {
        case
        1:
            $Where['exam_work.type']       = config('ExamWorkType.station');  //鉴定所
            $Where['exam_work_level.type'] = config('ExamWorkType.station');  //鉴定所
            break;
        case
        2:
            $Where['exam_work.type']       = config('ExamWorkType.school');  //院校
            $Where['exam_work_level.type'] = config('ExamWorkType.school');  //院校
            break;
        case
        3:
            $Where['exam_work.type']       = config('ExamWorkType.organize');  //机构
            $Where['exam_work_level.type'] = config('ExamWorkType.organize');  //机构
            break;
    }

    return $Where;
}

/**
 * 不同环境下获取真实的IP
 * @return array|false|string
 * @user 朱颖 {2018/10/26}~{10:05}
 */
function getip()
{
    static $realip;
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    return $realip;
}


/**
 * 根据某字段 去重
 * @param $arr
 * @param $key 数组
 * @return array
 */
function array_unique_key($arr = [], $key = '')
{
    $res = array();
    foreach ($arr as $k => $value) {
        if (isset($res[$value[$key]])) {
            unset($value[$key]);
        } else {
            $res[$value[$key]] = $value;
        }
    }
    return $res;
}

/**
 * @param $arr
 * @param $para
 * @return array
 */
function array_where($arr, $para)
{
    $eachData = $arr;
    $newArr   = [];
    foreach ($eachData as $dataK => $dataV) {
        $isSuccess = true;
        foreach ($para as $paraK => $paraV) {
            if (!key_exists($paraK, $dataV)) {
                $isSuccess = false;
            }
            if ($dataV[$paraK] != $paraV) {
                $isSuccess = false;
            }
        }
        if ($isSuccess) {
            $newArr[] = $dataV;
        }
    }
    return $newArr;
}

//随机生成条形码
function get_rnd_id()
{
    $m = explode(' ', microtime());
    return base_convert(($m[1] . ($m[0] * 100000000)), 10, 32);
}


/**
 * 返回数组中指定多列
 *
 * @param  Array $input 需要取出数组列的多维数组
 * @param  String $keys 要取出的列名数组
 * @param  String $index_key 作为返回数组的索引的列
 * @return Array
 */
function array_columns($input, $keys = [], $index_key = null)
{
    $result = array();

    //$keys =isset($column_keys)? explode(',', $column_keys) : array();

    if ($input) {
        foreach ($input as $k => $v) {

            // 指定返回列
            if ($keys) {
                $tmp = array();
                foreach ($keys as $key) {
                    $tmp[$key] = $v[$key];
                }
            } else {
                $tmp = $v;
            }

            // 指定索引列
            if (isset($index_key)) {
                $result[$v[$index_key]] = $tmp;
            } else {
                $result[] = $tmp;
            }

        }
    }

    return $result;
}

/**
 * 根据参数自动返回不为空的搜索条件 搜索条件闪存在session里
 * @param $param
 * @param string $dengyudecanshu
 * @return array
 * @user 李海江 2018/10/9~11:40 AM
 */
function searchLike($param, $dengyudecanshu = '')
{
    //公共查询条件
    $map = array();
    foreach ($param as $k => $v) {
        if (in_array($k, ['count', 'limit'])) {
            unset($param[$k]);
        } elseif ($k == $dengyudecanshu) {
            $map[$k] = $v;
        } elseif (!empty($v) && $k != 'page' && $k != $dengyudecanshu) {
            $map[$k] = ['like', '%' . $v . '%'];
        }
    }

    \Think\Session::flash('search', $param);
    return $map;
}


/**
 * 一个一维数组 , 把其中某一个值拆分成一个数组 , 把原数组里的其他值当成这个新数组里的每个元素的同级
 * 例子    $arr = ['pid'=>1, name='张三,李四,...', status=>1];
 * 变成    $arr = [['pid'=>1,name='张三',status=>1],['pid'=>1,name='李四',status=>1],...]
 * @param $data
 * @param $key
 * @param $onevalue
 * @param $onekey
 * @return array
 * @user 李海江 2018/10/10~4:05 PM
 */
function array_insert($data, $name)
{
    $arrayName = explode("\n", $data[$name]);
    unset($data[$name]);
    $array = array();
    $i     = 0;
    foreach ($arrayName as $k => $v) {
        foreach ($data as $kk => $vv) {
            $array[$i][$kk]   = $vv;
            $array[$i][$name] = $v;
        }
        ++$i;
    }
    return $array;
}

/**
 * 一维数组 和 字符串 合并成一个二维数组
 * @param $data
 * @param $stringKey
 * @param $string
 * @return mixed
 * @user 李海江 2018/10/19~2:07 PM
 */
function array_string_megre($data, $dataKey, $string, $stringKey)
{
    $array = [];
    for ($i = 0; $i < count($data); $i++) {
        $array[$i][$dataKey]   = $data[$i];
        $array[$i][$stringKey] = $string;
    }
    return $array;
}


/**
 * 获取uid
 * @return mixed
 * @user 李海江 2018/10/18~11:59 AM
 */
function getUid()
{
    return session('adminuser.id');
}

/**
 * 获取username
 * @return mixed
 * @user 李海江 2018/10/18~11:59 AM
 */
function getUsername()
{
    return session('adminuser.username');
}

/**
 * 获取所属机构的省市县类型
 * @user 李海江 2018/10/29~11:13 AM
 */
function getCenterType()
{
    return session('adminuser.center_type');
}

/**
 * 获取所属机构的id
 * @return mixed
 * @user 李海江 2018/10/29~2:15 PM
 */
function getCenterId()
{
    return session('adminuser.center_id');
}

/***
 * 通过某个键 对比两个二维数组里是否存在另外一个二维数组 , 如果有相同的加checked
 * @param array $firstArray 第一个二维数组
 * @param array $secondArray 第二个二维数组
 * @param string $key 需要对比的键名 默认为id
 * @return mixed 返回第一个数组
 * @user 李海江 2018/10/19~10:47 AM
 */
function contrastArray($firstArray, $secondArray, $key = 'id')
{
    for ($i = 0; $i < count($firstArray); $i++) {
        for ($j = 0; $j < count($secondArray); $j++) {
            if ($firstArray[$i][$key] == $secondArray[$j][$key]) {
                $firstArray[$i]['checked'] = true;
            }
        }
    }
    return $firstArray;
}

/**
 * layui复选框返回数组
 * @param $array
 * @param $key
 * @return array
 * @user 李海江 2018/10/23~3:22 PM
 */
function layuiCheckboxToArray($array, $key)
{
    $data = array_keys($array[$key]);
    return $data;
}

/**
 * 返回当前人的所有rules
 * @return array
 * @user 李海江 2018/10/23~3:24 PM
 */
function getMenu($uid)
{
    $auth     = new \Auth\Auth();
    $groupsId = $auth->getAuthListId($uid);
    return $groupsId;
}

/**
 * @param $plan int
 * @param $organize int 考点
 * @param $enroll int
 * @param $type 准考证 card 桌贴 deskpaste 清考册 clear 座位排布图 seat
 * 获取考务文件名
 */
function getFileName($plan, $organize, $enroll, $type)
{
    $file = '';
    if ($type && $plan && $organize && $enroll) {
        $file = 'download/' . $type . '/' . $plan . '/' . $organize . '/' . $plan . '_' . $organize . '_' . $enroll . '_' . $type . '.pdf';
    } elseif ($type && $plan && $organize) {
        $file = 'download/' . $type . '/' . $plan . '/' . $organize;
    } elseif ($plan && $type) {
        $file = 'download/' . $type . '/' . $plan;
    } elseif ($type) {
        $file = 'download/' . $type;
    }
    return $file;
}

/**
 * @param $file
 * @param string $name
 * 下载文件和文件夹
 */
function downFile($file = '', $name = 'file')
{
    if (is_dir($file)) {
        createZip($file, $name);
        if (file_exists($name . ".zip")) {
            header("Content-Type: application/zip");
            header("Content-Length: " . filesize($name . ".zip"));
            header("Content-Disposition: attachment; filename=\"" . basename($name . ".zip") . "\"");
            readfile($name . ".zip");
        } else {
            return "404";
        }
    } else {
        if (file_exists($file)) {
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $name);
            readfile($file);
        } else {
            return "404";
        }
    }
}

/**
 * @param $file
 * @param $name
 * @return bool
 * 生成zip文件
 */
function createZip($file, $name)
{
    $files = my_dir($file);
    return downZip($name, $file, $files);
}

/**
 * @param $dir
 * @return array
 * 遍历文件夹
 */
function my_dir($dir)
{
    $files = array();
    if (@$handle = opendir($dir)) {
        while (($file = readdir($handle)) !== false) {
            if ($file != ".." && $file != ".") {
                if (is_dir($dir . "/" . $file)) {
                    $files[$file] = my_dir($dir . "/" . $file);
                } else {
                    $files[] = $file;
                }
            }
        }
        closedir($handle);
        return $files;
    }
}

/**
 * @param $file
 * @param $files
 * @return bool
 * 生成zip包
 */
function downZip($name, $file, $files)
{
    $zip = new \ZipArchive();
    if ($zip->open($name . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        if (addFile($file, $files, $zip)) {
            $zip->close();//关闭资源句柄
            return true;
        } else {
            $zip->close();//关闭资源句柄
            return false;
        }
    } else {
        return false;
    }
}

/**
 * @param $file
 * @param $files
 * @param $zip
 * @return bool
 * 向zip中添加文件
 */
function addFile($file, $files, $zip)
{
    try {
        foreach ($files as $k => $v) {
            if (is_array($v)) {
                addFile($file . '/' . $k, $v, $zip);
            } else {
                $zip->addFile($file . '/' . $v);
            }
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}


/**
 * 身份证真实性验证规则
 * @param $id_card
 * @return bool
 * @user 李海江 2018/12/11~1:41 PM
 */
function validation_filter_id_card($id_card)
{
    if (strlen($id_card) == 18) {
        return idcard_checksum18($id_card);
    } elseif ((strlen($id_card) == 15)) {
        $id_card = idcard_15to18($id_card);
        return idcard_checksum18($id_card);
    } else {
        return false;
    }
}


/**
 * 计算身份证校验码，根据国家标准GB 11643-1999
 * @param $idcard_base
 * @return bool|mixed
 * @user 李海江 2018/12/11~1:41 PM
 */
function idcard_verify_number($idcard_base)
{
    if (strlen($idcard_base) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum           = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod           = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}


/**
 * 将15位身份证升级到18位
 * @param $idcard
 * @return bool|string
 * @user 李海江 2018/12/11~1:41 PM
 */
function idcard_15to18($idcard)
{
    if (strlen($idcard) != 15) {
        return false;
    } else {
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
            $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
        } else {
            $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
        }
    }
    $idcard = $idcard . idcard_verify_number($idcard);
    return $idcard;
}


/**
 * 18位身份证校验码有效性检查
 * @param $idcard
 * @return bool
 * @user 李海江 2018/12/11~1:41 PM
 */
function idcard_checksum18($idcard)
{
    if (strlen($idcard) != 18) {
        return false;
    }
    $idcard_base = substr($idcard, 0, 17);
    if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
        return false;
    } else {
        return true;
    }
}

/**
 * 读取xml文档（数组结构，也可以单个key放字符串自己截取，如：原因一\原因二）
 * @param $filename
 * @return array
 */
function xml_read($filename)
{
    $pathRoot = "xml/%s.xml";
    $path = sprintf($pathRoot,$filename);
    $returnArr = [];
    if(file_exists($path)){
        $doc = new DOMDocument();
        $doc->load($path);
        $fields = $doc->getElementsByTagName("fields")->item(0)->nodeValue;
        if($fields) {
            $fieldArr = explode('|',$fields);
            $data = $doc->getElementsByTagName($filename);
            foreach ($data as $item) {
                $one = [];
                foreach ($fieldArr as $field) {
                    $one[$field] = $item->getElementsByTagName($field)->item(0)->nodeValue;
                }
                $returnArr[count($returnArr)] = $one;
            }
        $doc->save($path);
        }
    }
    return $returnArr;
}

/**
 * 写入xml(默认全文档重写)
 * @param $filename
 * @param $dataArr
 */
function xml_write($filename,$dataArr){
    $pathRoot = "xml/%s.xml";
    $path = sprintf($pathRoot,$filename);
    if(is_array(current($dataArr))) {
        $fields = array_keys(current($dataArr));
        $dom = new DOMDocument('1.0', 'utf8');
        $dom->formatOutput = true;
        $root = $dom->createElement('root');
        $dom->appendChild($root);
        $root->appendChild($dom->createElement('fields',implode('|',$fields)));
        foreach ($dataArr as $dataItem) {
            $item = $dom->createElement('student');
            $root->appendChild($item);
            foreach ($fields as $field) {
                $element = $dom->createElement($field);
                $item->appendChild($element);
                $text = $dom->createTextNode($dataItem[$field]);
                $element->appendChild($text);
            }
        }
        $dom->save($path);
    }
}