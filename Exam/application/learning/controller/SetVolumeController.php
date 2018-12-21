<?php
namespace app\learning\controller;
use think\Controller;
use think\Request;
use app\common\service\LearningQuestion;
use app\common\service\LearningTopicPaper;
use app\common\service\WorkType;
use app\common\service\Work;
use app\common\service\WorkDirection;
use app\common\service\ExamPlan;
use think\Session;
use app\common\service\LearningTopicSimulation;

class SetVolumeController extends Controller
{
    private $Swork;
    private $Stopic;
    private $SlearningTopicPaper;
    private $WorkTypeModel;
    private $SWorkDirection;
    private $SexamPlan;
    private $SLearningTopicSimulation;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->SexamPlan = new ExamPlan();
        $this->Stopic = new LearningQuestion();
        $this->SlearningTopicPaper = new LearningTopicPaper();
        $this->WorkTypeModel = new WorkType();
        $this->Swork = new Work();
        $this->SWorkDirection = new WorkDirection();
        $this->SLearningTopicSimulation = new LearningTopicSimulation();
    }

    public function chooseButton()
    {
        $work = $this->Swork->BaseSelect(['status'=>1]);
        return view("",['work'=>$work]);
    }

    public function details()
    {
        $request = Request::instance()->get();

        $dataOnly = $this->SlearningTopicPaper->find($request['id']);

        $where['id'] = ['in',$dataOnly['paper_id']];

        $data = $this->Stopic->selectEachDetail($where);

        foreach ($data as $k => $v) {
           if ($v['type'] == 3) {
               //判断
               $v['answer'] = str_replace(0,"正确", $v['answer']);
               $v['answer'] = str_replace(1,"错误", $v['answer']);
           } else if ($v['type'] == 5 or $v['type'] == 7) {
               //论述和简答
               unset($v['option_a']);
               unset($v['option_b']);
               unset($v['option_c']);
               unset($v['option_d']);
           } else {
               //选项题
               $v['answer'] = str_replace(0,"A", $v['answer']);
               $v['answer'] = str_replace(1,"B", $v['answer']);
               $v['answer'] = str_replace(2,"C", $v['answer']);
               $v['answer'] = str_replace(3,"D", $v['answer']);
           }
        }

        return view('set_volume/details',['data'=>$data]);
    }

    public function show()
    {
        $request = Request::instance()->post();
        //条件
        $direction = [];
        $where = [];
        if (!empty($request['work'])) {
            $where['paper.work_id'] = trim($request['work']);
            $direction = $this->SWorkDirection->BaseSelect(['work_id'=>$request['work']]);
        }
        if (!empty($request['level'])) {
            $where['paper.level_id'] = trim($request['level']);
        }
        if (!empty($request['direction'])) {
            $where['paper.work_direction_id'] = trim($request['direction']);
        }
        if (!empty($type)) {
            $where['paper.type'] = trim($type);
        }

        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $join = [
            ['__WORK__', '`paper`.work_id = `work`.id', 'left'],
            ['__WORK_DIRECTION__', '`work`.id = work_direction.work_id', 'left']
        ];
        $field = ['`paper`.id,`paper`.create_time,`paper`.level_id,work.name as work_name,work_direction.name as work_direction_name'];
        $data = $this->SlearningTopicPaper->BaseJoinSelectPage($paginate,'paper',$join, $where, $field);

        //下拉列表数据
        $work = $this->Swork->BaseSelect(['status'=>1]);

//        $data = $this->SlearningTopicPaper->getList($field,$where);

        return view('set_volume/show',['data'=>$data, 'search'=>$request,'work'=>$work,'direction' =>$direction]);
    }

    public function index()
    {
        $request = Request::instance()->post();
        $type = Request::instance()->get('type');

        //条件
        $direction = [];
        $where = [];
        if (!empty($request['work'])) {
            $where['que.work_id'] = trim($request['work']);
            $direction = $this->SWorkDirection->BaseSelect(['work_id'=>$request['work']]);

        }
        if (!empty($request['level'])) {
            $where['que.level_id'] = trim($request['level']);
        }
        if (!empty($request['direction'])) {
            $where['que.work_direction_id'] = trim($request['direction']);
        }
        if (!empty($type)) {
            $where['que.type'] = trim($type);
        }

        $field = 'que.topic_name,que.id,que.level_id,que.type,que.create_time,`work`.name as work_name,work_direction.name as direction_name';
        $data = $this->Stopic->selectWorkDirection($where, $field);

        //下拉列表数据
        $work = $this->Swork->BaseSelect(['status'=>1]);

        return view('set_volume/index',[
            'data'=>$data,
            'type'=>$type,
            'work'=>$work,
            'search'=> $request,
            'direction' =>$direction
            ]);
    }

    public function add()
    {
        $request = Request::instance()->get();

        $topicArr = explode(',',$request['id']);
        foreach ($topicArr as $k => $v) {
            if (empty($v)) unset($topicArr[$k]);
        }

        $topicArr = array_unique($topicArr);

        $topicCount = count($topicArr);

        $topicStr = implode(',',$topicArr);

        //验证添加试题工种，级别，方向 唯一性
        $where['learning_question.id'] = ['in',$topicStr];
        $group = 'learning_question.work_id, learning_question.level_id, learning_question.work_direction_id';
        $field = 'work.name as work_name,work_direction.name as direction_name,level_id,work.id as work_id,work_direction.id as direction_id';
        $workLevelDirection = $this->Stopic->selectWorkLevelDirection($where, $field, $group);

        if (count($workLevelDirection) > 1) {
            //选择题的工种，级别，方向 多个 返回
            $this->assign(['errorInfo'=> $workLevelDirection]);
        }

        $where['id'] = ['in',$topicStr];
        $field = 'type,count(0) as count';
        $topicEachCount = $this->Stopic->selectEachCount($where,$field);

        return view('set_volume/add',['topicEachCount'=>$topicEachCount, 'topicCount'=>$topicCount, 'topicId'=>$topicStr, 'workLevelDirection'=>$workLevelDirection]);
    }


    public function release()
    {
        $id = Request::instance()->get('id');
        $data = $this->SlearningTopicPaper->find($id);

        return view('set_volume/release',['data'=>$data]);
    }

}