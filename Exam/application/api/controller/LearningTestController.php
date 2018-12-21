<?php

namespace app\api\controller;
use think\Request;
use app\common\service\LearningQuestion;
use app\common\service\LearningTopicPaper;
use app\common\controller\BaseApi;
use app\common\service\LearningHistory;
use app\common\service\LearningTopicLog;
use app\common\service\Work;
use app\common\service\WorkType;

use think\Session;

class LearningTestController extends BaseApi
{
    private $Stopic;
    private $Ssetvolume;
    private $Shistory;
    private $StopicLog;
    private $Swork;
    private $Sworktype;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->Stopic = new LearningQuestion();
        $this->Ssetvolume = new LearningTopicPaper();
        $this->Shistory = new LearningHistory();
        $this->StopicLog = new LearningTopicLog();
        $this->Swork = new Work();
        $this->Sworktype = new WorkType();
    }

    /***
     * @return array
     * @user 刘欣 2018/11/8~16:56
     */
    public function selectLevel()
    {
        $request = Request::instance()->post();
        $key = array_keys($request);
        $endKey = end($key);

        $array = [];
        if ($endKey == 'work_type')
        {
            //查询工种类型下有没有工种返回提示
            $arrWorkType['work_type_id'] = isset($request['work_type']) ? $request['work_type'] : '0';
            $arrWorkType['status'] = 1;
            $data = $this->SWork->BaseSelect($arrWorkType);
            if ($data){
                return layuiMsg('-1', '请选择该工种类型下对应的工种');
            } else {
                $array['work_type'] = $this->WorkTypeModel->BaseFind(['id'=>$request['work_type']])['name'];
                $workTypeSession = ['work_type'=>$request['work_type']];
                Session::set('threeLevel',$workTypeSession);
            }
        } else if ($endKey == 'work')
        {
            //查询工种下有没有级别返回提示
            $data = true;
            if ($data){
                return layuiMsg('-1', '请选择该工种类型下对应的工种');
            } else {
                $array['level'] = isset($request['level']) ? $request['level'] : '0';
                $levelSession = [
                    'work'=>$request['work'],
                    'work_type'=>$request['work_type']
                ];
                Session::set('threeLevel',$levelSession);
            }
        } else if($endKey == 'level')
        {
            $array['work_type'] = $this->WorkTypeModel->BaseFind(['id'=>$request['work_type']])['name'];
            $array['work'] = $this->SWork->BaseFind(['id'=>$request['work']])['name'];
            $array['level'] = isset($request['level']) ? $request['level'] : '0';
            $levelSession = [
                'work'=>$request['work'],
                'work_type'=>$request['work_type'],
                'level'=>$request['level']
            ];
            Session::set('threeLevel',$levelSession);
        }
        return layuiMsg('1','提交成功',$array);
    }
    /***
     * 练习题答卷
     * @return array
     * @user 刘欣 2018/11/8~16:56
     */
    public function randomTopicAdd()
    {
        if (Request::instance()->isPost()) {
            //1.返回无效请求
            $request = Request::instance()->post();
            $time = $request['start_time'];
            unset($request['start_time']);
            if (empty($request)) {
                return layuiMsg('-1','请勿提交空卷!');
            }

            //2.根据题id查询答案对比
            $results = 0;
            $resultsCorrect = [];//正确试题
            $resultsError = [];//错误试题
            foreach ($request as $key => $value)
            {
                //判断题
                if ($key == 'judge') {
                    foreach ($value as $k => $v)
                    {
                       $answer = $this->Stopic->BaseFind(['id' => $k])['answer'];
                        $answer = $answer == 'A' ? '正确' : '错误';
                       if ($answer == $value)
                       {
                           $resultsCorrect[] = json_encode([$k=>'正确']);
                           ++$results;
                       } else {
                           $resultsError[] = json_encode([$k=>'错误']);
                       }
                    }
                }
                //多选题
                if ($key == 'many') {
                    foreach ($value as $k => $v)
                    {
                        $answer = $this->Stopic->BaseFind(['id' => $k])['answer'];
                        $topicAnswer = implode(',',$value[$k]);
                        if ($answer == $topicAnswer)
                        {
                            $resultsCorrect[] = json_encode([$k=>$v]);
                            ++$results;
                        }else {
                            $resultsError[] = json_encode([$k=>$v]);
                        }
                    }
                }
                //单选题
                if ($key == 'single') {
                    foreach ($value as $k => $v)
                    {
                        $answer = $this->Stopic->BaseFind(['id' => $k])['answer'];
                        if ($answer == $v)
                        {
                            $resultsCorrect[] = json_encode([$k=>$v]);
                            ++$results;
                        } else {
                            $resultsError[] = json_encode([$k=>$v]);
                        }
                    }
                }
            }

            $data = [
                'user_id' => Session::get('user')['id'],
                'results_correct' => implode(';',$resultsCorrect),
                'results_error' => implode(';',$resultsError),
                'start_time' => $time,
                'paper_name' => '在线练习题',
                'score' => $results,
                'range'=> '在线练习题',
                'stop_time' => time(),
                'create_time' => time(),

            ];

            //存入历史记录表
            $historyIp = $this->Shistory->create($data);
            if ($historyIp) {
                return layuiMsg('1','提交成功',['score'=> $results]);
            } else {
                return layuiMsg('-1','提交失败');
            }

        }
    }

    /***
     * 测试试卷交卷过程
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 刘欣 2018/11/8~14:05
     */
    public function add()
    {
        if (Request::instance()->isPost()) {
            //请求
            $request = Request::instance()->post();
            $except = Request::instance()->except(['id','start_time']);
            if (empty($except)) {
                return layuiMsg('-1','请勿提交空卷!');
            }

            //查询未填试题
            $dataOnly = $this->Ssetvolume->find($request['id']);

            //数据库试题号
            $dataDatabase = explode(',',$dataOnly['paper_id']);
            //用户填写题号
            $dataUserId = [];
            //用户填写答案
            $dataUserValue = [];
            foreach ($request as $key => $value)
            {
                //判断题
                if ($key == 'judge') {
                    foreach ($value as $k => $v)
                    {
                        $dataUserId[] = $k;
                        $dataUserValue[$k] = $v == 'A' ? '正确' : '错误';
                    }
                }
                //多选题
                if ($key == 'many') {
                    foreach ($value as $k => $v)
                    {
                        $dataUserId[] = $k;
                        $dataUserValue[$k] = implode(',',$v);
                    }
                }
                //单选题
                if ($key == 'single') {
                    foreach ($value as $k => $v)
                    {
                        $dataUserId[] = $k;
                        $dataUserValue[$k] = $v;
                    }
                }
            }
            //用户未填写试题id
            $arrayDiff = array_diff($dataDatabase,$dataUserId);

            //查询试题答案
            $where['id'] = ['in',$dataOnly['paper_id']];
            $field = 'answer,id,range';
            $topic = $this->Stopic->selectEachDetail($where,$field);
            //数据库试题正确答案
            $dataDatabaseAnswer = [];
            //归属题库
            $range = '';
            foreach ($topic as $key => $value)
            {
                if (!$range)  $range = $value['range'];
                $dataDatabaseAnswer[$value['id']] = $value['answer'];
            }

            //进行对比
            $correctResults = [];//用户考试正确成绩
            $errorResults = [];//用户考试正确成绩
            $score = 0;//得分

            foreach ($dataDatabaseAnswer as $key => $value) {
                foreach ($dataUserValue as $k => $v)
                {
                    if ($key == $k) {
                        if ($value == $v) {
                            $correctResults[] = json_encode([$k=>$v]);
                            $score += 1;
                        } else {
                            $errorResults[] = json_encode([$k=>$v]);;
                        }
                    }
                }
            }

            //学时
            if ($score >= 60)
            {
                $data = [
                    'topic_name' => $request['paper_name'],
                    'user_id' => Session::get('user')['id'],
                    'learn_time'=> '1',
                    'score' => $score
                ];
                $this->StopicLog->BaseSave($data);
            }

            //成绩
            $data = [
                'user_id' => Session::get('user')['id'],
                'result_empty' => implode(',',$arrayDiff),
                'results_correct' => implode(';',$correctResults),
                'results_error' => implode(';',$errorResults),
                'start_time' => $request['start_time'],
                'paper_name' => $request['paper_name'],
                'score' => $score,
                'range'=> $range,
                'stop_time' => time(),
                'create_time' => time(),

            ];

            //存入历史记录表
            $historyIp = $this->Shistory->create($data);

            if ($dataOnly['is_answer'] == '-1') {
                //只提示分数
                return layuiMsg('1','返回分数',['score'=>$score]);
            } else {
                //重定向到历史记录页
                return layuiMsg('2','重定向到历史记录页',['ip'=>$historyIp['id']]);
            }
        }
    }

}