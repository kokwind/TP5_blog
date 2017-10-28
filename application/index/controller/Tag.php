<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Tag as IndexTag;
use think\Request;          //获取当前请求信息


class Tag extends Controller
{
    public function index()
    {
        $tagModel = new IndexTag;
        $data = $tagModel->getAllTag();
        if($data['tagTotal']){
            $this->assign('articleList',$data['articleList']);
            //$this->assign('tagAll',$data['tagAll']);
            //$this->assign('tagNum',$data['tagNum']);
            $this->assign('tagTotal',$data['tagTotal']);
            
            return $this->fetch('Tag/index');
        }else{
            return $this->error('还没有标签');
        }      
    }

    
}
