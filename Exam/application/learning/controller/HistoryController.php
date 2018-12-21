<?php

namespace app\learning\controller;

use think\Controller;
use think\Request;
use app\common\service\LearningHistory;
use app\common\service\Userinfo;
use app\common\service\LearningPaperHistory;
use app\common\service\LearningAnswerHistory;
use app\common\service\WorkDirection;
use app\common\service\Work;

class HistoryController extends Controller
{
    private $Swork;
    private $Shistory;
    private $SLearningPaperHistory;
    private $SLearningAnswerHistory;
    private $SWorkDirection;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->Swork = new Work();
        $this->SLearningAnswerHistory = new LearningAnswerHistory();
        $this->Shistory = new LearningHistory();
        $this->Userinfo = new Userinfo();
        $this->SLearningPaperHistory = new LearningPaperHistory();
        $this->SWorkDirection = new WorkDirection();
    }

    public function index()
    {
        $request = Request::instance()->post();//搜索
        $role = Request::instance()->get('role');//角色
        //下拉搜索，工种
        $work = $this->Swork->BaseSelect(['status'=>1]);
        //角色
        if ($role == 1) {
            //考评人员
            $where['learning_paper_history.role'] = 1;
        } else {
            //考生
            $where['learning_paper_history.role'] = 0;
        }
        //条件
        $where = [];
        //搜索保持方向
        $direction = [];
        if (!empty($request['user_login_name'])) {
            $where['userinfo.username'] = ['like', '%' . trim($request['user_login_name']) . '%'];
        }
        if (!empty($request['work'])) {
            $where['learning_paper_history.work_id'] = trim($request['work']);
            $direction = $this->SWorkDirection->BaseSelect(['work_id'=>$request['work']]);
        }
        if (!empty($request['level'])) {
            $where['learning_paper_history.level'] = trim($request['level']);
        }
        if (!empty($request['direction'])) {
            $where['learning_paper_history.work_direction_id'] = trim($request['direction']);
        }

        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $join = [
            ['__WORK__','history.work_id = work.id','left'],
            ['__WORK_DIRECTION__','history.level = work_direction.id','left'],
            ['__USERINFO__','history.user_id = userinfo.user_login_id','left']
        ];
        $field = ['work.name as work_name,work_direction.name as work_direction_name,userinfo.username as user_login_name,history.name as learning_paper_history_name,history.create_time,history.`level` as level_name,history.role as learning_paper_history_role,history.test_type as learning_paper_history_test_type,history.id,history.user_id'];
        $data = $this->SLearningPaperHistory->BaseJoinSelectPage($paginate,'history',$join, $where, $field);
        //查询字段
//        $data = $this->SLearningPaperHistory->getList($where, $field);
        return view('history/index', ['data' => $data, 'work'=> $work,'direction'=>$direction,'search'=>$request]);
    }

    public function question_history()
    {
        $request = Request::instance()->get();

        $where['learning_answer_history.paper_id'] = $request['paper_id'];
        $where['learning_answer_history.user_id'] = $request['user_id'];
        $field = ['learning_question.*,learning_answer_history.user_answer'];
        $data = $this->SLearningAnswerHistory->questionList($field, $where);
        foreach ($data as $k => $v) {
            $v['answer'] = str_replace(0,"A", $v['answer']);
            $v['answer'] = str_replace(1,"B", $v['answer']);
            $v['answer'] = str_replace(2,"C", $v['answer']);
            $v['answer'] = str_replace(3,"D", $v['answer']);

            $v['user_answer'] = str_replace(0,"A", $v['user_answer']);
            $v['user_answer'] = str_replace(1,"B", $v['user_answer']);
            $v['user_answer'] = str_replace(2,"C", $v['user_answer']);
            $v['user_answer'] = str_replace(3,"D", $v['user_answer']);
        }

        return view('',['data'=>$data]);
    }

}