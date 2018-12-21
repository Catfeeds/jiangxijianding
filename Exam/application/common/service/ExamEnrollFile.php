<?php
namespace app\common\service;
use app\common\model\ExamEnrollFile as MExamEnrollFile;
use think\Config;

class ExamEnrollFile extends MExamEnrollFile
{


    /** 添加审核资料
     * @param $arrDate
     * @return bool|string
     * @user xuweiqi
     */
    public function addFile($arrDate,$examEnroll,$noneEmpty)
    {
        $this->startTrans();
        try {
            if($noneEmpty == 1){
                $req = $this->BaseSaveAll($arrDate,['exam_enroll_id' =>$examEnroll]);
            }else{
                $req= $this->BaseSaveAll($arrDate);
            }
            if (!$req) {
                throw new \Exception('新建审核资料成功');
            }
            $examEnrollData['id'] = $req['exam_enroll_id'];
            $res = model('examEnroll')->BaseUpdate(['status'=>config('ExamEnrollStatus.uploadfile')],['id'=>$examEnroll]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }

    /**
     * [uploadFiles 机构批量上传]
     * @param  [type] $arrDate    [上传文件数据]
     * @param  [type] $examEnroll [报名状态修改值]
     * @param  [type] $noneEmpty  [报名状态修改条件]
     * @return [type]             [description]
     */
    public function uploadFiles($arrDate,$examEnroll,$noneEmpty,$userinfo)
    {
        $this->startTrans();
        try {
            $req = $this->BaseSaveAll($arrDate);
            if (!$req) {
                throw new \Exception('图片上传失败');
            }
            $res = model('ExamEnroll')->BaseUpdate($examEnroll,$noneEmpty);
            if(!$res)
            {
                throw new \Exception('报名状态修改失败');
            }
            if(!empty($userinfo)){
                foreach($userinfo as $k=>$v){
                  $result = model('Userinfo')->BaseUpdate(['avatar'=>$v['avatar']],['user_login_id'=>$v['user_login_id']]);
                }
            }
            if(!$res)
            {
                throw new \Exception('证件照添加失败');
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }
    }


    /**
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @user xuweiqi
     */
    public function findFileOne($map){
        return $this->BaseFind($map);
    }

}