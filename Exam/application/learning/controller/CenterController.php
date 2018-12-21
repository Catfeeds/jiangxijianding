<?php

namespace app\learning\controller;

use think\Controller;
use think\Request;
use app\common\service\LearningTopicPaper;
use app\common\service\LearningQuestion;
use app\common\service\LearningHistory;
use app\common\service\LearningMedia;

class CenterController extends Controller
{
    private $Ssetvolume;
    private $Stopic;
    private $Shistory;
    private $Smedia;

    public function __construct(Request $request = null)
    {
        $this->Ssetvolume = new LearningTopicPaper();
        $this->Stopic = new LearningQuestion();
        $this->Shistory = new LearningHistory();
        $this->Smedia = new LearningMedia();
        parent::__construct($request);
    }

    /**
     * 随机答题
     * @user 刘欣 2018/11/5~11:31
     */
    public function randomTopic()
    {
        //1.获取单选，多选，判断题型数据
        $param['type'] = [['=',1],['=',2],['=',3],'or'];
        $topicObject = $this->Stopic->BaseSelect($param);
        //2.转换成数组
        $topicArray = collection($topicObject)->toArray();
        //3.打乱数据
        shuffle($topicArray);
        //4.抽取数据前十条
        $data = array_slice($topicArray,0,10);
        return view('center/random_topic',['data'=>$data]);
    }

    /***
     * 个人中心列表
     * @return \think\response\View
     * @user 刘欣 2018/10/30~13:55
     */
    public function index()
    {
       return view();
    }

    /***
     * 考评人员|普通用户-学习资料列表
     * @return \think\response\View
     * @user 刘欣 2018/10/24~10:16
     */
    public function auditLearning()
    {
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );

        $where['pid'] = '0';
        $where['type'] = '1';
        $data = $this->Smedia->BaseSelectPage($paginate,$where);

        return view('center/audit_learning',['data'=>$data]);
    }

    /***
     * 考评人员|普通用户-开始练习详情
     * @return \think\response\View
     * @user 刘欣 2018/10/24~11:32
     */
    public function auditStart()
    {
        $request = Request::instance()->param();
        $where['pid'] = $request['id'];
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $data = $this->Smedia->BaseSelectPage($paginate,$where);

        return view('center/audit_start',['data'=>$data]);
    }


    /***
     * 考评人员-学习记录列表 暂无
     * @return \think\response\View
     * @user 刘欣 2018/10/24~10:49
     */
    public function auditHistory()
    {
        $request = Request::instance()->param();
        $where['pid'] = $request['id'];
        $where['state'] = '1';
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $data = $this->Smedia->BaseSelectPage($paginate,$where);

        return view('center/audit_history',['data'=>$data]);
    }

    /***
     * 模拟考试|在线答题-学习记录试题列表
     * @return \think\response\View
     */
    public function userHistory()
    {
        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => request()->param()]
        );
        $data = $this->Shistory->BaseSelectPage($paginate);
        foreach ($data as $key => $value)
        {
            $data[$key]['correct_count'] = isset($value['results_correct']) ? substr_count($value['results_correct'],';')+1 : '0';//√
            $data[$key]['error_count'] = isset($value['results_error']) ? substr_count($value['results_error'],';')+1 : '0';//×
        }

        return view('center/user_history',['data'=>$data]);
    }

    /***
     * 模拟考试|在线答题-学习记录详情
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 刘欣 2018/10/24~10:55
     */
    public function onlineTopicAnswer()
    {
        $request = Request::instance()->param();
        $data = $this->Shistory->BaseFind(['id'=> $request['id']]);

        if (!empty($request['flag'])) {
            $data['score'] = '';
        }
        $where['id'] = ['in',$data['result_empty']];
        $resultEmpty = $this->Stopic->selectEachDetail($where);
        $resultsCorrect = $this->resultArray($data['results_correct']);
        $resultError = $this->resultArray($data['results_error']);

        return view('center/online_topic_answer',['resultEmpty'=>$resultEmpty,'resultsCorrect'=>$resultsCorrect,'resultError'=>$resultError,'score'=> $data['score']]);
    }

    /***
     * 模拟考试-试题列表
     * @return \think\response\View
     */
    public function testTopic()
    {
        $where['state'] = '1';
        $where['range'] = '3';
        $paperName = $this->Ssetvolume->selectColumn('id,exam_name',$where);

        return view('center/test_topic',['paperName'=>$paperName]);
    }

    /***
     * 模拟答题-试题详情
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function testTopicStart()
    {
        $request = Request::instance()->param();
        $dataOnly = $this->Ssetvolume->where('state','1')->find($request['id']);
        $where['id'] = ['in',$dataOnly['paper_id']];
        $topic = $this->Stopic->selectEachDetail($where);
        shuffle($topic);
        $examName = $dataOnly->exam_name;

        return view('center/test_topic_start',['topic'=>$topic,'id'=>$request['id'],'dataOnly'=>$dataOnly,'examName'=>$examName]);
    }

    /***
     * 在线答题-试题列表
     * @return \think\response\View
     */
    public function onlineTopic()
    {
        $where['state'] = '1';
        $where['range'] = '1';
        $paperName = $this->Ssetvolume->selectColumn('id,exam_name',$where);

        return view('center/online_topic',['paperName'=>$paperName]);
    }

    /***
     * 在线答题-答题详情
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function topicStart()
    {
        $request = Request::instance()->param();
        $dataOnly = $this->Ssetvolume->where('state','1')->find($request['id']);
        $where['id'] = ['in',$dataOnly['paper_id']];
        $topic = $this->Stopic->selectEachDetail($where);
        shuffle($topic);

        $examName = $dataOnly->exam_name;

        return view('center/topic_start',['topic'=>$topic,'id'=>$request['id'],'dataOnly'=>$dataOnly,'examName'=>$examName]);
    }

    /***
     * 转换json数据返回合并后的数据
     * @param $data
     * @return array
     */
    public function resultArray($data)
    {
        if (empty($data))
        {
            return [];
        }
        $array = explode(";",$data);
        foreach ($array as $k => $v)
        {
            $array[$k] = json_decode($v,true);
        }
        $resultArr = [];
        foreach ($array as $key => $value)
        {
            foreach ($value as $k => $v)
            {
                $result = $this->Stopic->BaseFind(['id'=>$k]);
                $result['user_answer'] = $v;
                $resultArr[] = $result;
            }
        }
        return $resultArr;
    }

    /***
     * 学习资料列表
     * @return \think\response\View
     * @user 刘欣 2018/10/25~14:34
     */
    public function learningList()
    {
        return view();
    }



}