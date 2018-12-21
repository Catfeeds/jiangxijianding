<?php
namespace app\index\controller;

use app\index\model\User as UserModel;
use think\Request;//引入request
use think\Controller;//引入控制器

class UserController extends Controller
{




    /*
     * 文件上传
     * */
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
//        echo "<pre>";
//        print_r($file);die;
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }


//    /*
////     * 图片裁剪
////     * $img  原图路径
////     * $newimg 新图路径
////     * $width 宽度
////     * $height 高度
////     * @@事例
////     * $img = './uploads/20180725/45e0d688b149c5074d8f2b62a8e0e2e4.jpg';
////     * $newimg = "new_".$img;
////     * */
////    public function saveImg($img='',$newimg='',$width=300,$height=300){
////        if(empty($img)){
////            return false;
////        }
////        if (empty($newimg)){
////            $newimg = "new_".$img;
////        }
////        if ($width<1){
////            return false;
////        }
////        if ($height<1){
////            return false;
////        }
////        $image = \think\Image::open($img);
////        //将图片裁剪为300x300并保存为crop.png
////        $image->crop($width, $height)->save($newimg);
////    }

    public function saveImg(){
        return view('saveimg');
    }

    public function imgShow(){
//        print_r(1);die;
//        $img = "20180725/45e0d688b149c5074d8f2b62a8e0e2e4.jpg";
        $img = "img/bz1.png";
        $this->assign("img",$img);
        return view('imgshow');
    }

    public function downloadPdf(){
        return view('indexPdf');
    }




}