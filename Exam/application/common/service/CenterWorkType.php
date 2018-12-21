<?php
/**
 * Created by PhpStorm.
 * User: WQ
 * Date: 2018/12/11
 * Time: 16:29
 */

namespace app\common\service;
use app\common\model\CenterWorkType as MCenterWorkType;use think\Db;

class CenterWorkType extends MCenterWorkType
{
    /**
     * 获取鉴定中心鉴定计划的工种级别
     * @param $exam_type
     * @param $work_type
     * @param $center_type
     * @param $exam_id
     * @param int $exam_work_type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCenterWork($exam_type,$work_type,$center_type,$exam_id,$exam_work_type=5){
        $field = 'exam_work_type.exam_type_name,exam_work_type.work_type_name,`work`.id,`work`.name,center_work_type.work_type_level,
	CASE WHEN exam_work.id IS NULL THEN 	0 ELSE 1 END AS workchecked,case when exam_work_level.id is null then 0 else 1 end as levelchecked';
        $where = [
            'exam_work_type.exam_type_id'=>$exam_type,
        'center_work_type.center_level'=>$center_type,
        'exam_work_type.work_type_id'=>$work_type
        ];
        $workLevel = $this
            ->join("__EXAM_WORK_TYPE__","center_work_type.work_type_id = `exam_work_type`.work_type_id and exam_work_type.delete_time is null")
            ->join("__WORK__","`work`.work_type_id = center_work_type.work_type_id and exam_work_type.delete_time is null")
            ->join("__EXAM_WORK__","exam_work.work_id = `work`.id	and exam_work.type = ".$exam_work_type." and exam_work.delete_time is null and exam_work.exam_id = ".$exam_id,"left")
            ->join("__EXAM_WORK_LEVEL__"," exam_work_level.exam_work_id = exam_work.id and exam_work_level.work_level =center_work_type.work_type_level and exam_work_level.delete_time is null","left")
            ->field($field)
            ->where($where)
            ->where("center_work_type.delete_time is null")
            ->order('work.id')->order('center_work_type.work_type_level')
            ->select();
        $workArr = [];
        foreach ($workLevel as $k=>$v){
            if(array_key_exists($v['name'],$workArr)){
                $workArr[$v['name']]['level']=array_merge($workArr[$v['name']]['level'],
                    [
                        [
                            'id'=>$v['work_type_level'],
                            'level'=>$this->levelStr($v['work_type_level']),
                            'ischecked' => $v["levelchecked"]
                        ]
                    ]);
            }else{
                $workArr[$v['name']] = $v;
                $workArr[$v['name']]['ischecked'] = $v["workchecked"];
                $workArr[$v['name']]['level'] =[
                    [
                        'id'=>$v['work_type_level'],
                        'level'=>$this->levelStr($v['work_type_level']),
                        'ischecked' => $v["levelchecked"]
                    ]
                ];
            }
        }
        return $workArr;
    }
    public function levelStr($index){
        if(empty($index))
        {
            return "无级别";
        }
        $arr = ['',"高级技师","技师","高级","中级","初级"];
        return $arr[$index];
    }

}