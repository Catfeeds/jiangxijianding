<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/9/19
 * Time: 下午6:13
 */

namespace app\common\service;

use app\common\model\ExamOrder as MExamOrderDetail;

class ExamOrderDetail extends MExamOrderDetail
{

    /**
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @user xuweiqi
     */
    public function findOrderDetail($map)
    {
        return $this->BaseFind($map);
    }

    /**
     * [orderDetail 机构订单详情]
     * @return [type] [description]
     */
    public function orderDetail($map)
    {
        $field = "od.*,w.name,ee.work_level_subject_level as level,ul.id_card,ul.name as username";
    	$join = array(
    		['__EXAM_ENROLL__ ee','ee.id=od.enroll_id'],
    		['__WORK__ w','w.id=ee.work_id'],
    		['__USER_LOGIN__ ul','ul.id=ee.user_login_id']
    	);
    	$paginate = array(
           config('paginate.list_rows'),
           false,
           ['query'=>request()->param()],
        );

    	$detail = $this->BaseJoinSelectPage($paginate,'od',$join,$map,[$field]);
        // print_r($detail);die;
    	return $detail;
    }

}