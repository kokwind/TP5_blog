<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use think\Request;          //获取当前请求信息
use think\Validate;     //使用tp5的验证器
use think\Session;
use app\common\model\Article as AdminArticle;
use app\common\model\Comment as AdminComment;
use app\admin\model\Link as AdminLink;

class Recycle extends Base
{
    //管理回收站
    
    //默认显示文章回收站
    public function index()
    {
        return "回收站管理";
    }

    public function article()
    {
        //显示已标记删除的文章
        $articleModel = new AdminArticle;
        $recycleArticle = $articleModel->showRecycleArticle();
        $this->assign('recycleArticle',$recycleArticle);
        
        return $this->fetch('Recycle/article');
    }

    public function comment()
    {
        // $recycleModel = new AdminRecycle;
        $commentModel = new AdminComment;
        $recycleComment = $commentModel->showRecycleComment();
        $this->assign('recycleComment',$recycleComment);
        
        return $this->fetch('Recycle/comment');
    }

    public function link()
    {
        // $recycleModel = new AdminRecycle;
        $linkModel = new AdminLink;
        $recycleLink = $linkModel->showRecycleLink();
        $this->assign('recycleLink',$recycleLink);

        return $this->fetch('Recycle/link');
    }


}