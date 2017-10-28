<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Category as AdminCategory;
use think\Request;          //获取当前请求信息


class Category extends Controller
{
    public function index()
    {
        $categoryModel = new AdminCategory;
        $data = $categoryModel->getAllCategory();
            
        $this->assign('articleAll',$data['articleAll']);
        $this->assign('tagAll',$data['tagAll']);
        $this->assign('categoryAll',$data['categoryAll']);
        $this->assign('categoryNum',$data['categoryNum']);
        
        return $this->fetch('Category/index');

    }

    //根据cid显示特定的分类信息
    public function list()
    {
        $cid = input('cid');
        if(!empty($cid)){
            // 分类 cid 不为空，根据cid显示
            $categoryModel = new AdminCategory;
            $data = $categoryModel->getList($cid);
            if($data['articleAll']){
                $this->assign('articleAll',$data['articleAll']);
                $this->assign('tagAll',$data['tagAll']);
                $this->assign('categoryAll',$data['categoryAll']);
                $this->assign('categoryNum',$data['categoryNum']);
    
                return $this->fetch('Category/index');
            }else{
                return $this->error('此分类无文章');
            }
            
        }
    }
    
}
