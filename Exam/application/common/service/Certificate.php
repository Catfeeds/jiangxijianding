<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/23
 * Time: 15:01
 */

namespace app\common\service;
use app\common\model\Certificate as MCertificate;
use app\common\model\StbArea;
use app\common\model\ApplyCertDetail;
class Certificate extends MCertificate
{
    private $MCertificate;
    private $StbArea;
    private $SapplyCertDetail;
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->MCertificate = new MCertificate;
        $this->StbArea = new StbArea;
        $this->SapplyCertDetail = new ApplyCertDetail;
    }

    /**
     * @param $data
     * @param $map
     * @return false|int|string
     */
    public function updateCertData($data,$map){
        return $this->MCertificate->BaseUpdate($data,$map);
    }

    /**
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function findCertDataOne($map){
        return $this->MCertificate->BaseFind($map);
    }

    /**
     * @param array $map
     * @param array $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @user 朱颖 {2018/10/31}~{17:47}
     */
    public function getDetailById($map = [],$field = [])
    {
        return $this->MCertificate
            ->join("__USER_LOGIN__","certificate.id_no=user_login.id_card AND certificate.type=user_login.id_type")
            ->join("__EXAM_ENROLL__","certificate.exam_enroll_id=exam_enroll.id")
            ->join("__WORK__","exam_enroll.work_id=`work`.id")
            ->join("__WORK_TYPE__","`work`.work_type_id=work_type.id")
            ->join("__WORK_DIRECTION__","exam_enroll.work_direction_id=work_direction.id")
            ->where($map)
            ->field($field)
            ->find();
    }

    /**
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     * @user xuweiqi
     */
    public function selCertData($map){
        return $this->MCertificate->BaseSelect($map);
    }

    /**
     * [selectExamGrade 查询证书申领信息]
     * @return [type] [description]
     */
    public function selectExamGrade($map,$field)
    {
        
        $join = [
            ['__EXAM_ORDER_DETAIL__ ed','ed.enroll_id = c.exam_enroll_id','left']
        ];

        $paginate = array(
            config('paginate.list_rows'),
            false,
            ['query' => Request()->param()],
        );
        $ExamGrade = $this->MCertificate->BaseJoinSelectPage($paginate, 'c', $join, $map, [$field],'','ed.order_id');
        return $ExamGrade;


    }

    /** 查询未领取的证书 */
    public function selectNotCert($map){
        $field = "c.*,eo.pay_state,cw.cert_get_way,cw.is_take,eo.order_num";
        $join = [
            ['__CERT_WAY__ cw',"cw.certificate_id=c.id ",'left'],
            ['__EXAM_ORDER__ eo','eo.exam_plan_id=c.exam_plan_id','left'],
            ['__EXAM_ENROLL__ ee','ee.exam_plan_id = eo.exam_plan_id and ee.user_login_id=c.user_login_id'],
            ['__EXAM_ORDER_DETAIL__ ed','ed.order_id = eo.id and ed.enroll_id = ee.id']
        ];
        $ExamGrade = $this->MCertificate->BaseJoinSelect('c', $join, $map, [$field]);
        return $ExamGrade;
    }

    //生成证书编号 和 导入编号
    public function createCertNum($addData = [])
    {
        // 启动事务
        $this->MCertificate->startTrans();
        try {

        $arr = array_columns($addData,['no','type','exam_time','address_code']);
        //取出需要的值
        $address_code = array_column($addData,'address_code');
        //去重
        $address_code = array_unique($address_code);
        //切割为字符串
        $address_code = implode(",",$address_code);
        //拼接条件
        $str_level['code'] = ['in',$address_code];
        $arr_level = $this->StbArea->BaseSelect($str_level,['code','level']);
        foreach ($addData as $k=>$v)
        {
            foreach ($arr_level as $key=>$val)
            {
                if ($v['address_code'] == $val['code'])
                {
                    $arr_level[$key]['level'] = str_replace('1','0',$val['level']);
                    $arr_level[$key]['level'] = str_replace('2','1',$val['level']);
                    $arr_level[$key]['level'] = str_replace('3','2',$val['level']);
                    if ($v['address_code'] == $val['code'])
                    {
                        $addData[$k]['address_code'] = $val['level'];
//                        $addData[$k]['type'] = 5; //模拟数据
//                        $certificate_code[$k] = $v['exam_enroll_id']; //模拟数据
                    }
                }
            }
        }
        //当前已有的编号
        $professional_qualification = 1;
        //总共五位
        $sum_qualification = 5;
        //总共6位 专项能力 预备技师
        $sum_special_ability = 6;
        //考评员
        $sum_jury = 4;
        //当前长度
        $repair = strlen($professional_qualification);
        if ($repair > 5 || $repair < 1)
        {
            throw new \Exception('操作频繁');
        }
        $certificate_sum = 0;
        foreach ($addData as $k=>$v)
        {
            switch ($v['type'])
            {
                //职业资格鉴定   竞赛    技师&高级技师
                case config('ExamType.professional_qualification') :
                case config('ExamType.competition') :
                case config('ExamType.technician'):
                    $no[$k] = substr(date('Y',$v['exam_time']),-2).'14'.config('Address')[$arr[$k]['address_code']].$v['address_code'].$v['code'].$v['level'].str_pad((++$professional_qualification),$sum_qualification,"0",STR_PAD_LEFT);
                    break;
                //考评员
                case config('ExamType.jury'):
                    $no[$k] = substr(date('Y',$v['exam_time']),-2).'14'.config('Address')[$arr[$k]['address_code']].$v['level'].str_pad((++$professional_qualification),$sum_jury,"0",STR_PAD_LEFT);
                    break;
                //专项能力
                case config('ExamType.special_ability'):
                    $no[$k] = substr(date('Y',$v['exam_time']),-2).'14'.config('Address')[$arr[$k]['address_code']].str_pad((++$professional_qualification),$sum_special_ability,"0",STR_PAD_LEFT);
                    break;
                //预备技师
                case config('ExamType.prepare_technicians'):
                    $no[$k] = substr(date('Y',$v['exam_time']),-2).'140000009'.str_pad((++$professional_qualification),$sum_special_ability,"0",STR_PAD_LEFT);
                    break;
            }

            //需要导入 编号  专项能力 市或县
            if($v['type'] == config('ExamType.special_ability') && $v['exam_center_type'] == 2 ||$v['type'] == config('ExamType.special_ability') && $v['exam_center_type'] == 3)
            {
                $whereCertDetail[$k] = $v['center_id'];
                $certificate_sum++;
            }
        }
        $useCode = [];
        if ($certificate_sum > 0)
        {
            $whereCertDetail = implode(',',$whereCertDetail);
            $CertDetail = $this->SapplyCertDetail->BaseSelect(['center_id'=>['in',$whereCertDetail],'is_use'=>0],['certificate_code','center_id','id'],'',$certificate_sum);
            $CertDetail = collection($CertDetail)->toArray();
            //空白证书不够
            if (count($CertDetail) < $certificate_sum)
            {
                throw new \Exception('空白证书不够,请先申请再生成证书');
            }

            foreach ($addData as $k=>$v)
            {
                if (in_array($v['center_id'],array_column($CertDetail,'center_id')))
                {
                    $addData[$k]['series_number'] = $CertDetail[$k]['certificate_code'];
                    $useCode[$k] = $CertDetail[$k]['id'];
                }

            }
            //已使用的  空白证书id
            $useCode = implode(',',$useCode);
            //update `apply_cert_detail` set is_use=0 where id in(1,2,3,4,5);
            $strUpdate = $this->SapplyCertDetail->BaseUpdate(['is_use'=>1],['id'=>['in',$useCode]]);

            if (!$strUpdate)
            {
                throw new \Exception('生成失败');
            }
        }
        foreach ($addData as $k=>$v)
        {
            unset($addData[$k]['current_level']);
            unset($addData[$k]['center_id']);
            unset($addData[$k]['exam_time']);
            unset($addData[$k]['address_code']);
            unset($addData[$k]['code']);
            unset($addData[$k]['exam_center_type']);
        }
        $strAddCert = $this->MCertificate->BaseSaveAll($addData);
        if (!$strAddCert)
        {
            throw new \Exception('生成失败');
        }
//        print_r($strAddCert);
//        die;
            $this->MCertificate->commit();
            return layuiMsg(1,'生成成功');
        } catch (\Exception $e) {
            // 回滚事务
            $this->MCertificate->rollback();
            return layuiMsg(-1,$e->getMessage());
        }
    }

}