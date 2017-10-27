<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Recycle as AdminRecycle;
use think\Request;          //获取当前请求信息
//use think\Db;       //使用数据库
use think\Validate;     //使用tp5的验证器

class Recycle extends Controller
{
    //管理回收站
    
    //默认显示文章回收站
    public function index()
    {
        return "回收站管理";
    }

    public function article()
    {
        $recycleModel = new AdminRecycle;
        $recycleArticle = $recycleModel->articleRecycle();
        $this->assign('recycleArticle',$recycleArticle);
        
        return $this->fetch('Recycle/article');
    }

    public function comment()
    {
        $recycleModel = new AdminRecycle;
        $recycleComment = $recycleModel->commentRecycle();
        $this->assign('recycleComment',$recycleComment);
        
        return $this->fetch('Recycle/comment');
    }

    public function link()
    {
        $recycleModel = new AdminRecycle;
        $recycleLink = $recycleModel->linkRecycle();
        $this->assign('recycleLink',$recycleLink);

        return $this->fetch('Recycle/link');
    }


}