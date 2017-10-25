<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Login as AdminLogin;

class Login extends Controller
{
    //登陆页面
    public function login()
    {
        
        
        return $this->fetch();
    }

    //退出登陆
    public function logout()
    {

    }


}