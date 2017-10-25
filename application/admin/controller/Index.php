<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Index as AdminIndex;

class Index extends Controller
{
    //进入admin页面
    public function Index()
    {
        
        return $this->fetch();
    }

    //后台页面头部
    public function Top()
    {
        
        return $this->fetch();
    }

    //左侧导航menu
    public function Menu()
    {
        return $this->fetch();
    }

    //右侧展示界面
    public function Desk()
    {
        $index_model = new AdminIndex;
        $data = $index_model->getDeskData();
        
        //给模板传值
        $this->assign('all_article',$data['all_article']);
        $this->assign('delete_article',$data['delete_article']);
        $this->assign('hide_article',$data['hide_article']);
        $this->assign('all_comment',$data['all_comment']);
        //渲染模板
        return $this->fetch();
    }

    //底部bottom
    public function Bottom()
    {
        return $this->fetch();
    }
}