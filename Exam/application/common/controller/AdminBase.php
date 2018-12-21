<?php
/**
 * Created by PhpStorm.
 * User: boao
 * Date: 2018/8/29
 * Time: 13:43
 */

namespace app\common\controller;

use app\common\service\Admin;
use app\common\service\Menu;
use app\common\service\OaSend;
use Auth\Auth;
use think\Controller;
use think\Request;
use think\Response;
use think\Session;

/**
 * Class AdminBase
 * @package app\common\controller
 */
class AdminBase extends Controller
{
    private $flag = true;
    /**
     * @var Admin
     */
    private $SAdmin;
    /**
     * @var Menu
     */
    private $SMenu;
    private $Navigation;
    private $SOAsend;
    /**
     * AdminBase constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if (!Session::get('adminuser')) {
            $this->error('您还未登录,请先登录', '/cms/index/admin/');
        } else {
            $this->SMenu = new Menu();
            $this->SAdmin = new Admin();
            $this->SOAsend = new OaSend();
            if (session('adminuser.username') != config('adminMessage.name')) {
                $result = self::checkAuth();
                if (!$result) $this->error('您没有权限访问', '/admin/');
                $this->flag = false;
            }
            $menuData = $this->getMenu($this->flag);
            $this->assign('menuData', $menuData);
            $count = $this->SOAsend->BaseSelectCount(['to_id'=>Session::get('adminuser.id'),'status'=>1]);
            $this->assign('count',$count);
        }
        $this->Navigation = $this->navigation();
    }


    /**
     * 检查当前登录用户的权限
     * @return bool
     * @user 李海江 2018/10/17~11:02 AM
     */
    static public function checkAuth()
    {
        $auth = new Auth();
        $request = Request::instance();
        $m = $request->module();
        $c = $request->controller();
        $a = $request->action();
        $rule_name = $m . '/' . $c . '/' . $a;
        return $auth->check($rule_name, session('adminuser.id'));
    }


    /**
     * 获取纵向菜单
     * @return array
     * @user 李海江 2018/10/17~10:37 AM
     */
    public function getMenu($isAdmin = false)
    {
        if ($isAdmin) {
            $myMenu = $this->SMenu->getAllMenuTree(['hidden' => 0]);
        } else {
            $rules = $this->SAdmin->getMyRules();
            $myMenu = $this->SMenu->getAllCheckMenuTree($rules, ['hidden' => 0]);
        }
        return $myMenu;
    }

    /**
     * @param string $template
     * @param array $vars
     * @param array $replace
     * @param array $config
     * @return mixed|string
     * @throws \think\Exception
     * @user 李海江 2018/10/17~1:44 PM
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        if (array_key_exists("layout", $vars)) {
            return $this->view->fetch($template, $vars, $replace, $config);
        } else {
            $this->assign("childUrl", $template);
            return $this->view->fetch($template, $vars, $replace, $config);
        }
    }

    /**
     * 设置导航
     */
    public function navigation()
    {
        $request = Request::instance();
        $navicateNew= array();
        $menu = $this->SMenu->showMenuForName(array('url' => $request->pathinfo()));
        $navicate = Session::get('Navicate');
        $data[$menu['id']]['url'] = $request->url();
        $data[$menu['id']]['title'] = $menu['title'];
        $data[$menu['id']]['id'] = $menu['id'];
        if($navicate)
        {
            foreach ($navicate as $k => $v)
            {
                $navicateNew[$k] = $v;
                if($k ==  $menu['id'])
                {
                    break;
                }
            }
        }

        if($menu)
        {
            Session::set('Navicate','0');
            if($navicateNew == 0 || !$navicateNew)
            {
                Session::set('Navicate',$data);
            }else{
                if($menu['parent_id'] == 0 || $menu['url'] == '#' || $menu['status'] ==1)
                {
                    Session::set('Navicate',$data);
                }else{
                    $navicateNew[$menu['id']]['url'] = $request->url();
                    $navicateNew[$menu['id']]['title'] = $menu['title'];
                    $navicateNew[$menu['id']]['id'] = $menu['id'];
                    Session::set('Navicate',$navicateNew);
                }
            }
        }
    }

    /**
     * @return mixed
     * 取导航
     */
    public function navigationInfo()
    {
        return Session::get('Navicate');
    }

    /**
     * @param string $template
     * @param array $vars
     * @param array $replace
     * @param int $code
     * @return Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     * 在view方法中增加导航数据返回
     */
    public function thisView($template = '', $vars = [], $replace = [], $code = 200)
    {
        $vars['Navigation'] = $this->navigationInfo();
        return Response::create($template, 'view', $code)->replace($replace)->assign($vars);
    }
}