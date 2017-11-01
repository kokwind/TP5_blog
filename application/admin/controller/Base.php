<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;          //获取当前请求信息
use think\Session;

class Base extends Controller
{
    
    /**
     * 定义控制器初始化方法，判断是否登录访问
     * @param strind $this->isLogin  判断用户是否登录
     * @param strind Request::instance()->action  判断访问的方法是否在定义的需要登录的方法里面
     * @param strind $this->checkLogin[0]  值为 '*' 即当前控制器的所有方法都需要登录才能访问
     */
    public function _initialize()
    {
        if( !$this->isLogin() )
        {
            return $this->error('请先登录系统！','admin/Login/login');
        }
    }

    public function isLogin()
    {
        return session('?name');
    }
}