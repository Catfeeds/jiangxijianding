<?php

namespace app\common\service;

use app\common\model\ExamEnroll as MExamEnroll;
use think\db;

/**
 * Class ExamEnroll
 * @package app\common\service
 */
class ExamEnroll extends MExamEnroll
{

    private $SLearningHour;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->SLearningHour = new LearningHour();
    }

    public function getWorkDirectionLevel($field, $where)
    {
        return $this
            ->join('__WORK__','exam_enroll.work_id = `work`.id','left')
            ->join('__WORK_DIRECTION__','`exam_enroll`.work_direction_id = work_direction.work_id','left')
            ->field($field)
            ->where($where)
            ->select();
    }


    /**
     * app考生获取我的报名
     * @param $param
     * @return string
     * @user 李海江 2018/11/19~6:50 PM
     */
    public function appMyEnroll($param)
    {

        $appSatus = [
            config('ExamEnrollStatus.checkpass') => '待缴费',
            config('ExamEnrollStatus.huan') => '待缴费',
            config('ExamEnrollStatus.huanfalse') => '待缴费',
            config('ExamEnrollStatus.payost') => '待缴费',
            config('ExamEnrollStatus.paydelayed') => '待考试',
            config('ExamEnrollStatus.paypass') => '待考试',
            config('ExamEnrollStatus.printticket') => '待考试',
        ];
        $join = array(
            ["__EXAM_PLAN__", 'enroll.exam_plan_id=exam_plan.id'],
            ["__WORK__", "enroll.work_id=`work`.id"],
            ["__WORK_DIRECTION__", "enroll.work_direction_id=work_direction.id", 'left'],
        );
        $field = ['exam_plan.title', 'work.name as wname', 'work_direction.name as dname', 'work_level_subject_level', 'exam_enroll.status', 'theory_end_time', 'comprehen_end_time', 'operation_end_time', 'theory', 'comprehen', 'operation'];
        $data = $this->BaseJoinSelect('enroll', $join, $param, $field, '', '', '');
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['work_level_subject_level'] = $data[$i]->work_level_subject_level;
            $a = array($data[$i]['theory_end_time'], $data[$i]['comprehen_end_time'], $data[$i]['operation_end_time']);
            $subject = [];
            if (in_array($data[$i]['status'], [config('ExamEnrollStatus.payost'), config('ExamEnrollStatus.paypass'), config('ExamEnrollStatus.printticket')])) {
                if ($data[$i]['theory']) $subject[0] = '理论';
                if ($data[$i]['comprehen']) $subject[1] = '综合';
                if ($data[$i]['operation']) $subject[2] = '实操';
            }
            $data[$i]['subject'] = $subject;
            if (max($a) > time()) {
                $data[$i]['status'] = '考试结束';
            } else {
                $data[$i]['status'] = $appSatus[$data[$i]['status']];
            }
            unset($data[$i]['theory_end_time'], $data[$i]['comprehen_end_time'], $data[$i]['operation_end_time'], $data[$i]['theory'], $data[$i]['comprehen'], $data[$i]['operation']);
        }
        return $data;
    }


    /**
     * 考评员我的报名
     * @param $param
     * @return string
     * @throws \think\exception\DbException
     * @user 李海江 2018/12/3~5:48 PM
     */
    public function appStaffEnroll($param)
    {
        $join = array(
            ["__EXAM_PLAN__", 'enroll.exam_plan_id=exam_plan.id'],
            ["__WORK__", "enroll.work_id=`work`.id"],
            ["__WORK_DIRECTION__", "enroll.work_direction_id=work_direction.id", 'left'],
        );
        $field = ['exam_plan.id', 'exam_plan.title', 'work.name as wname', 'work_direction.name as dname', 'exam_enroll.status', 'work_level_subject_level'];
        $data = $this->BaseJoinSelect('enroll', $join, $param, $field, '', '', '');

        for ($i = 0; $i < count($data); $i++) {
            //查询我的分数
            $res = $this->SLearningHour->getHour(['enroll_id' => $data[$i]['id'], 'user_id' => appGetUid()]);
            $hours = empty($res) ? 0 : $res[0]['hours'];
            if ($hours < 40) {
                $data[$i]['status'] = '进行中';
                $data[$i]['loading'] = $hours;
            } else {
                $data[$i]['status'] = '已完成';
            }
            $data[$i]['work_level_subject_level'] = $data[$i]->work_level_subject_level;
        }
        return $data;
    }

    /**
     * 查询报名表exam_enroll 数据
     * @param string $map
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function ExamEnroll($map = '')
    {
        $field = "ee.*,exam_plan.work_type_name,exam_plan.audit_endtime,exam_plan.enroll_endtime,exam_plan.exam_time, work.id as wid,work.name as workname, work.id as wid, work_direction.name as directionname,work_direction.id as did, ee.work_level_subject_level as level,userinfo.username";
        $join = array(
            ["__EXAM_PLAN__", "ee.exam_plan_id=exam_plan.id"],
            ["__USERINFO__", "userinfo.user_login_id=ee.user_login_id", "left"],
            ["__WORK__", "ee.work_id=`work`.id"],
            ["__WORK_DIRECTION__", "ee.work_direction_id=work_direction.id",'left'],
        );
        $data = $this->BaseJoinSelect('ee', $join, $map, [$field], '', '', "ee.id");
        return $data;
    }

    public function getWorkList($where = [], $field = [], $order = "id")
    {
        return $this
            ->join("__WORK__", "`work`.id = exam_enroll.work_id")
            ->join("__WORK_DIRECTION__", "work_direction.id=exam_enroll.work_direction_id")
            ->join("__LEARNING_HOUR__", "learning_hour.enroll_id=exam_enroll.id and exam_enroll.user_login_id = learning_hour.user_id", 'left')
            ->where($where)
            ->field($field)
            ->group('enroll_id')
            ->order($order)
            ->select();
    }

    public function getWorkByUserId($where = [], $field, $order = "")
    {
        return $this
            ->join("__WORK__", "`work`.id = exam_enroll.work_id")
            ->join("__WORK_DIRECTION__", "work_direction.work_id=`work`.id")
            ->where($where)
            ->field($field)
            ->order($order)
            ->group("exam_enroll.work_id")
            ->select();
    }

    /**
     * 连接user_login表 获取一条数据
     * @param array $where
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function joinUserById($where = [], $field = '')
    {
        $field = "e.*,u.*";
        $data = $this->alias('e')
            ->join("__USER_LOGIN__ u", "e.user_login_id=u.id")
            ->field($field)
            ->where($where)
            ->find();
        return $data;
    }


    /**
     * 根据条件修改
     * @param string $data
     * @param string $where
     * @return false|int|string
     */
    public function updByid($data = '', $where = '')
    {
        // return
        $this->BaseUpdate($data, $where);
        echo $this->getLastSql() . '<br>';
    }

    /**
     * 添加数据
     * @param string $data
     * @param string $where
     * @return false|int|string
     */
    public function insByid($data)
    {
        return $this->BaseSave($data);
    }

    /**
     * 验证用户基本数据唯一，真实性
     * @param string     data 要验证的数据
     * @param string    field 要查询字段
     * @param int       userid 用户id
     * @return bool
     * @user liuxin     2018/9/17
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


    /**
     * @验证数据user_login_id exam_plan_id work_id work_direction_id work_level_id唯一性
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkUser($map)
    {
        if (!empty($map)) {
            return $this
                ->join("__USER_LOGIN__", "user_login.id=exam_enroll.user_login_id")
                ->where("user_login_id", 'in', $map['user_login_id'])
                ->where("exam_plan_id", 'in', $map['exam_plan_id'])
                ->where("work_id", 'in', $map['work_id'])
                ->where("work_direction_id", 'in', $map['work_direction_id'])
                ->where("work_level_id", 'in', $map['work_level_id'])
                ->select();
        }
    }


    /**根据手机号取出 获取当前用户的信息
     * @return mixed
     * @user xuweiqi
     */
    public function getUserLoginInfo()
    {
        $userLoginId = $this->SUserLogin->BaseFind(['mobile' => session('user')['mobile']]);
        return $userLoginId;
    }

    /**根剧当前用户查出 当前用户的报考信息
     * @return mixed
     * @user xuweiqi
     */
    public function getExamEnrollJoinInfo()
    {
        $userLoginId = $this->getUserLoginInfo();
        //查询报名表各关联信息
        $field = "ee.*,ep.title,w.name as workname,wd.name as directionname,w.id as wid,wd.id as did";
        $join = array(
            ['__EXAM_PLAN__ ep', "ee.exam_plan_id=ep.id"],
            ["__USERINFO__ u", "u.user_login_id=ee.user_login_id", "left"],
            ["__WORK__ w", "ee.work_id=w.id"],
            ["__WORK_DIRECTION__ wd", "ee.work_direction_id=wd.id"],
        );
        $examJoinData = $this->SExamEnroll->BaseJoinSelect('ee', $join, ['ee.user_login_id' => $userLoginId['id']], [$field]);
        return $examJoinData;
    }
    public function getImg($where=[])
    {
        //查询报名表各关联信息
        $field = "exam_enroll.id,userinfo.avatar";
        $join = array(
            ["__USERINFO__", "`userinfo`.`user_login_id` = `exam_enroll`.`user_login_id`", "left"],
        );
        $examJoinData = $this->BaseJoinFind('exam_enroll', $join, ['exam_enroll.id' => $where['id']], [$field]);
        return $examJoinData;
    }

    /**根据条件查找examenroll 数据
     * @param $map
     * @return data
     * @user xuweiqi
     */
    public function findExamEnroll($map)
    {
        return $this->BaseFind($map);
    }

    /**查询examenroll 关联数据
     * @param $map
     * @return data
     * @user xuweiqi
     */
    public function selectExamEnroll($map)
    {
        return $this->ExamEnroll($map)[0];
    }

    /**
     * 根据work和level统计数量
     * /**
     * [selectExamGrade 查出考试成绩通过者的信息考试信息]
     * @param  [type] $where [description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    public function selectExamGrade($where)
    {
        $field = "distinct ee.id,ep.title,g.id as gid,g.work_name,ee.work_level_subject_level,g.id_card,g.TicketNum,wd.name,ul.id,c.cert_way,eo.pay_state,ul.name as username,c.certificate_no";
        $join = array(
            ['__EXAM_PLAN__ ep', 'ee.exam_plan_id=ep.id'],
            ['__USER_LOGIN__ ul', 'ee.user_login_id=ul.id', 'left'],
            ['__GRADE__ g', 'ul.id_card=g.id_card'],
            ['__WORK_DIRECTION__ wd', 'g.work_direction_id=wd.id'],
            ['__CERTIFICATE__ c', 'ee.id=c.exam_enroll_id', 'right'],
            ['__EXAM_ORDER__ eo', 'eo.exam_plan_id=ep.id', 'left'],
        );
        $paginate = array(
            config('paginate.list_rows'),
            false,
            'query' => ['exam_plan_id' => $where['ee.exam_plan_id']],
        );
        $ExamGrade = $this->BaseJoinSelectPage($paginate, 'ee', $join, $where, [$field]);
        return $ExamGrade;
    }

    /**
     * 根据考点获取考生
     */
    public function getAllForOrganize($map)
    {
        return $this->where('organize_id', '=', $map['id'])->select();
    }

    /**
     * 根据工种级别获取考生
     */
    public function getExaminess($where)
    {
        $map['work_id'] = $where['work_id'];
        $map['work_level_subject_level'] = $where['level'];
        return $this->BaseSelect($map, '', 'work_direction_id asc');
    }

    /**use
     * @param $map
     * @param string $order
     * @param string $group
     * @return false|\PDOStatement|string|\think\Collection
     * 获取全部考生
     */
    public function getOrganizeAll($map, $order = '', $group = '')
    {
        $this->BaseSelect($map, '', $order, '', $group);
    }

    public function getOrganizeAllPage($map, $order = '', $group = '')
    {
        $join = array(['__WORK_LEVEL_SUBJECT__', '(WLB.work_id=work_level_subject.work_id and WLB.work_level_subject_level=work_level_subject.`level`)']);
        $field = array('work_level_subject.work_id', 'work_level_subject.`level`', 'work_level_subject.subject_id');
        return $this->BaseJoinSelectPage('', 'WLB', $join, $map, $field, $order, $group);
    }

    public function getOrganize($map)
    {
        return $this->BaseFind($map);
    }

    public function getWork($organize, $plan,$field='')
    {
        $where['exam_plan_id'] = $plan['id'];
        $where['organize_id'] = $organize['id'];
        return $this->field('work_id')->where($where)->group('work_id')->select();
    }

    public function getLevel($organize, $plan, $work)
    {
        $where['exam_plan_id'] = $plan['id'];
        $where['organize_id'] = $organize['id'];
        $where['work_id'] = $work;
        return $this->field('work_level_subject_level')->where($where)->group('work_level_subject_level')->select();
    }

    public function getCardInfo($map)
    {
        $where['exam_enroll.exam_plan_id'] = $map['exam_plan_id'];
        $where['organize_id'] = $map['organize_id'];
        return $this->field('exam_enroll.user_login_id,exam_enroll.organize_id,exam_plan.year,exam_plan.batch_num,
                     exam_enroll.exam_plan_id,organize.code,organize.address_code,exam_enroll.work_id,exam_enroll.work_level_subject_level,
                     exam_enroll.id')
            ->join('__EXAM_PLAN__', 'exam_plan.id=exam_enroll.exam_plan_id', 'left')
            ->join('__ORGANIZE__', 'organize.id=exam_enroll.organize_id', 'left')
            ->where($where)
            ->select();
    }

    /**
     * 获取这个登录者有啥角色
     * @param $uid
     * @return false|\PDOStatement|string|\think\Collection
     * @user 李海江 2018/11/6~9:51 PM
     */
    public function getUserRole($uid)
    {

        $map = ['user_login_id' => $uid, 'status' => ['egt', config('ExamEnrollStatus.checkpass')]];
        $list = $this->BaseSelect($map, ['type'], '', '', 'type');
        return $list;
    }

    /**
     * [getPlanInfo 获取当前计划的基本信息以及报名表中此计划可以提交审核的信息]
     * @param  [type] $arrWhere [description]
     * @return [type]           [description]
     */
    public function getPlanInfo($arrWhere)
    {
        return $this->where($arrWhere)->field("count(id) as count,id")->select();
    }

    /**
     * @param $where
     * @return int|string
     * 统计查询
     */
    public function getCount($where)
    {
        return $this->BaseSelectCount($where);
    }

    /**
     * 报考条件限制
     * @param array $webData
     * @param array $mapenroll
     * @return int|string
     * @user xuweiqi
     */
    public function conditions($webData = [], $mapenroll = [])
    {
        $map = [
            'exam_plan_id' => $webData['exam_plan_id'],
            'work_id' => $webData['work_id'],
//              'work_direction_id' => $webData['work_direction_id'],
            'work_level_subject_level' => $webData['work_level_subject_level'],
            'user_login_id' => session('user')['id'],
        ];
//          $mapOriginal['type'] = session('user')['id_type'];
//          $mapOriginal['id_no'] = session('user')['id_card'];
//          $mapOriginal['current_level'] = $webData['current_level'];
//          $mapOriginal['work'] = $webData['work'];
//          $mapOriginal['certificate_no'] = $webData['certificate_no'];
        //测试
        $isRepetData = $this->findExamEnroll($map);
        if (!empty($isRepetData)) {
            return '您已报过此次工种、方向、级别,请报其他信息!';
        };
        //查询用户已报考数据
        $countRepet = $this->BaseSelect($mapenroll);
        if (count($countRepet) > 2) {
            return '您此次报考的鉴定计划已经超过3次,不能再报哦!';
        };
    }

    public function seatSubjec($map, $order, $group='')
    {
        $join = array(['__WORK_LEVEL_SUBJECT__', '(work_level_subject.work_id=exam_enroll.work_id)']);
        $field = array('work_level_subject.work_id', 'work_level_subject.`level`', 'work_level_subject.subject_id');
        $where['exam_enroll.status'] = array(array('eq', '50'), array('eq', '51'), 'or');
        $where['exam_enroll.type'] = array('neq','1');
        $where_or['exam_enroll.status'] = array('eq', '20');
        $where_or['exam_enroll.type'] = array('eq','1');
        return $this->field($field)->join($join)->where($map)->where(function ($query) use ($where) {
            $query->where($where);
        })->whereor(function ($query) use ($where_or) {
            $query->where($where_or);
        })->order($order)->group($group)->select();
    }

    /**
     * @param $map
     * @param string $order
     * @param string $group
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     * 获取鉴定计划下有资格参加考试的人，考生需要缴费，考评员不需要缴费
     */
    public function getQualifications($map, $field = '', $order = '', $group = '')
    {
        $where['status'] = array(array('eq', '50'), array('eq', '51'), 'or');
        $where['type'] = array('NEQ', '1');
        $where_or['status'] = array('eq', '20');
        $where_or['type'] = array('eq', '1');
        return $this->field($field)->where($map)->where(function ($query) use ($where) {
            $query->where($where);
        })->whereor(function ($query) use ($where_or) {
            $query->where($where_or);
        })->order($order)->group($group)->select();
    }

    public function getworkForEnroll($where,$field='')
    {
        return $this->field($field)->join('__WORK__','work.id=exam_enroll.work_id','left')->where($where)->find();
    }

    /**
     * @param $where
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     * 接口获取考生信息
     */
    public function getUserCard($where)
    {
        return $this
                    ->field('exam_enroll.id,grade.id as grade_id,exam_enroll.work_id,exam_enroll.work_direction_id,exam_enroll.work_level_subject_level,user_login.id_card,userinfo.avatar,grade.watch_score,exam_card.card,exam_enroll.user_login_id,exam_enroll.exam_plan_id,exam_enroll.type,userinfo.username')
                    ->join('__EXAM_CARD__','exam_card.enroll_id=exam_enroll.id and exam_card.exam_plan_id=exam_enroll.exam_plan_id','left')
                    ->join('__USER_LOGIN__','user_login.id=exam_enroll.user_login_id','left')
                    ->join('__USERINFO__','userinfo.user_login_id=exam_enroll.user_login_id','left')
                    ->join('__GRADE__','exam_enroll.user_login_id=grade.user_login_id and exam_enroll.exam_plan_id=grade.exam_plan_id and exam_enroll.work_id=grade.work_id and exam_enroll.work_direction_id=grade.work_direction_id and exam_enroll.work_level_subject_level=grade.level','left')
                    ->where($where)
                    ->find();
    }

    /**
     * @param $where
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     * 获取分数
     */
    public function getGrade($where)
    {
        return $this->field('grade.id,grade.watch_score,exam_enroll.user_login_id,exam_enroll.exam_plan_id,exam_enroll.work_id,exam_enroll.work_direction_id,exam_enroll.work_level_subject_level')
                     ->where($where)
                     ->join('__GRADE__','exam_enroll.user_login_id=grade.user_login_id and exam_enroll.exam_plan_id=grade.exam_plan_id and exam_enroll.work_id=grade.work_id and exam_enroll.work_direction_id=grade.work_direction_id and exam_enroll.work_level_subject_level=grade.level','left')
                     ->find();
    }

}