<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/10/23
 * Time: 15:01
 */

namespace app\common\service;
use app\common\model\Thesis as MThesis;

class Thesis extends MThesis
{

    /**
     * @param array $map
     * @param array $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @user xuweiqi
     */
    public function findThesisData($map=[]){
        return  $this->BaseFind($map);
    }

    /** 添加论文数据和修改报名论文状态
     * @param $thesisData
     * @param $enrollData
     * @param $map
     * @return bool|string
     * @user xuweiqi
     */
    public function addThesis( $thesisData, $enrollData, $map ,$arrThesis)
    {
        // 启动事务
        $this->startTrans();
        try {

            $delThesis = $this->BaseDelete($arrThesis);
            $thesisRes = $this->BaseSave($thesisData,true);
            if ( !$thesisRes )
            {
                throw new \Exception('添加论文失败');
            }
            $enrollres =model('ExamEnroll')->BaseUpdate($enrollData,$map);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $e->getMessage();
        }

    }

}