<?php
namespace app\api\controller;
use app\common\service\ExamEnrollFile;
use app\common\controller\BaseApi;
use app\index\controller\IndexController;
use app\common\service\ExamEnroll;
use app\common\service\UserLogin;
use think\Request;

class ExamEnrollFileController extends BaseApi{

    /**
     * @var ExamEnrollFile
     */
    private $SExamEnrollFile;
    private $SExamEnroll;
    private $SUserLogin;
    /**
     * ExamEnrollFileController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SExamEnrollFile = new ExamEnrollFile();
        $this->SExamEnroll     = new ExamEnroll();
        $this->SUserLogin      = new UserLogin();
    }

    public function add()
    {

    }

    /**删除上传审核文件
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        //删除上传审核文件信息
        $res= $this->SExamEnrollFile->BaseDelete(['id'=>$id]);
        if($res == true){
            return layuiMsg(1,"操作成功");
        }else{
            return layuiMsg(-1,"操作失败");
        }
    }

    public function update()

    {

    }

    //上传审核资料文件
    public function  yourUrlo(Request $request)
    {
        $exam_enroll_id = $request->post();
        $exam_enroll_id = $exam_enroll_id['data'];

        $result = $this->validate($exam_enroll_id,'ExamEnroll.uploadfile');
        if($result !== true){
            return layuiMsg(-1,$result);
        }
        $examEnroll = $exam_enroll_id['exam_enroll_id'];
        foreach ($exam_enroll_id as $k =>$v ){
            if($k == 'zheng'){
                $arrDate[$k]['exam_enroll_id']=$examEnroll;
                $arrDate[$k]['type']=1;
                $arrDate[$k]['path'] = $v;
            }elseif ($k == 'fan') {
                $arrDate[$k]['exam_enroll_id']=$examEnroll;
                $arrDate[$k]['type']=4;
                $arrDate[$k]['path'] = $v;
            }elseif ($k == 'xueli'){
                $arrDate[$k]['exam_enroll_id']=$examEnroll;
                $arrDate[$k]['type']=2;
                $arrDate[$k]['path'] = $v;
            }else{
                $arrDate[$k]['exam_enroll_id']=$examEnroll;
                $arrDate[$k]['type']=3;
                $arrDate[$k]['path'] = $v;
            }
        }
        unset($arrDate['exam_enroll_id']);
        unset($arrDate['file']);

//        $req= $this->SExamEnrollFile->BaseSaveAll($arrDate);
        $req = $this->SExamEnrollFile->addFile($arrDate,$examEnroll);
        if($req == true){
            return layuiMsg(1,'上传成功');
        }else{
            return layuiMsg(-1,'上传失败');
        }
    }

    //上传审核资料文件
    public function  yourUrl(Request $request)
    {
        $file = request()->file('file');
        $filename = $file -> getInfo()['name'];
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        $reubfo = array();  //定义一个返回的数组
        if($info){
            $reubfo['code']= 1;
            $reubfo['savename'] = "/uploads/auditdata/".$filename;
        }else{
            // 上传失败获取错误信息
            $reubfo['code']= 0;
            $reubfo['err'] = $file->getError();
        }
        return $reubfo;
    }


//文件上传代码--带缩率图
    public function uploads() {
        $params = $this->request->param();
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $file_path=ROOT_PATH . 'public/uploads/';
        $info = $file->move($file_path);
        $reubfo = array(); //定义一个返回的数组
        if ($info) {
            $reubfo['info'] = 1;
            $reubfo['savename'] = $info->getSaveName();
            $image = \think\Image::open(ROOT_PATH.'public/uploads'.DS.$reubfo['savename']);
            $width = $image->width();
            $height = $image->height();
            $path='sm_file/uploads/'.date('Ymd');
            if (!$this->checkPath($path)) {
                $reubfo['err'] = '生成缩略图失败';
            }
            $image->thumb(200,200,1)->save(ROOT_PATH.'public/sm_file/uploads'.DS.$reubfo['savename']);
        } else { // 上传失败获取错误信息
            $reubfo['info'] = 0;
            $reubfo['err'] = $file->getError();
        }
        return $reubfo;
    }


}