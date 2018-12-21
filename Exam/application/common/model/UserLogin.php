<?php

namespace app\common\model;


/**
 * Class UserLogin
 * @package app\common\model
 */
class UserLogin extends BaseModel
{

    public $id_type;
    public $education;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $id_type = config('EnrollStatusText.id_type');
        $education = config('EnrollStatusText.education');
        if (count($data) > 0) {
            if (key_exists('id_type', $data)) {
                $this->id_type = $id_type[$data['id_type']];
            }
            if (key_exists('education', $data)) {
                $this->education = $education[$data['education']];
            }

        }
    }


    /**
     * @param $status
     * @return string
     * @user 李海江 2018/10/25~1:55 PM
     */
    public function getStatusAttr($status)
    {
        switch ($status) {
            case -1:
                return '冻结';
                break;
            case 1:
                return '正常';
                break;
            default:
                return '未知';
                break;
        }
    }


    /**
     * 模型关联 用户 - 用户信息
     * @return \think\model\relation\HasOne
     * @user 李海江 2018/10/25~12:06 PM
     */
    public function info()
    {
        return $this->hasOne('Userinfo', 'user_login_id', 'id');
    }


    /**
     * 模型关联 用户 - 证书
     * @return \think\model\relation\HasMany
     * @user 李海江 2018/11/15~3:04 PM
     */
    public function certificate()
    {
        return $this->hasMany('Certificate', 'user_login_id', 'id');
    }

    /**
     * 模型关联 用户 - 成绩
     * @return \think\model\relation\HasMany
     * @user 李海江 2018/11/15~5:42 PM
     */
    public function grade()
    {
        return $this->hasMany('Grade', 'user_login_id', 'id');
    }

}

