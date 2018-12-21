<?php
namespace app\api\controller;
use app\common\model\Subject;
use app\common\controller\BaseApi;

class SubjectController extends BaseApi{

    private $obj;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->obj = new Subject();
    }


    public function add()
  {

  }

  public function delete()
  {

  }

  public function update()

  {

  }


    /**
     * 根据subject的id 获取科目的级别  work work_subject联查
     * @return array
     * @user 许卫旗 9.25
     */
    public function getSubjectLevelByid()
    {
        $request = Request::instance();
        if($request->isPost())
        {
            $webData = $request->post();
            $map['subject_id.id'] = $webData['subject_id'];
            $field="subject.*,sl.id as sid,sl.level";
            $data = $this->obj->getLevelByid($map,$field);
            if ($data){
                return layuiMsg('1', '获取成功',$data);
            }else{
                return layuiMsg('-1', '此名称下暂无可报名工种');
            }
        }
    }
}