<?php  

namespace app\common\service;

use app\common\model\JuryReview as MJuryReview;

/**
 * Class ExamEnroll
 * @package app\common\service
 */
class JuryReview extends MJuryReview
{
	/**
	 * [selectAllotRecord 查询已分配的人员记录]
	 * @return [type] [description]
	 */
	public function selectAllotRecord($map,$field=[])
	{
		$join = [
			['__JURY__ j','j.id = jr.jury_id','left'],
			['__WORK__ w','jr.work_id = w.id','left'],
		];
		return $this->BaseJoinSelect('jr',$join,$map,$field);
	}
	public function selectAllWork($map,$field=[])
	{
		$join = [
			['__JURY_CERTIFICATE__','jury_certificate.id = jury_review.jury_id','left'],
			['__WORK__','jury_review.work_id = work.id','left'],
			['__ORGANIZE__','organize.id = jury_review.organize_id','left'],
		];
		return $this->BaseJoinSelect('jury_review',$join,$map,$field);
	}

	public function getOrganizeJury($paginate, $param = [], $field = [], $order = '', $group = '')
    {
        $join = array(
            ["__WORK__","jury_review.work_id=`work`.id AND `work`.`status`=1","left"],
            ["__JURY_CERTIFICATE__","jury_review.jury_id=jury_certificate.id AND jury_certificate.`status`=1","left"],
        );
//        print_r($field);die;
        $data = $this->BaseJoinSelectPage($paginate,'jury_review', $join, $param, $field, $order, $group);
        return $data;
    }
}