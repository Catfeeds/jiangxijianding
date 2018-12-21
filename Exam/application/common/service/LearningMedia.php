<?php
namespace app\common\service;
use app\common\model\LearningMedia as MLearningMedia;

class LearningMedia extends MLearningMedia
{
    public function select($where='')
    {
        return $this
            ->where($where)
            ->order('id', 'desc')
            ->paginate(10);
    }

    /**
     * @param $page
     * @param $param
     * @param bool $field
     * @param string $order
     * @return mixed|string
     * @user 李海江 2018/11/16~15:19
     */
    public function appGetAll($page, $param, $field = true, $order = 'id desc')
    {
        $data = $this->BaseSelectPage($page, $param, $field, $order)->each(function ($item) {
            $item->checked = false;
        });
        $data = json_decode(json_encode($data,true),true);
        return $data['data'];
    }
}