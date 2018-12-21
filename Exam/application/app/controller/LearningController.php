<?php
/**
 * Created by PhpStorm.
 * User: 刘欣
 * Date: 2018/11/7
 * Time: 16:36 AM
 */
namespace app\app\controller;
use app\common\controller\AppBase;
use app\common\service\LearningCollection;
use app\common\service\LearningHistory;
use app\common\service\LearningPaperHistory;
use app\common\service\LearningAnswerHistory;
use app\common\service\LearningMedia;
use app\common\service\LearningTopicPaper;
use app\common\service\LearningHour;
use app\common\service\LearningQuestion;
use app\common\service\UserLogin;
use app\common\service\Work;
use think\Request;
use app\common\service\ExamEnroll;


class LearningController extends AppBase
{
    private $Swork;
    private $SUserLogin;
    private $SLearningHistory;
    private $SAnswer;
    private $SPaper;
    private $Stopic;
    private $Smedia;
    private $STopicPaper;
    private $SlearningTime;
    private $SlearningCollect;
    private $SexamEnroll;


    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->Stopic = new LearningQuestion();
        $this->STopicPaper = new LearningTopicPaper();
        $this->request = $request;
        $this->Swork = new Work();
        $this->SUserLogin = new UserLogin;
        $this->SLearningHistory = new LearningHistory;
        $this->SPaper = new LearningPaperHistory();
        $this->SAnswer = new LearningAnswerHistory();
        $this->Smedia = new LearningMedia();
        $this->SlearningTime = new LearningHour();
        $this->SlearningCollect = new LearningCollection();
        $this->SexamEnroll = new ExamEnroll();

    }

    public function file($file_url)
    {
        return view('file/index', ['file_url' => $file_url]);
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * 文件-收藏列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/21~15:53
     */
    public function showCollect()
    {
        $uid = appGetUid();
        $where['learning_collection.user_id'] = $uid;
        $field = 'learning_media.*';
        $list = $this->SlearningCollect->getList($where,$field);

        foreach ($list as $key => $value) {
            // 学习资料轮播图
            $list[$key]['cover'] = 'http://'.$_SERVER['SERVER_NAME'] . '/uploads/learning/appimg/' . rand(1, 3) . '.png';

            if ($value['file_type'] == 'PDF文件格式' || $value['file_type'] == 'Flash动画文件') {
                $list[$key]['file_url'] = 'http://'.$_SERVER['SERVER_NAME'] . $value['file_address'];
                $list[$key]['file_url'] = 'http://'.$_SERVER['SERVER_NAME'] . '/app/learning/file?file_url=' .$value['file_address'];
            } else {
                $list[$key]['file_url'] = 'http://view.officeapps.live.com/op/view.aspx?src=http://' .$_SERVER['SERVER_NAME'] . $value['file_address'];
            }
        }
        config('app.20001.data', $list);
        result('20001');
    }

    /***
     * 文件-收藏 取消收藏
     * @throws \think\exception\DbException
     * @user 刘欣 2018/11/20~21:40
     */
    public function collect()
    {
        //工种id 用户id
        $user_id = appGetUid();
        $map = $this->request->only(['file_id', 'type'], 'post');
        $where['user_id'] = $user_id;
        $where['topic_id'] = $map['file_id'];

        if ($map['type']) {
            $isCollect = $this->SlearningCollect->BaseFind($where);
            if ($isCollect) {
                result('40108');
                die;
            }
        }

        if ($map['type']) {
            $result = $this->SlearningCollect->BaseSave($where);
        } else {
            $result = $this->SlearningCollect->BaseUpdate(['delete_time' => time()], $where);
        }

        if ($result) {
            result('20001');
        }
    }

    /**试卷列表-清除历史记录
     * @user 刘欣 2018/11/20~16:49
     */
    public function clearHistory()
    {
        //工种id 用户id
        $user_id = appGetUid();
        $role = $this->request->header('role');
        $map = $this->request->only(['work_id', 'level', 'work_direction_id'], 'post');
        if ($role == 0) {
            //考生  方向不是必传
            $where['user_id'] = $user_id;
            $where['work_id'] = $map['work_id'];
            $where['level'] = $map['level'];
            if (!empty($map['work_direction_id'])) {
                $where['work_direction_id'] = $map['work_direction_id'];
            }
            $result = $this->SPaper->BaseUpdate(['delete_time' => time()], $where);

            if ($result) {
                result('20001');
            } else {
                result('40010');
            }
        } else {
            //考评人员
            $where['user_id'] = $user_id;
            $result = $this->SPaper->BaseUpdate(['delete_time' => time()], $where);
            if ($result) {
                result('20001');
            } else {
                result('40010');
            }
        }
    }

    /**
     * 获取学习资料 标识已收藏的资料 （考评员）
     * @param array $map
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 刘欣 2018/11/14~12:04
     */
    public function materials($map = [])
    {
        $map = $this->request->only(['file_id'], 'post');
        $uid = appGetUid();
        $field = ['id,file_name,file_type,file_address,file_id'];

        $where = '';
        //file_id查询分类
        if (!empty($map['file_id'])) {
            $where['file_id'] = $map['file_id'];
        }
        //分页
        $page = request()->param('page') ? request()->param('page') : 1;
        $count = request()->param('count') ? request()->param('count') : 10;
        $page = [$count, true, ['page' => $page]];
        //所有学习资料列表
        $list = $this->Smedia->appGetAll($page, $where, $field, 'id desc');
        //获得我的收藏的学习资料列表
        $collectList = $this->SlearningCollect->select(['user_id' => $uid], ['topic_id as id']);

        $list = contrastArray($list, $collectList);


        foreach ($list as $key => $value) {

            // 学习资料轮播图
            $list[$key]['cover'] = 'http://'.$_SERVER['SERVER_NAME'] . '/uploads/learning/appimg/' . rand(1, 3) . '.png';
            //file_address
            $list[$key]['file_address'] = 'http://'.$_SERVER['SERVER_NAME'] . $value['file_address'];

            if ($value['file_type'] == 'PDF文件格式' || $value['file_type'] == 'Flash动画文件') {
                $list[$key]['file_url'] = 'http://'.$_SERVER['SERVER_NAME'] .'/app/learning/file?file_url=' .$value['file_address'];
            } else {
                $list[$key]['file_url'] = 'http://view.officeapps.live.com/op/view.aspx?src=http://' .$_SERVER['SERVER_NAME'] . $value['file_address'];
            }

        }

        config('app.20001.data', $list);
        result('20001');
    }

    /***
     * 返回文件类型 学时
     * @user 刘欣 2018/11/19~19:14
     */
    public function topicType()
    {

        //类型(1:视频,2:文档,3:pdf,4:ppt,5.swf)
        $type = [
            [
                'file_name' => '视频格式',
                'file_id' => '1'
            ],
            [
                'file_name' => 'Docx文档格式',
                'file_id' => '2'
            ],
            [
                'file_name' => 'PDF文件格式',
                'file_id' => '3'
            ],
            [
                'file_name' => 'PPT文件格式',
                'file_id' => '4'
            ],
            [
                'file_name' => 'Flash动画文件',
                'file_id' => '5'
            ]
        ];
        //接受数据 0学员 1考评员
        $role = $this->request->header('role');
        $user_id = appGetUid();

//        //查询用户报名工种，级别，方向
//        $where['type'] = $role;
//        $where["exam_enroll.user_login_id"] = $user_id;
//        $where["exam_enroll.status"] = ['egt',config('ExamEnrollStatus.checkpass')];
//        $field = ['sum(learning_hour.hours) hours'];
//        $list = $this->SexamEnroll->getWorkList($where, $field);

        $data = [
            'learning_time' => 0,
            'type' => $type
        ];
        config('app.20001.data', $data);
        result('20001');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * 试卷列表-练习考试-练习记录
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/22~12:27
     */
    public function testPaperHistory()
    {
        //接受数据 0学员 1考评员
        $role = $this->request->header('role');
        $user_id = appGetUid();
        $map = $this->request->only(['work_id', 'level_id', 'direction_id', 'count'], 'post');

        if ($role == 1) {
            $map['work_id'] = 0;
            $map['level_id'] = 0;
            $map['direction_id'] = 0;
        }
        if ($role == 0 && (empty($map['work_id']) || empty($map['level_id']) || empty($map['direction_id']))) {
            config('app.40105.data', []);
            result('40105');
        }
        $where['learning_paper_history.work_id'] = $map['work_id'];
        $where['learning_paper_history.work_direction_id'] = $map['direction_id'];
        $where['learning_paper_history.level'] = $map['level_id'];
        $where['learning_paper_history.user_id'] = $user_id;
        $where['learning_paper_history.role'] = $role;
        $where['learning_paper_history.test_type'] =1;
        $field = "learning_paper_history.id,learning_paper_history.stop_time,`work`.`name`,learning_paper_history.`level`,work_direction.`name` as work_direction_name,learning_paper_history.score,learning_paper_history.correct_count,learning_paper_history.error_count,learning_paper_history.empty_count";
        $list = $this->SPaper->getWorkHistoryList($where, $field, "learning_paper_history.id DESC");

        foreach ($list as $k => $v) {
            $list[$k]['cover'] = 'http://'.$_SERVER['SERVER_NAME'] . '/uploads/learning/appimg/' . rand(1, 3) . '.png';
            $v['level_id'] = $v['level'];
            $v['level_name'] = $v->level;
            unset($v['level']);
        }

        config('app.20001.data', $list);
        result('20001');
    }

    /**
     * 当前用户所有报名工种
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/22~12:27
     */
    public function workList()
    {
        //接受数据 0学员 1考评员
        $role = $this->request->header('role');
        $user_id = appGetUid();

        //查询用户报名工种，级别，方向
        $where['type'] = $role;
        $where["exam_enroll.user_login_id"] = $user_id;
        $where["exam_enroll.status"] = ['egt',config('ExamEnrollStatus.checkpass')];
        $field = ['sum(learning_hour.hours) hours,exam_enroll.id as enroll_id,`work`.id', '`work`.`name`', '`work`.`code`', 'work_direction.id as direction_id', 'work_direction.name as direction_name', 'exam_enroll.work_level_subject_level'];
        $list = $this->SexamEnroll->getWorkList($where, $field);

        foreach ($list as $k => $v) {
            $v['level_id'] = $v['work_level_subject_level'];
            $v['level_name'] = $v->work_level_subject_level;
            unset($v['work_level_subject_level']);
        }

        config('app.20001.data', $list);
        result('20001');
    }

    /***
     * 试卷列表-模拟考试
     * @user 刘欣 2018/11/20~17:27
     */
    public function topicPaper()
    {
        $role = $this->request->header('role');
        $user_id = appGetUid();
        $map = $this->request->only(['work_id', 'level_id', 'work_direction_id','enroll_id'], 'post');

        if ($role == 1) {
            $where['learning_topic_paper.work_id'] = 0;
            $where['learning_topic_paper.level_id'] = 0;
            $where['learning_topic_paper.work_direction_id'] = 0;
        } else {
            $where['learning_topic_paper.work_id'] = $map['work_id'];
            $where['learning_topic_paper.level_id'] = $map['level_id'];
            $where['learning_topic_paper.work_direction_id'] = $map['work_direction_id'];
        }
        $where['learning_topic_paper.test_type'] = '2';

        $field = ['learning_topic_paper.create_time,learning_topic_paper.id,learning_topic_paper.paper_name,learning_topic_paper.work_id,learning_topic_paper.work_direction_id,work.name,work_direction.name,learning_paper_history.score,learning_paper_history.correct_count,learning_paper_history.id as history_id,learning_paper_history.error_count,learning_paper_history.empty_count'];
        $volumeObject = $this->STopicPaper->getPaperDetail($where,$user_id, $field, '', 'id desc');

        foreach ($volumeObject as $k => $v){
            $v['flag'] = !empty($v['history_id']);
            $volumeObject[$k]['cover'] = 'http://'.$_SERVER['SERVER_NAME'] . '/uploads/learning/appimg/' . rand(1, 3) . '.png';
        }

        config('app.20001.data', $volumeObject);
        result('20001');

    }

    /**
     * 试题列表-模考试题
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/22~22:08
     */
    public function topicQuestionList()
    {
        $map = $this->request->only(['paper_id'], 'post');
        if (empty($map["paper_id"])) {
            result('40109');
        }

        //当前时间试题编号
        $paper = $this->STopicPaper->BaseFind(["id"=>$map["paper_id"]]);
        $questionIds = $paper["paper_id"];
        $jsonScore = $paper['each_score'];

        //考生条件
        $where['que.id'] = ["in", $questionIds];
        $res = $this->BuilderQuestion($where,'',$jsonScore,'');
        //返回内容: [题型[题目、选项、答案、解析、分数]]
        config('app.20001.data', $res);
        result('20001');
    }

    /**
     * 试题列表-练习试题列表 继续答题列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/22~22:05
     */
    public function testQuestionList($paperId=0)
    {
        //角色 考评人员:1 考生:0
        $role = $this->request->header('role');
        $map = $this->request->only(['work_id', 'level_id', 'work_direction_id'], 'post');
        //考生条件
        $where = [];
        if ($paperId > 0) {
            $where['answer.paper_id'] = $paperId;
        } else {
            if ($role == 0) {
                $where['que.work_id'] = $map['work_id'];
                $where['que.work_direction_id'] = $map['work_direction_id'];
                $where['que.level_id'] = $map['level_id'];
            } else {
                $where['que.work_id'] = 0;
            }
        }

        $res = $this->BuilderQuestion($where, $paperId > 0,'',true);
        //返回内容: [题型[题目、选项、答案、解析、分数]]
        config('app.20001.data', $res);
        result('20001');
    }

    /***
     * 试题列表-继续答题列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 李海江 2018/11/23~10:32
     */
    public function goOnQuestionList()
    {
        $map = $this->request->only(['paper_id'], 'post');
        return $this->testQuestionList($map['paper_id']);
    }

    /**
     * 构造试题列表(格式化)
     * @param array $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/22~22:16
     */
    private function BuilderQuestion($where = [],$isGoOn=false,$isScore,$limit)
    {
        //模考题需每题分数
        if ($isScore) {
            $arrayScore = json_decode($isScore, true);
            //列出三种题型分数
            $single = $arrayScore[1];
            $more = $arrayScore[2];
            $judge = $arrayScore[3];
        }

        $buildList = [];
        $queList = [];
        //查询题库
        $field = ["que.id,que.type,que.topic_name,que.option_a,que.option_b,que.option_c,que.option_d,que.answer,que.answer_explain,".($isGoOn?"answer.user_answer":"'' user_answer")];
        $res = $this->Stopic->selectQuestionWithAnswer($where, $field,$isGoOn ? 'INNER' : 'LEFT',$limit ? '100' : '');

        foreach ($res as $k => $v) {

            switch ($v['type']) {
                case 3:
                    $res[$k]['option'] = [
                        'A.' . $v['option_a'],
                        'B.' . $v['option_b'],
                    ];
                    break;
                case 1:
                    $res[$k]['option'] = [
                        'A.' . $v['option_a'],
                        'B.' . $v['option_b'],
                        'C.' . $v['option_c'],
                        'D.' . $v['option_d'],
                    ];
                    break;
                case 2:
                    $res[$k]['option'] = [
                        'A.' . $v['option_a'],
                        'B.' . $v['option_b'],
                        'C.' . $v['option_c'],
                        'D.' . $v['option_d'],
                    ];
                    break;
            }
            unset($v['option_a']);
            unset($v['option_b']);
            unset($v['option_c']);
            unset($v['option_d']);
            $buildList[$v['type']][]=$v;
        }
        $score = '';
        foreach ($buildList as $k=>$v) {
            //判断该题是什么题型 赋值 +=
            if ($isScore) {
                if ($k == 1) {
                    $score = $single;
                } else if($k == 2) {
                    $score = $more;
                } else if ($k == 3) {
                    $score = $judge;
                } else {
                    $score = '';
                }
            }
            $queList[] = ['type'=>$k,'score'=>$score,'data'=>$v];
        }
        return $queList;
    }

    /***
     * 交卷-保存答案
     * todo:刘欣(模考60分加1学时larning_Hour表)
     * @throws \think\exception\DbException
     * @user 朱颖 2018/11/22~18:39
     */
    public function submitAnswer()
    {
        $user_id = appGetUid();
        //接受数据 0学员 1考评员
        $role = $this->request->header('role');
        $map = $this->request->only(['enroll_id','test_type','answer', "start_time","paper_id", "work_id", "level_id", "work_direction_id","duration"], 'post');
        //考评人员参数可为空
        if ($role == 1) {
            $map['work_id'] = '0';
            $map['level_id'] = '0';
            $map['work_direction_id'] = '0';
        }

        //解析编码格式
        $answerArray = json_decode($map['answer'], true);

        $questionIds = array_column($answerArray, "id");
        $questionIds = implode(",", $questionIds);
        $where['id'] = ["in", $questionIds];
        $questionList = $this->Stopic->BaseSelect($where, ["id", "answer", "type"]);
        $saveAnswerArray = [];
        $question_count = 0;
        $correct_count = 0;
        $error_count = 0;
        $empty_count = 0;
        $single_value = 0;
        $more_value = 0;
        $judge_value = 0;
        $score = 0;
        foreach ($answerArray as $k => $v) {
            $currQuestion = array_where(collection($questionList)->toArray(), ["id" => $v["id"]])[0];
            //试题答案
            $saveAnswerArray[] = ["user_id" => $user_id, "role" => $role, "question_id" => $currQuestion["id"], "question_answer" => $currQuestion["answer"], "user_answer" => $v["answer"], "type" => $currQuestion["type"], "is_correct" => $currQuestion["answer"] == $v["answer"], "create_time" => $map["start_time"]];

            if($currQuestion["answer"] ==$v["answer"]){
                $correct_count++;
            }else if(empty($v["answer"])){
                $empty_count++;
            }else{
                $error_count++;
            }
            $question_count++;
        }
        $score = $correct_count;
        //模考计算学时数
        if (isset($map['paper_id'])) {
            $hours = 0;
            if ($score >= 60) {
                $hours = $this->STopicPaper->BaseFind(['id' => $map['paper_id']])['hours'];
            }
            //学时
            $hours = [
                'hours' => $hours ? $hours : 0,
                "paper_id"=> $map['paper_id'],
                'user_id' => $user_id,
                'enroll_id' => $map['enroll_id'],
                'score'=> $score,
                "create_time"=> time(),

            ];
            $this->SlearningTime->BaseSave($hours);
        }

        $savePaper = [
            "name" => isset($paper['paper_name']) ? $paper['paper_name'] : '',
            'score'=> isset($score) ? $score : 0,
            "user_id" => $user_id,
            "role" => $role,
            "work_id" => $map["work_id"],
            "work_direction_id" => $map["work_direction_id"],
            "level" => $map["level_id"], "question_count" => $question_count,
            "correct_count" => $correct_count,
            "error_count" => $error_count,
            "empty_count" => $empty_count,
            "accuracy_rate"=>$correct_count/$question_count*100,
            "start_time"=>$map["start_time"],
            "duration"=>$map["duration"],
            "test_type"=>$map['test_type'],
            "paper_id"=>isset($map['paper_id']) ? $map['paper_id'] : 0,
            "create_time"=> time(),
            "stop_time"=> time()
        ];
        $savePaperId = $this->SPaper->BaseSave($savePaper);
        foreach ($saveAnswerArray as $k=>$v){
            $saveAnswerArray[$k]["paper_id"] = $savePaperId;
        }
        //保存试题答案记录
        $this->SAnswer->BaseSaveAll($saveAnswerArray);

        //答案历史表存的paper_id
        $savePaper['paper_id'] = $savePaperId;
        $savePaper['hours'] = isset($hours['hours']) ? $hours['hours'] : 0;

        //返回内容: [题型[题目、选项、答案、解析、分数]]
        config('app.20001.data', $savePaper);
        result('20001');

    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
