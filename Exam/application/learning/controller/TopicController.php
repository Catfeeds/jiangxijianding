<?php
namespace app\learning\controller;
use think\Controller;
use think\Request;
use app\common\service\WorkType;
use app\common\service\LearningQuestion;
use think\Session;
use app\common\service\Work;

class TopicController extends Controller
{
    private $Stopic;
    private $WorkTypeModel;
    private $Swork;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->Stopic = new LearningQuestion();
        $this->WorkTypeModel = new WorkType();
        $this->Swork = new Work();
    }

    public function volume()
    {
        return view();
    }

    public function options()
    {
        return view();
    }

    //选项卡
    public function officialChoose()
    {
        Session::delete('threeLevel');
        //获取work
        $work = $this->Swork->BaseSelect(['status'=>1]);
        return view("",['work'=>$work]);
    }

    public function import()
    {
        return view();
    }

    public function oneChone()
    {
        $work = $this->Swork->BaseSelect(['status'=>1]);
        return view("",['work'=>$work]);
    }

    //首页
    public function official()
    {
        $request = Request::instance()->post();

        $where = [];
        if (!empty($request['type'])) {
            $where['type'] = trim($request['type']);
        }
        if (!empty($request['range'])) {
            $where['range'] = trim($request['range']);
        }
        if (!empty($request['topic_level'])) {
            $where['topic_level'] = trim($request['topic_level']);
        }
        if (!empty($request['topic_state'])) {
            $where['topic_state'] = trim($request['topic_state']);
        }

        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $join = [
              ['__WORK__', 'question.work_id = work.id','left'],
              ['__WORK_DIRECTION__', 'question.work_direction_id = work_direction.id','left']
        ];
        $field = ['question.*,work.name as work_name,work_direction.name as work_direction_name'];
        $data = $this->Stopic->BaseJoinSelectPage($paginate,'question',$join, $where, $field);

//        $field = 'learning_question.*,work.name as work_name,work_direction.name as work_direction_name';
//        $data = $this->Stopic->selectWorkLevelDirection($where,$field,"id desc");

        return view('topic/official',['data'=>$data, 'search'=>$request]);
    }

    //选项题
    public function officialAddChoose()
    {
        $id = Request::instance()->param('id');
        if ($id){
            $data = $this->Stopic->find(['id'=>$id]);
            $this->assign('datas',$data);
        }
        return view();
    }

    //判断题
    public function officialAddJudge()
    {
        $id = Request::instance()->param('id');
        if ($id){
            $data = $this->Stopic->find(['id'=>$id]);
            $this->assign('datas',$data);
        }

        return view();
    }

    //简答题
    public function officialAddBrief()
    {
        $id = Request::instance()->param('id');
        if ($id){
            $data = $this->Stopic->find(['id'=>$id]);
            $this->assign('datas',$data);
        }

        return view();
    }

    //论述题
    public function officialAddDescribe()
    {
        $id = Request::instance()->param('id');
        if ($id){
            $data = $this->Stopic->find(['id'=>$id]);
            $this->assign('datas',$data);
        }

        return view();
    }



    //填空题
    public function officialAddEmpty()
    {
        $id = Request::instance()->param('id');
        if ($id){
            $data = $this->Stopic->find(['id'=>$id]);
            $answer = explode('|@|',$data['answer']);
            $count = empty($answer) ? '1' : count($answer);

            $this->assign('datas',$data);
            $this->assign('answer',$answer);
            $this->assign('count',$count);
        }

        return view();
    }

    //作文题
    public function officialAddComposition()
    {
        $id = Request::instance()->param('id');
        if ($id){
            $data = $this->Stopic->find(['id'=>$id]);
            $this->assign('datas',$data);
        }

        return view();
    }

    //分析题
    public function officialAddAnalyze()
    {
        $id = Request::instance()->param('id');
        if ($id){
            $data = $this->Stopic->find(['id'=>$id]);
            $this->assign('datas',$data);
        }

        return view();
    }
    //操作题
    public function officialAddOperation()
    {
        $id = Request::instance()->param('id');
        if ($id){
            $data = $this->Stopic->find(['id'=>$id]);
            $this->assign('datas',$data);
        }

        return view();
    }

    public function practice()
    {
        return view();
    }

}