<?php
namespace app\api\controller;
use app\common\controller\BaseApi;
use app\common\service\Thesis;
use think\Request;

class  ThesisController extends BaseApi{

    protected $SThesis;
    public function __construct()
    {
        $this->SThesis = new Thesis();
    }

    public function add()
  {


  }

  public function delete()
  {

  }

  public function update()

  {

  }

    //上传审核资料文件
    public function  yourUrl(Request $request)
    {
        $file = request()->file('file'); // 获取上传的文件
        $exam_enroll_id=$request->post();
        $examEnrollId = $exam_enroll_id['exam_enroll_id'];
        $enrollMap['id'] = $examEnrollId;
        $enrollThesisStatus['thesis_state'] = 2;
        if($file==null){
            return array('code'=>-1,'msg'=>'未上传文件');
        }
        // 获取文件后缀
        $temp = explode(".", $_FILES["file"]["name"]);
//        $extensionFirst=current($temp);    //获取文件名
        $extension = end($temp);
        // 判断文件是否合法
        if(!in_array($extension,array("docx","doc"))){
            return array('code'=>-1,'msg'=>'上传文件不支持,请上传Word文件!');
        }
        $info = $file->rule('uniqid')->move(ROOT_PATH.'public'.DS.'uploads/thesis/'); // 移动文件到指定目录 没有则创建
        $img = '/uploads/thesis/'.$info->getSaveName();
        $arrDate['exam_enroll_id'] = $examEnrollId;
        $arrDate['path']=$img;
        $arrThesis['exam_enroll_id'] = $examEnrollId;
        $req= $this->SThesis->addThesis($arrDate,$enrollThesisStatus,$enrollMap,$arrThesis);
        if($req == true){
            return layuiMsg(1,'上传成功',$req);
        }else{
            return layuiMsg(-1,'上传失败');
        }
    }

}