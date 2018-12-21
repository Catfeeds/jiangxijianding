<?php
namespace app\learning\controller;
use think\Controller;
use think\Request;
use app\common\service\LearningQuestion;
use app\common\service\LearningTopicPaper;
use app\common\service\LearningHistory;

class TestController extends Controller
{
    private $Stopic;
    private $Ssetvolume;
    private $Shistory;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->Stopic = new LearningQuestion();
        $this->Ssetvolume = new LearningTopicPaper();
        $this->Shistory = new LearningHistory();
    }

    public function index()
    {
        $request = Request::instance()->param();

        if (Request::instance()->isGet() && $request) {

            $dataOnly = $this->Ssetvolume->where('state','1')->find($request['id']);

            $where['id'] = ['in',$dataOnly['paper_id']];

            $topic = $this->Stopic->selectEachDetail($where);

            if ($topic) {
                $topic[0]->paper_name = $dataOnly->paper_name;
                shuffle($topic);
            }

            $this->assign('topic', $topic);
            $this->assign('id', $request['id']);
            $this->assign('dataOnly',$dataOnly);
        }

        //科目列表
        $where['state'] = '1';
        $paperName = $this->Ssetvolume->selectColumn('id,exam_name',$where);

        return view('test/index',['paperName'=>$paperName]);
    }

    public function show()
    {
        $request = Request::instance()->get();

        $data = $this->Shistory->BaseFind(['id'=> $request['id']]);

        if (!empty($request['flag'])) {
            $data['score'] = '';
        }

        $where['id'] = ['in',$data['result_empty']];
        $resultEmpty = $this->Stopic->selectEachDetail($where);
        $resultsCorrect = $this->resultArray($data['results_correct']);
        $resultError = $this->resultArray($data['results_error']);

        return view('test/show',['resultEmpty'=>$resultEmpty,'resultsCorrect'=>$resultsCorrect,'resultError'=>$resultError,'score'=> $data['score']]);
    }

    public function resultArray($data)
    {
        if (empty($data)) {
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
                $result['user_answer'] = is_array($v) ? implode(',',$v) : $v;
                $resultArr[] = $result;
            }
        }
        return $resultArr;
    }

}