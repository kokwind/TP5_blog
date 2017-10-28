<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Archive as AdminArchive;
use think\Request;          //获取当前请求信息


class Archive extends Controller
{
    public function index()
    {
        $archiveModel = new AdminArchive;
        $data = $archiveModel->getAllArchive();
        if($data['yearNum']){
            $this->assign('articleList',$data['articleList']);
            $this->assign('yearNum',$data['yearNum']);
            
            return $this->fetch('Archive/index');
        }else{
            return $this->error('无归档日期');
        }  
        
    }

    
}
