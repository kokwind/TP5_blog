<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;          //获取当前请求信息
use app\admin\model\Login as AdminLogin;
use app\admin\model\User as AdminUser;
use think\Session;

class Login extends Controller
{
    //登陆页面
    public function login()
    {
        $request = Request::instance();
        $method = $request->method();

        if($method == 'POST'){
            //判断登陆信息
            $userIP = $request->ip();
            $data = $request->except(['request']);
            $data['ip'] = $userIP;
            $userModel = new AdminUser;
            $resLogin = $userModel->checkUser($data);
            if($resLogin){
                //登陆成功
                Session::set('name',$data['username']);
                return $this->redirect('Index/index');      //重定向
            }else{
                return $this->error('用户名或者密码错误！','Login/login');
            }
        }else{
            return $this->fetch('Login/login');
        }
        
    }

    //退出登陆
    public function logout()
    {
        Session::delete('name');
        return $this->fetch('/Login/login');
    }


}