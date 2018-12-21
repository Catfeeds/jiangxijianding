<?php
namespace app\api\controller;
use app\common\controller\BaseApi;
use app\common\service\Grade;
use think\Request;

class GradeController extends BaseApi
{
    private $SGrade;

    public function __construct()
    {
        $this->SGrade = new Grade();
    }

    /**  成绩查询登录
     * @return array
     * @user xuweiqi
     */
    public function gradeloginAction()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            if(!captcha_check($arrData['yzm'])){
                return layuiMsg(-2,'验证码错误!');
            }

            if (($arrData['id_card'] &&$arrData['id_type'] && $arrData['username']) || ($arrData['id_card'] &&$arrData['id_type'] && $arrData['TicketNum']) || ($arrData['username'] && $arrData['TicketNum'])) {
                $map['id_card'] = $arrData['id_card']?$arrData['id_card']:'';
                $map['id_type'] = $arrData['id_type']?$arrData['id_type']:'';
                $map['username'] = $arrData['username']?$arrData['username']:'';
                $map['TicketNum'] = $arrData['TicketNum']?$arrData['TicketNum']:'';
                foreach( $map as $k=>$v){
                    if( !$v ){
                        unset( $map[$k] );
                    }
                }
                $data = $this->SGrade->BaseSelect($map);
                foreach ($data as $v){
                    $v['directionname']=$v->directioninfo->name;
                    $v['level']=$v->level;
                    $v['theory_score_result']=$v->theory_score_result;
                    $v['watch_score_result']=$v->watch_score_result;
                    $v['synthesize_score_result']=$v->synthesize_score_result;
                }
//                dump($data);die;
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