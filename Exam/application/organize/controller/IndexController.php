<?php
namespace app\organize\controller;

use app\common\controller\Organizebase;
use app\common\service\Organize;
use app\common\service\OrganizeWork;

class IndexController extends Organizebase
{

    //首页
    public function index()
    {
        $organizeuser = session('organizeuser');
       
        return view('',["organizeuser"=>$organizeuser,'title'=>'首页']);
    }


    //修改密码页面
    public function updatepage()
    {
        return view('update');
    }


    //修改详细信息页面
    public function infopage()
    {
        $arrOrganize = session("organizeuser");
        $map['id'] = $arrOrganize['id'];
        $model = new Organize();
        $organizeuser = $model->BaseFind($map);
        $organizeuser['address'] = explode(",",$organizeuser['address']);
        return view('userinfo',['arrOrganize'=>$organizeuser]);
    }

    public function center()
    {
         $organizeuser = session('organizeuser');
        $arrWhere['organize.id'] = $organizeuser['id'];
        if ($organizeuser['type'] == 1){
            $arrWhere['exam_work.type'] = 6;
            $arrWhere['exam_work_level.type'] = 6;
        }else if ($organizeuser['type']== 2){
            $arrWhere['exam_work.type'] = 7;
            $arrWhere['exam_work_level.type'] = 7;
        }else if ($organizeuser['type'] == 3){
           $arrWhere['exam_work.type'] = 4;
            $arrWhere['exam_work_level.type'] = 4;
        }else{
            $arrWhere['exam_work.type'] = 0;
            $arrWhere['exam_work_level.type'] = 0;
        }
        $field = "organize.*,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
        $Organize = new OrganizeWork();
        $arr = config('EnrollStatusText.work_level_subject_level');
        $data = $Organize->getListData($arrWhere,$field);
        $data = $this->dataHandle($data,$arrWhere);
        return view('',["data"=>$data,'arr'=>$arr,'title'=>'机构详情']);
    }

    /**
     * 对机构数据进行处理
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function dataHandle($data,$arrWhere)
    {
        $arrOrganize = collection($data)->toArray();
        $Organize = new Organize();
        // print_r($Organize);die;
        foreach ($arrOrganize as $k=>$v){
            $v['address'] = str_replace(","," ",$v['address']);
            $arrOrganize[$k]['build_date'] = date("Y-m-d H:i:s",$v['build_date']);
        }
        $arr = array_columns($arrOrganize,['wid','workname','wdname','work_level']);
        
        $arrWork = array_unique_key($arrOrganize,"wid");
        foreach ($arrWork as $k=>$v){
            $arrWork[$k]['wdname'] = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'wdname'));
            $work_level = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'work_level'));
            sort($work_level);
            $arrWork[$k]['level'] = $work_level;
        }
        if (empty($arrOrganize)){
            $arrWork = $Organize->BaseSelect(['id'=>$arrWhere['organize.id']]);
            foreach ($arrWork as $k=>$v){
                $arrWork[$k]['build_date'] = date("Y-m-d H:i:s",$v['build_date']);
                $arrWork[$k]['typename'] = '';
                $arrWork[$k]['wtid'] = '';
                $arrWork[$k]['wid'] = '';
                $arrWork[$k]['workname'] = '';
                $arrWork[$k]['work_level'] = '';
                $arrWork[$k]['wdname'] = [];
                $arrWork[$k]['level'] = [];

            }
        }
        return $arrWork;
    } 


}

