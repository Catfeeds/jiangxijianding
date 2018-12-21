<?php
namespace app\common\service;

use app\common\model\Invoice as MInvoice;
class Invoice extends MInvoice
{

    /**
     * @param $map
     * @return array|false|\PDOStatement|string|\think\Model
     * @$user xuwieqi
     */
    public function selInvoiceData($map = []){
         return $this->BaseFind($map);
    }

    /**
     * @param $data
     * @return mixed|string
     * @$user xuwieqi
     */
    public function saveInvoiceData($data){
        return $this->BaseSave($data);
    }

    /**
     * @param $data
     * @param $map
     * @return false|int|string
     * @$user xuwieqi
     */
    public function updateInvoiceData($data,$map){
       return  $this->BaseUpdate($data, $map);
    }



}
