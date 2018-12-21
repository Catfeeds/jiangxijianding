<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/10/18
 * Time: 5:25 PM
 */
namespace app\common\model;

/**
 * Class ExamCenter
 * @package app\common\model
 */
class ExamCenter extends BaseModel
{

    public function getCountyTypeAttr($type)
    {
        switch ($type) {
            case 0 :
                return '';
                break;
            case 1:
                return '省辖县';
                break;
            case 2:
                return '县级市';
                break;
        }
    }

    public function setCountyTypeAttr($type)
    {
        switch ($type) {
            case '标准市' :
                return 0;
                break;
            case '省辖市':
                return 1;
                break;
            case '县级市':
                return 2;
                break;
        }
    }
}
