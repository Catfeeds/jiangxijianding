<?php

namespace app\inter\controller;

use think\Controller;

/**
 * Class CertificateController
 * @package app\xmlinterface\controller
 */
class CertificateController extends Controller
{
    /**
     * @user 李海江 2018/12/18~9:07 PM
     */
    public function index()
    {
        $arr = array(
            'plan'      => [
                'plan_name'   => '批次名称(必填)',
                'print_code'  => '机构代码',
                'print_name'  => '机构名称',
                'enroll_code' => '报名机构代码',
                'enroll_name' => '报名机构名称',
                'ctype'       => '证书类型[1:普通版,2:统考版](必填)',
                'exam_name'   => '考试名称(必填)',
                'exam_type'   => '考试类型[全国、全市、专场、竞赛、ATA](必填)',
                'id'          => '导入系统的考试id(必填)',
                'num'         => '导入人数(必填)',
            ],
            'exam-list' => [
                [
                    'exam_id'                => '导入系统考生唯一ID(必填)',
                    'exam_status'            => '考生状态[0：新增,1：更新](必填)',
                    'name'                   => '姓名(必填)',
                    'card_num'               => '居民身份证、军官证、香港特区护照/身份证、澳门特区护照/身份证、台湾居民来往大陆通行证(必填)',
                    'gender'                 => '性别[男、女](必填)',
                    'birthday'               => '出生日期yyyy-MM-dd(必填)',
                    'culture_standard'       => '文化程度(必填)',
                    'exam_card_num'          => '准考证号(必填)',
                    'work_name'              => '鉴定职业(必填)',
                    'level_name'             => '鉴定级别[一级、二级、三级、四级、五级](必填)',
                    'occu_code'              => '职业/工种编码[参照职业大典](必填)',
                    'credential_code'        => '证书编号(必填)',
                    'credential_create_date' => '发证日期yyyy-MM-dd(必填)',
                    'tt_score'               => '理论成绩(必填)',
                    'oo_score'               => '实操成绩(必填)',
                    'gg_score'               => '综合评审成绩(必填)',
                    'ee_score'               => '英语成绩',
                    'is_pass'                => '评定成绩[合格、良好、优秀](必填)',
                    'photo'                  => '照片[base64编码字符串](必填)',
                    'derection'              => '职业方向(必填)',
                    'qualification'          => '职业资格小类(必填)',
                ],
            ],
        );

        $option = ['root_node' => 'data', 'item_node' => 'exam', 'item_key' => '',];
        return xml($arr, '200', [], $option);
    }
}