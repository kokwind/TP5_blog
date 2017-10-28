<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Index as AdminIndex;
use think\Request;          //获取当前请求信息


class Index extends Controller
{
    public function index()
    {
        $indexModel = new AdminIndex;
        $data = $indexModel->getAllArticle();
            
        $this->assign('articleAll',$data['articleAll']);
        $this->assign('tagAll',$data['tagAll']);
        $this->assign('categoryAll',$data['categoryAll']);
        return $this->fetch('Index/index');

    }

    
}
