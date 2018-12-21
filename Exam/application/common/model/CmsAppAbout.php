<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/14
 * Time: 10:28 AM
 */

namespace app\common\model;


class CmsAppAbout extends BaseModel
{
    /**
     * @param $status
     * @return string
     * @user wangzhong
     */
      public function getStatusAttr($status)
      {
          switch ($status) {
              case 1:
                  return '<span  style="color: #5FB878;">发布</span>';
                  break;
              default:
                  return '<span style="color:indianred">未发布</span>';
                  break;
          }
      }
         public function getTypeAttr($type)
      {
          switch ($type)
          {
              case 0:
                  return '常见问题';
                  break;
                  default:
                  return '用户协议';
                  break;
          }
      }

}