<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/19
 * Time: 9:47
 */
namespace app\cms\controller;


use app\common\controller\AdminBase;
use app\common\model\CmsPresentation;

use app\common\service\CmsAppAbout;
use app\common\service\CmsGuide;
use think\Controller;
use think\Request;

class CentreController extends AdminBase
{
    protected  $SGuide;
    protected $SPhone;
    protected $SPresentation;
    protected $SAbout;

    /**
     * 构造函数
     * CentreController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SGuide = new CmsGuide();
         $this->SAbout = new CmsAppAbout();
        $this->SPresentation = new CmsPresentation();

    }
    public function index()
    {
      $data = $this->SAbout->BaseSelectPage([8,true,'query'=>request()->param()],['pid'=>1],[],'id desc');

        return view('',['data'=>$data]);
    }

    /**
     * 中心简介
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function presentation()
    {
        $data = $this->SPresentation->find();

        return view('',['data'=>$data]);
    }

    /**
     * 联系方式展示
     * @return \think\response\View
     */
    public function phone(Request $request)
    {
        $data = $this->SPresentation->where('type',4)->find();

        return view('',['data'=>$data]);
    }


   public function about()
   {
       $data = $this->SAbout->BaseFind(['pid'=>0,'type'=>1]);

       return view('',['data'=>$data]);
   }
   public function add(Request $request)
   {
       return view('');
   }
   public function update(Request $request)
   {
       $id['id'] = $request->param('id');
       $info = $this->SAbout->BaseFind($id);

       return view('about',['info'=>$info->getData()]);
   }
   public function xieyi()
   {
       $data = $this->SAbout->BaseFind(['pid'=>0,'type'=>1]);
       return view('',['data'=>$data]);
   }
}