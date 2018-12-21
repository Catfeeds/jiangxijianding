<?php
namespace app\api\controller;
use app\common\controller\BaseApi;
use app\common\service\ExamCard;
use think\Request;

class ExamCardController extends BaseApi
{
    private $SExamCard;

    public function __construct()
    {
        $this->SExamCard = new ExamCard();
    }

    /**  准考证查询登录
     * @return array
     * @user xuweiqi
     */
    public function ticketloginAction()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            if(!captcha_check($arrData['yzm'])){
                return layuiMsg(-2,'验证码错误!');
            }
            if (($arrData['id_card'] &&$arrData['id_type'] && $arrData['username']) || ($arrData['id_card'] &&$arrData['id_type'] && $arrData['card']) || ($arrData['username'] && $arrData['card'])) {
                $map['id_card'] = $arrData['id_card']?$arrData['id_card']:'';
                $map['id_type'] = $arrData['id_type']?$arrData['id_type']:'';
                $map['username'] = $arrData['username']?$arrData['username']:'';
                $map['card'] = $arrData['card']?$arrData['card']:'';
                foreach( $map as $k=>$v){
                    if( !$v ){
                        unset( $map[$k] );
                    }
                }
                $data = $this->SExamCard->BaseSelect($map);
                foreach ($data as $v){
                    $site_id = $v->enrollinfo->site_id;
                    $v['id_type']=$v->id_type;
                    $v['level']=$v->level;
                    $v['ticket'] = getFileName($v['exam_plan_id'],$site_id,$v['enroll_id'],'card');
                }

                if (empty($data)) {
                    return layuiMsg(-1,"未查询到相关信息!");
                } else {
                    return layuiMsg(1,"查询成功",$data);
                }
            } else {
                return layuiMsg(-1, '输入以上两项内容才可查询');
            }
            return layuiMsg(-1,"请求失败");
        }
    }


}