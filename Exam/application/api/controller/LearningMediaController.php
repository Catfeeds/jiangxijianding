<?php

namespace app\api\controller;
use app\common\service\LearningMedia;
use think\Request;
use app\common\controller\BaseApi;

class LearningMediaController extends BaseApi
{
    private $media;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->media = new LearningMedia();
    }

    public function add()
    {
        $file = request()->file('file');

        if (empty($file)) {
            $res['code'] = 2;
            $res['msg'] = '未正确上传文件';
            return $res;
        }

        $info = $file->validate([
            'size'=>8388600000,
            'ext' => 'mp4,docx,pdf,pptx,swf'
        ])->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'learning' . DS .'videos');

        if ($info) {

            $fileSuffix = $info->getExtension();
            switch ($fileSuffix) {
                case 'mp4':
                    $fileType = '1';//'视频格式';
                    $fileId = '1';
                    break;
                case 'docx':
                    $fileType = '2';//'docx文档格式';
                    $fileId = '2';
                    break;
                case 'pdf':
                    $fileType = '3';//'pdf文件格式';
                    $fileId = '3';
                    break;
                case 'pptx':
                    $fileType = '4';//'ppt文件格式';
                    $fileId = '4';
                    break;
                case 'swf':
                    $fileType = '5';//'swf flash动画文件';
                    $fileId = '5';
                    break;
            }

            $fileName = $info->getInfo()['name'];
            $fileSize = round($info->getSize() / 1024 * 100) / 100 . ' KB';
            $fileAddressLocal = '/uploads/learning/videos/' . $info->getSaveName();


            $where = [
                'file_name'  =>  $fileName,
                'file_url' => $fileAddressLocal,
                'file_size' =>  $fileSize,
                'file_address' => $fileAddressLocal,
                'file_type' =>  $fileType,
                'file_id' => $fileId,
                'create_time' => time()
            ];

            $result = $this->media->BaseSave($where);

            if ($result) {
                $res['code'] = 0;
                $res['msg'] = '上传成功';
            } else {
                $res['code'] = 0;
                $res['msg'] = '上传失败';
            }
            return $res;
        } else {
            $res['code'] = 1;
            $res['msg'] = $file->getError();
            return $res;
        }


    }

    public function delete()
    {
        if (Request::instance()->isPost())
        {
            $arrData = input('post.');
            if (!$arrData && empty($arrData['id'])){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }

            $objDel = $this->media->destroy($arrData['id']);

            if ($objDel){
                $arrMsg['status'] = 1;
                $arrMsg['msg'] = "删除成功";
                return $arrMsg;
            }else{
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "删除失败";
                return $arrMsg;
            }
        }
    }
}