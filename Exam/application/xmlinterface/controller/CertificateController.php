<?php

namespace app\xmlinterface\controller;

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
                'plan_name'   => '2015年9月全国统考（东城）',
                'print_code'  => '机构代码',
                'print_name'  => '机构名称',
                'enroll_code' => '报名机构代码',
                'enroll_name' => '报名机构代码',
                'ctype'       => 1,
                'exam_name'   => '2015年9月全国职业技能鉴定统一考试',
                'exam_type'   => '全国',
                'id'          => 1,
                'num'         => 500,
            ],
            'exam-list' => [
                [
                    'exam_id'                => 1,
                    'exam_status'            => '新增',
                    'name'                   => '张三',
                    'card_num'               => '110101198803102336',
                    'gender'                 => '男',
                    'birthday'               => '1995-11-05',
                    'culture_standard'       => '本科',
                    'exam_card_num'          => '1411230104000700018',
                    'work_name'              => '电子商务师',
                    'level_name'             => '三级',
                    'occu_code'              => '0100998',
                    'credential_code'        => '1401000007300271',
                    'credential_create_date' => '2015-09-10',
                    'tt_score'               => '80.33',
                    'oo_score'               => '60.35',
                    'gg_score'               => '90.11',
                    'ee_score'               => '88.88',
                    'is_pass'                => '优秀',
                    'photo'                  => 'abcdefg.jpg',
                    'derection'              => '理发师',
                    'qualification'          => '职业资格小类',
                ],
                [
                    'exam_id'                => 1,
                    'exam_status'            => '新增',
                    'name'                   => '张三',
                    'card_num'               => '110101198803102336',
                    'gender'                 => '男',
                    'birthday'               => '1995-11-05',
                    'culture_standard'       => '本科',
                    'exam_card_num'          => '1411230104000700018',
                    'work_name'              => '电子商务师',
                    'level_name'             => '三级',
                    'occu_code'              => '0100998',
                    'credential_code'        => '1401000007300271',
                    'credential_create_date' => '2015-09-10',
                    'tt_score'               => '80.33',
                    'oo_score'               => '60.35',
                    'gg_score'               => '90.11',
                    'ee_score'               => '88.88',
                    'is_pass'                => '优秀',
                    'photo'                  => 'abcdefg.jpg',
                    'derection'              => '理发师',
                    'qualification'          => '职业资格小类',
                ],
            ],
        );

        $option = ['root_node' => 'data', 'item_node' => 'exam', 'item_key' => '',];
        return xml($arr, '200', [], $option);
    }
}