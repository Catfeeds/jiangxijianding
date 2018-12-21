<?php
namespace app\common\model;

class ExamEnroll extends BaseModel
{
    // 自动写入时间戳
    protected $updateTime = false;

    public $status;
    public $source;
    public $result;
    public $cert_way;
    public $thesis_state;
    public $work_level_subject_level;
    public $exam_type;
    public $education;

    public function __construct($data=[])
    {
        parent::__construct($data);
        $arr= config('EnrollStatusText.status');
        $arrsource= config('EnrollStatusText.source');
        $arrexamresult = config('EnrollStatusText.result');
        $certType      = config('EnrollStatusText.cert_way');
        $thesis_state      = config('EnrollStatusText.thesis_state');
        $work_level_subject_level      = config('EnrollStatusText.work_level_subject_level');
        $exam_type     = config('EnrollStatusText.exam_type');
        $education     = config('EnrollStatusText.education');
//        print_r($data);die;
        if(count($data)>0){
            if(key_exists('status',$data)){
                $this->status = $arr[$data['status']];
            }
            if(key_exists('source',$data)){
                $this->source = $arrsource[$data['source']];
            }
            if(key_exists('result',$data)){
                $this->result = $arrexamresult[$data['result']];
            }
            if(key_exists('cert_way',$data)){
                $this->cert_way = $certType[$data['cert_way']];
            }
            if(key_exists('thesis_state',$data)){
                $this->thesis_state = $thesis_state[$data['thesis_state']];
            }
            if(key_exists('work_level_subject_level',$data) && $data['work_level_subject_level']!=null){
                $this->work_level_subject_level = $work_level_subject_level[$data['work_level_subject_level']];
            }
            if(key_exists('exam_type',$data)){
                $this->exam_type = $exam_type[$data['exam_type']];
            }
            if(key_exists('education',$data)){
                $this->education = $education[$data['education']];
            }
        }
    }

    public function getAuditWayAttr($audit_way){
        switch ($audit_way)
        {
            case 0:
                return '线下审核';
                break;
            case 1:
                return '线上审核';
                break;
                default:
                return '未知';
                break;
        }
    }

    /**
     * @param $time
     * @return false|string
     * @user xuweiqi
     */
    public function getExamTimeAttr($time)
    {
        return date('Y-m-d',$time);
    }


    /**
     * @param array $where
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function joinUserById($where = [],$field = '')
    {
        $field="e.*,u.*";
        $data= $this->alias('e')
            ->join("__USER_LOGIN__ u","e.user_login_id=u.id")
            ->field($field)
            ->where($where)
            ->find();
        return $data;
    }


    /**
     * 验证用户基本数据唯一，真实性
     * @param $data
     * @param $field
     * @param string $userid
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function onlyUser($data, $field, $userid = '')
    {
        if ($userid) {
            return $this->where($field, $data)
                ->where('id', 'neq', $userid)
                ->find();
        } else {
            return $this->where($field, $data)
                ->find();
        }

    }

}