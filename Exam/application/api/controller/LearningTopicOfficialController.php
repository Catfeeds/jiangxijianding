<?php

namespace app\api\controller;
use app\common\service\LearningQuestion;
use app\common\service\LearningTopicPaper;
use think\Request;
use app\common\controller\BaseApi;
use app\common\service\Work;
use app\common\service\WorkType;
use think\Session;
use app\common\service\WorkDirection;
use app\common\service\ExamPlan;
use app\common\service\LearningPaperHistory;
use app\common\service\LearningHour;
use app\common\service\LearningAnswerHistory;
use app\common\service\ExamWorkLevel;


class LearningTopicOfficialController extends BaseApi
{
    private $SExamWorkLevel;
    private $Sofficial;
    private $STopicPaper;
    private $SWork;
    private $WorkTypeModel;
    private $SWorkDirection;
    private $SexamPlan;
    private $Stopic;
    private $SPaper;
    private $SlearningTime;
    private $SAnswer;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->SExamWorkLevel = new ExamWorkLevel();
        $this->SAnswer = new LearningAnswerHistory();
        $this->SPaper = new LearningPaperHistory();
        $this->Stopic = new LearningQuestion();
        $this->Sofficial = new LearningQuestion();
        $this->STopicPaper = new LearningTopicPaper();
        $this->SWork = new Work();
        $this->WorkTypeModel = new WorkType();
        $this->SWorkDirection = new WorkDirection();
        $this->SexamPlan = new ExamPlan();
        $this->SlearningTime = new LearningHour();

    }

    /**
     * @user 批量导入 2018/12/17~16:11
     */
    public function topicImport()
    {
        vendor("PHPExcel.PHPExcel");
        $file = request()->file('file');
        if (is_null($file)) {
            return layuiMsg('-5','文件不能为空!');
        }
        $info = $file->validate(['size' => 512000, 'ext' => 'xlsx,xls,csv'])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel');
        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel' . DS . $exclePath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
            echo "<pre>";
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);

            //根据题型 将数据拼接成数组
            $data = [];
            foreach ($excel_array as $k => $v) {
                if (trim($v[7]) == '归属题库')
                {
                    $v[7] = 1;
                } else {
                    $v[7] = 2;
                }
                if (trim($v[12]) == '考生')
                {
                    $v[12] = 0;
                } else {
                    $v[12] = 1;
                }
                if (trim($v[7]) == '在线答题') {
                    $v[7] = 1;
                } else {
                    $v[7] = 2;
                }
                $work = $this->SWork->BaseFind(['name'=>trim($v[9])], 'id');
                $direction = $this->SWorkDirection->BaseFind(['name'=>trim($v[11])],'id');
                //工种，级别，方向
                if (empty($work) or empty($direction) or empty($v[10])) {
                    return layuiMsg('-6','工种，级别，方向，不能为空!');
                }
                $data[$k]['work_id'] = $this->SWork->BaseFind(['name'=>trim($v[9])], 'id')['id'];//工种
                $data[$k]['level_id'] = trim($v[10]); //级别
                $data[$k]['work_direction_id'] = $this->SWorkDirection->BaseFind(['name'=>trim($v[11])],'id')['id']; //方向
                $data[$k]['role'] = $v[12]; //角色
                $data[$k]['range'] = $v[7]; //题库类型
                switch (trim($v[1])) {
                    case '单选题':
                        $data[$k]['answer'] = str_replace("A",0, $v[6]);
                        $data[$k]['answer'] = str_replace("B",1, $data[$k]['answer']);
                        $data[$k]['answer'] = str_replace("C",2, $data[$k]['answer']);
                        $data[$k]['answer'] = str_replace("D",3, $data[$k]['answer']);
                        $data[$k]['topic_name'] = trim($v[0]); //题目
                        $data[$k]['type'] = 1; //类型
                        $data[$k]['option_a'] = trim($v[2]); //a
                        $data[$k]['option_b'] = trim($v[3]); //b
                        $data[$k]['option_c'] = trim($v[4]); //c
                        $data[$k]['option_d'] = trim($v[5]); //d
                        $data[$k]['answer_explain'] = trim($v[8]); //答案解析
                        $data[$k]['create_time'] = trim(date('Y-m-d '));//时间
                        break;
                    case '多选题':
                        $data[$k]['answer'] = str_replace("A",0, $v[6]);
                        $data[$k]['answer'] = str_replace("B",1, $data[$k]['answer']);
                        $data[$k]['answer'] = str_replace("C",2, $data[$k]['answer']);
                        $data[$k]['answer'] = str_replace("D",3, $data[$k]['answer']);
                        $data[$k]['topic_name'] = trim($v[0]); //题目
                        $data[$k]['type'] = 2; //类型
                        $data[$k]['option_a'] = trim($v[2]); //a
                        $data[$k]['option_b'] = trim($v[3]); //b
                        $data[$k]['option_c'] = trim($v[4]); //c
                        $data[$k]['option_d'] = trim($v[5]); //d
                        $data[$k]['answer_explain'] = trim($v[8]); //答案解析
                        $data[$k]['create_time'] = trim(date('Y-m-d '));//时间
                        break;
                    case '判断题':
                        $data[$k]['answer'] = str_replace(0,"正确", $v[6]);
                        $data[$k]['answer'] = str_replace(1,"错误", $data[$k]['answer']);
                        $data[$k]['topic_name'] = trim($v[0]); //题目
                        $data[$k]['type'] = 3; //类型
                        $data[$k]['option_a'] = trim($v[2]); //正确
                        $data[$k]['option_b'] = trim($v[3]); //错误
                        $data[$k]['answer_explain'] = trim($v[8]); //答案解析
                        $data[$k]['create_time'] = trim(date('Y-m-d '));//时间
                        break;
                    case '简答题':
                        $data[$k]['topic_name'] = trim($v[0]); //题目
                        $data[$k]['type'] = 4; //类型
                        $data[$k]['answer'] = trim($v[6]); //答案
                        $data[$k]['answer_explain'] = trim($v[8]); //答案解析
                        $data[$k]['create_time'] = trim(date('Y-m-d '));//时间
                        break;
                    case '论述题':
                        $data[$k]['topic_name'] = trim($v[0]); //题目
                        $data[$k]['type'] = 5; //类型
                        $data[$k]['answer'] = trim($v[6]); //答案
                        $data[$k]['answer_explain'] = trim($v[8]); //答案解析
                        $data[$k]['create_time'] = trim(date('Y-m-d '));//时间
                        break;
                    default:
                        return layuiMsg(-8,'请输入正确题型!');
                        break;
                }
            }
            $res = $this->Stopic->saveAll($data);
            if ($res) {
                return layuiMsg('1','操作成功');
            } else {
                return layuiMsg('-1','失败，请重新操作');
            }
        }
    }

    /**
     * @user 查询试题答案 2018/12/7~11:46
     */
    public function selectAnswer()
    {
        $map = Request::instance()->post();
        $where['id'] = $map['id'];
        $field = ['answer,answer_explain'];
        $data = $this->Stopic->BaseFind($where, $field);

       //分割二维数组
        if (count($map['answer'],1) > 1) {
            $map['answer'] = implode(',',$map['answer']);
        }
        if ($map['answer'] == $data['answer']) {
            return layuiMsg(0, '正确','');
        } else {
            return layuiMsg(1, '错误', $data['answer_explain']);
        }

    }

    public function explodeArray($v)
    {
        foreach ($v as $key => $value) {
            dump($value);die;
        }
    }

    /**
     * 保存用户答案
     * @user 刘欣 2018/12/7~13:50
     */
    public function submitAnswer()
    {
        $user_id = session('user')['id'];
        //角色 0学员 1考评员
        $role = 0;
        //接受参数
//        $map = $this->request->only(['enroll_id','test_type','answer', "start_time","paper_id", "work_id", "level_id", "work_direction_id","duration"], 'post');
        $map = Request::instance()->post();

        //根据角色 分开工种，级别，方向
//        if ($role == 1) {
//            $map['work_id'] = '0';
//            $map['level_id'] = '0';
//            $map['work_direction_id'] = '0';
//        }
        //解析编码格式
        $answerArray = json_decode($map['answer'], true);

        //多个答案切割成一个
        foreach ($answerArray as $k => $v) {
            unset($v['id']);
            $answerArray[$k]['answer'] = $v;
        }
        foreach ($answerArray as $k => $v) {
            $answerArray[$k]['answer'] = implode(',',$v['answer']);
        }

        $questionIds = array_column($answerArray, "id");
        $questionIds = implode(",", $questionIds);
        $where['id'] = ["in", $questionIds];
        $questionList = $this->Stopic->BaseSelect($where, ["id", "answer", "type"]);
        $saveAnswerArray = [];
        $question_count = 0;
        $correct_count = 0;
        $error_count = 0;
        $empty_count = 0;
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
                'enroll_id' => 11,//报名id
                'score'=> $score,
                "create_time"=> time(),

            ];
            $this->SlearningTime->BaseSave($hours);
        }

        $savePaper = [
            "name" => isset($map['name']) ? $map['name'] : '',
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
        $savePaperId = $this->SPaper->BaseSave($savePaper); //learning_paper_history
        foreach ($saveAnswerArray as $k=>$v){
            $saveAnswerArray[$k]["paper_id"] = $savePaperId; //paper_id
        }
        //保存试题答案记录
        $this->SAnswer->BaseSaveAll($saveAnswerArray); //learning_answer_history
        //答案历史表存的paper_id
        $savePaper['paper_id'] = $savePaperId;
        $savePaper['hours'] = isset($hours['hours']) ? $hours['hours'] : 0;

        if ($savePaper) {
            return layuiMsg(1,'交卷成功',$savePaper);
        } else {
            return layuiMsg(0,'交卷失败');
        }

    }


    //对象分割成字符串
    public function strImplode($param)
    {
        $data = collection($param)->toArray();
        $str = '';
        foreach ($data as $k => $v) {
            $str .= $v['id'] . ',';
        }
        return rtrim($str, ',');
    }

    public function workLevelDirectionInsert()
    {
        $map = Request::instance()->post();
        $mapQuestion['work_id'] = $map['work'];
        $mapQuestion['work_direction_id'] = $map['direction'];
        $mapQuestion['level_id'] = $map['level'];
        $mapQuestion['range'] = 2;
        $objQuestion = $this->Stopic->selectQuestion($mapQuestion,'id');
        $volume = [];
        if ($objQuestion) {
            $volume['paper_name'] = $map['work'].$map['direction'].$map['level'].'试卷'.rand(1,100);
            $volume['paper_id'] = $this->strImplode($objQuestion);
            $volume['work_id'] = $map['work'];
            $volume['level_id'] = $map['level'];
            $volume['work_direction_id'] = $map['direction'];
        }
        $res = $this->STopicPaper->save($volume);
        if ($res) {
            return layuiMsg('1','提交成功');
        } else {
            return layuiMsg('2','提交失败');
        }
    }

    public function volumeAdd()
    {
        $map = Request::instance()->post();
        //查询所有鉴定计划所有的工种，级别，方向
        $mapPlan['exam_work.type'] = 5;
        $mapPlan['exam_work_level.type'] = 5;
        $fieldPlan = "`work`.id AS work_id,work_direction.`id` AS work_direction_id,`exam_work_level`.`work_level`,`work`.name AS work_name,work_direction.`name` AS work_direction_name";
        $arrExamPlan = $this->SexamPlan->getAllWorkDirectionLevel($mapPlan,$fieldPlan);
        sort($arrExamPlan);
        //根据鉴定计划所有的工种，级别，方向查询试题
        $volume = [];
        foreach ($arrExamPlan as $key => $value) {
            $mapQuestion['work_id'] = $value['work_id'];
            $mapQuestion['level_id'] = $value['work_level'];
            $mapQuestion['work_direction_id'] = $value['work_direction_id'];
            $mapQuestion['range'] = 2; //模拟考试: 单选题80道;判断题20
            $objQuestion = $this->Stopic->selectQuestion($mapQuestion,'id');
            if ($objQuestion) {
                //为考生组卷
                $volume[$key]['paper_name'] = $value['work_direction_name'].$value['work_name'].$value['work_level'].'试卷'.$key;
                $volume[$key]['paper_id'] = $this->strImplode($objQuestion);
                $volume[$key]['work_id'] = $value['work_id'];
                $volume[$key]['level_id'] = $value['work_level'];
                $volume[$key]['work_direction_id'] = $value['work_direction_id'];
            }
        }
        //生成卷子数
        $num = empty($map['num']) ? 1 : $map['num'];

        //保存试卷
        for ($i=0;$i<$num;$i++) {
            $res = $this->STopicPaper->saveAll($volume);
        }
        if ($res) {
            return layuiMsg('1','提交成功');
        } else {
            return layuiMsg('2','提交失败');
        }
    }

    public function selectLevel()
    {
        $map = Request::instance()->post();

        if ($map['work'] == 0 && $map['direction'] == 0 && $map['level'] == 0) {

            $where = [
                'work'=>$map['work'],
                'level'=>$map['level'],
                'direction'=>$map['direction'],
            ];
            Session::set('threeLevel',$where);
            return layuiMsg('1','提交成功');
        }
        //条件
        $where = [
            'work'=>$map['work'],
            'level'=>$map['level'],
            'direction'=>$map['direction'],
        ];

        //条件信息
        Session::set('threeLevel',$where);

        //标题
        switch ($map['level']) {
            case '1':
                $level_name = '高级技师';
                break;
            case '2':
                $level_name = '技师';
                break;
            case '3':
                $level_name = '高级';
                break;
            case '4':
                $level_name = '中级';
                break;
            case '5':
                $level_name = '初级';
                break;
        }
        $title = [
            'work_name'=> $this->SWork->BaseFind(['id'=>$map['work']])['name'],
            'level_name' => $level_name,
            'direction_name' =>!empty($map['direction']) ? $this->SWorkDirection->BaseFind(['id'=>$map['direction']])['name'] : '0'
        ];

        return layuiMsg('1','提交成功',$title);
    }

    public function imgUpload()
    {
        $file = Request::instance()->file('file');
        if(empty($file)){
            $result["code"] = "1";
            $result["msg"] = "请选择图片";
            $result['data']["src"] = '';
        }else{
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/learning/img' );
            if($info){
                $name_path =str_replace('\\',"/",$info->getSaveName());
                //成功上传后 获取上传信息
                $result["code"] = '0';
                $result["msg"] = "上传成功";
                $result['data']["src"] = "/uploads/learning/img/".$name_path;
            }else{
                // 上传失败获取错误信息
                $result["code"] = "2";
                $result["msg"] = "上传出错";
                $result['data']["src"] ='';
            }
        }

        return json_encode($result);
    }

    public function setvolumeAdd()
    {
        if(Request::instance()->isPost())
        {
            $webData = Request::instance()->post();

            $data = [
                'paper_id' => $webData['paper_id'],
                'total_score' => $webData['total_score'],
                'paper_name' => $webData['paper_name'],
                'level_id' => $webData['level_id'],
                'work_id' => $webData['work_id'],
                'work_direction_id' => $webData['work_direction_id'],
            ];

            $eachSore = [
                1 => isset($webData['single']) ? $webData['single'] : "0",
                2 => isset($webData['more']) ? $webData['more'] : "0",
                3 => isset($webData['judge']) ? $webData['judge'] : "0",
            ];

            $data['each_score'] = json_encode($eachSore);
            $this->Sofficial->data($data);

            $data['create_time'] = time();
            $result = $this->STopicPaper
                ->isUpdate(false)
                ->data($data,true)
                ->save();

            if ($result) {
                $data['status'] = 1;
                $data['msg'] = '操作成功';
            } else {
                $data['status'] = 3;
                $data['msg'] = '操作失败';
            }
            return $data;
        }
    }

    //选项题
    public function addChoose()
    {
        $request = Request::instance();
        if($request->isPost())
        {
            $webData = $request->post();
            unset($webData['file']);
            //验证
            if (!array_key_exists('answer', $webData)) {
                $data['status'] = 2;
                $data['msg'] = '正确答案必须选择';
                return $data;
            }
            if (!array_key_exists('range', $webData)) {
                $data['status'] = 3;
                $data['msg'] = '归属题库必须选择';
                return $data;
            }
            if (!array_key_exists('topic_level', $webData)) {
                $data['status'] = 4;
                $data['msg'] = '难易程度必须选择';
                return $data;
            }

            $webData['type'] = count($webData['answer']) > 1 ? '2' : '1';//题型
            $webData['answer'] = implode(",",$webData['answer']);//答案

            $webData['knowledge_id'] = '110'; //细目表id

            //工种，级别，方向
            $threeLevel = Session::get('threeLevel');
            $webData['work_id'] = $threeLevel['work'];
            $webData['level_id'] = $threeLevel['level'];
            $webData['work_direction_id'] = $threeLevel['direction'];

            $this->Sofficial->data($webData);

            if ($webData['id']) {

                $webData['update_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(true)
                    ->data($webData,true)
                    ->save();
            } else {

                $webData['create_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(false)
                    ->data($webData,true)
                    ->save();
            }

            if ($result) {
                $data['status'] = 1;
                $data['msg'] = '操作成功';
            } else {
                $data['status'] = 3;
                $data['msg'] = '操作失败';
            }
            return $data;
        }
    }

    //判断题
    public function addJudge(){
        $request = Request::instance();
        if($request->isPost())
        {
            $webData = $request->post();
            unset($webData['file']);
            //验证
            if (!array_key_exists('answer', $webData)) {
                $data['status'] = 2;
                $data['msg'] = '正确答案必须选择';
                return $data;
            }
            if (!array_key_exists('range', $webData)) {
                $data['status'] = 3;
                $data['msg'] = '归属题库必须选择';
                return $data;
            }
            if (!array_key_exists('topic_level', $webData)) {
                $data['status'] = 4;
                $data['msg'] = '难易程度必须选择';
                return $data;
            }

            $webData['knowledge_id'] = '110'; //细目表id
            $webData['type'] = 3; //类型

            //工种，级别，方向
            $threeLevel = Session::get('threeLevel');
            $webData['work_id'] = $threeLevel['work'];
            $webData['level_id'] = $threeLevel['level'];
            $webData['work_direction_id'] = $threeLevel['direction'];


            $webData['option_a'] = '正确';
            $webData['option_b'] = '错误';

            $this->Sofficial->data($webData);

            if ($webData['id']) {

                $webData['update_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(true)
                    ->data($webData,true)
                    ->save();
            } else {
                $webData['create_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(false)
                    ->data($webData,true)
                    ->save();
            }

            if ($result) {
                $data['status'] = 1;
                $data['msg'] = '操作成功';
            } else {
                $data['status'] = 3;
                $data['msg'] = '操作失败';
            }
            return $data;
        }
    }

    public function addEmpty()
    {
        $request = Request::instance();

        if($request->isPost())
        {
            $webData = $request->post();
            unset($webData['file']);
            //验证
            if (!array_key_exists('range', $webData)) {
                $data['status'] = 3;
                $data['msg'] = '归属题库必须选择';
                return $data;
            }
            if (!array_key_exists('topic_level', $webData)) {
                $data['status'] = 4;
                $data['msg'] = '难易程度必须选择';
                return $data;
            }
            if (!isset($webData['answer'])) {
                $data['status'] = 5;
                $data['msg'] = '答案必须填写';
                return $data;
            }
            //答案
            $webData['answer'] = implode('|@|',$webData['answer']);

            $webData['knowledge_id'] = '110'; //细目表id
            $webData['type'] = 4; //类型

            //工种类型id或工种id或级别（假数据）
            $threeLevel = Session::get('threeLevel');

            switch (count($threeLevel))
            {
                case 1:
                    $webData['work_id'] = $threeLevel['work'];
                    break;
                case 2:
                    $webData['work_id'] = $threeLevel['work'];
                    $webData['level_id'] = $threeLevel['level'];
                    break;
            }

            $this->Sofficial->data($webData);

            if ($webData['id']) {

                $webData['update_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(true)
                    ->data($webData,true)
                    ->save();
            } else {
                $webData['create_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(false)
                    ->data($webData,true)
                    ->save();
            }

            if ($result) {
                $data['status'] = 1;
                $data['msg'] = '操作成功';
            } else {
                $data['status'] = 3;
                $data['msg'] = '操作失败';
            }
            return $data;
        }
    }

    public function officialAddBrief()
    {
        $request = Request::instance();
        if($request->isPost())
        {
            $webData = $request->post();

            unset($webData['file']);
            //验证
            if (!array_key_exists('answer', $webData)) {
                $data['status'] = 2;
                $data['msg'] = '正确答案必须选择';
                return $data;
            }
            if (!array_key_exists('range', $webData)) {
                $data['status'] = 3;
                $data['msg'] = '归属题库必须选择';
                return $data;
            }
            if (!array_key_exists('topic_level', $webData)) {
                $data['status'] = 4;
                $data['msg'] = '难易程度必须选择';
                return $data;
            }

            $webData['knowledge_id'] = '110'; //细目表id
            $webData['type'] = 5; //类型

            //工种，级别，方向
            $threeLevel = Session::get('threeLevel');
            $webData['work_id'] = $threeLevel['work'];
            $webData['level_id'] = $threeLevel['level'];
            $webData['work_direction_id'] = $threeLevel['direction'];

            $this->Sofficial->data($webData);

            if ($webData['id']) {

                $webData['update_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(true)
                    ->data($webData,true)
                    ->save();
            } else {
                $webData['create_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(false)
                    ->data($webData,true)
                    ->save();
            }

            if ($result) {
                $data['status'] = 1;
                $data['msg'] = '操作成功';
            } else {
                $data['status'] = 3;
                $data['msg'] = '操作失败';
            }
            return $data;
        }
    }

    public function officialAddComposition()
    {
        $request = Request::instance();
        if($request->isPost())
        {
            $webData = $request->post();

            //验证
            if (!array_key_exists('range', $webData)) {
                $data['status'] = 3;
                $data['msg'] = '归属题库必须选择';
                return $data;
            }
            if (!array_key_exists('topic_level', $webData)) {
                $data['status'] = 4;
                $data['msg'] = '难易程度必须选择';
                return $data;
            }

            $webData['knowledge_id'] = '110'; //细目表id
            $webData['type'] = 6; //类型

            //工种类型id或工种id或级别（假数据）
            $threeLevel = Session::get('threeLevel');

            switch (count($threeLevel))
            {
                case 1:
                    $webData['work_id'] = $threeLevel['work'];
                    break;
                case 2:
                    $webData['work_id'] = $threeLevel['work'];
                    $webData['level_id'] = $threeLevel['level'];
                    break;
            }

            $this->Sofficial->data($webData);

            if ($webData['id']) {

                $webData['update_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(true)
                    ->data($webData,true)
                    ->save();
            } else {
                $webData['create_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(false)
                    ->data($webData,true)
                    ->save();
            }

            if ($result) {
                $data['status'] = 1;
                $data['msg'] = '操作成功';
            } else {
                $data['status'] = 3;
                $data['msg'] = '操作失败';
            }
            return $data;
        }
    }

    public function officialAddDescribe()
    {
        $request = Request::instance();
        if($request->isPost())
        {
            $webData = $request->post();

            //验证
            if (!array_key_exists('range', $webData)) {
                $data['status'] = 3;
                $data['msg'] = '归属题库必须选择';
                return $data;
            }
            if (!array_key_exists('topic_level', $webData)) {
                $data['status'] = 4;
                $data['msg'] = '难易程度必须选择';
                return $data;
            }

            $webData['knowledge_id'] = '110'; //细目表id
            $webData['type'] = 7; //类型

            //工种，级别，方向
            $threeLevel = Session::get('threeLevel');
            $webData['work_id'] = $threeLevel['work'];
            $webData['level_id'] = $threeLevel['level'];
            $webData['work_direction_id'] = $threeLevel['direction'];

            $this->Sofficial->data($webData);

            if ($webData['id']) {

                $webData['update_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(true)
                    ->data($webData,true)
                    ->save();
            } else {
                $webData['create_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(false)
                    ->data($webData,true)
                    ->save();
            }

            if ($result) {
                $data['status'] = 1;
                $data['msg'] = '操作成功';
            } else {
                $data['status'] = 3;
                $data['msg'] = '操作失败';
            }
            return $data;
        }
    }

    public function officialAddAnalyze()
    {
        $request = Request::instance();
        if($request->isPost())
        {
            $webData = $request->post();

            //验证
            if (!array_key_exists('range', $webData)) {
                $data['status'] = 3;
                $data['msg'] = '归属题库必须选择';
                return $data;
            }
            if (!array_key_exists('topic_level', $webData)) {
                $data['status'] = 4;
                $data['msg'] = '难易程度必须选择';
                return $data;
            }

            $webData['knowledge_id'] = '110'; //细目表id
            $webData['type'] = 8; //类型

            //工种类型id或工种id或级别（假数据）
            $threeLevel = Session::get('threeLevel');

            switch (count($threeLevel))
            {
                case 1:
                    $webData['work_id'] = $threeLevel['work'];
                    break;
                case 2:
                    $webData['work_id'] = $threeLevel['work'];
                    $webData['level_id'] = $threeLevel['level'];
                    break;
            }

            $this->Sofficial->data($webData);

            if ($webData['id']) {

                $webData['update_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(true)
                    ->data($webData,true)
                    ->save();
            } else {
                $webData['create_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(false)
                    ->data($webData,true)
                    ->save();
            }

            if ($result) {
                $data['status'] = 1;
                $data['msg'] = '操作成功';
            } else {
                $data['status'] = 3;
                $data['msg'] = '操作失败';
            }
            return $data;
        }
    }

    public function officialAddOperation()
    {
        $request = Request::instance();
        if($request->isPost())
        {
            $webData = $request->post();

            //验证
            if (!array_key_exists('range', $webData)) {
                $data['status'] = 3;
                $data['msg'] = '归属题库必须选择';
                return $data;
            }
            if (!array_key_exists('topic_level', $webData)) {
                $data['status'] = 4;
                $data['msg'] = '难易程度必须选择';
                return $data;
            }

            $webData['knowledge_id'] = '110'; //细目表id
            $webData['type'] = 9; //类型

            //工种类型id或工种id或级别（假数据）
            $threeLevel = Session::get('threeLevel');

            switch (count($threeLevel))
            {
                case 1:
                    $webData['work_id'] = $threeLevel['work'];
                    break;
                case 2:
                    $webData['work_id'] = $threeLevel['work'];
                    $webData['level_id'] = $threeLevel['level'];
                    break;
            }

            $this->Sofficial->data($webData);

            if ($webData['id']) {

                $webData['update_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(true)
                    ->data($webData,true)
                    ->save();
            } else {
                $webData['create_time'] = time();
                $result = $this->Sofficial
                    ->isUpdate(false)
                    ->data($webData,true)
                    ->save();
            }

            if ($result) {
                $data['status'] = 1;
                $data['msg'] = '操作成功';
            } else {
                $data['status'] = 3;
                $data['msg'] = '操作失败';
            }
            return $data;
        }
    }

    public function delOffical()
    {
        if (Request::instance()->isPost())
        {
            $request = Request::instance();
            $arrData = $request->post();
            if (!$arrData && empty($arrData['id'])){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }

            //工种类型id或工种id或级别（假数据）
            $threeLevel = Session::get('threeLevel');

            switch (count($threeLevel))
            {
                case 1:
                    $webData['work_id'] = $threeLevel['work'];
                    break;
                case 2:
                    $webData['work_id'] = $threeLevel['work'];
                    $webData['level_id'] = $threeLevel['level'];
                    break;
            }

            $objDel = $this->Sofficial->destroy($arrData);
            if ($objDel){
                $arrMsg['status'] = 1;
                $arrMsg['msg'] = "删除成功";
                return $arrMsg;
            }else{
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "删除失败";
                return $arrMsg;
            }
        }
    }

}