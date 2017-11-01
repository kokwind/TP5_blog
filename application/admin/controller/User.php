<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;

class User extends Controller
{
    public function index(){
        return $this->fetch();
    }

}