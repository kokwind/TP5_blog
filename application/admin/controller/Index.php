<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Index as AdminIndex;
use think\Session;

class Index extends Controller
{
    //进入admin页面
    public function Index()
    {
        $index_model = new AdminIndex;
        $data = $index_model->getDeskData();
        
        //给模板传值
        $this->assign('all_article',$data['all_article']);
        $this->assign('delete_article',$data['delete_article']);
        $this->assign('hide_article',$data['hide_article']);
        $this->assign('all_comment',$data['all_comment']);
        $this->assign('delete_comment',$data['delete_comment']);
        $this->assign('all_link',$data['all_link']);
        $this->assign('hide_link',$data['hide_link']);
        $this->assign('delete_link',$data['delete_link']);
        //渲染模板
        return $this->fetch("Index/index");
    }

}