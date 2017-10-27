<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Comment as AdminComment;
use think\Request;          //获取当前请求信息

class Comment extends Controller
{
    //实现文章评论的相关操作
    public function index()
    {
        $commentModel = new AdminComment;
        $commentList = $commentModel->getPageDate();
        $this->assign('commentList',$commentList);
        
        return $this->fetch('Comment/index');
    }
    
    //通过或取消审核
    public function changeStatus()
    {
        $commentModel = new AdminComment;
        $change = $commentModel->changeCommentStatus();
        if($change){
            return $this->success('评论状态修改成功','Comment/index');
        }else{
            return $this->error('评论状态修改失败');
        }
    }

    //删除评论
    public function delete()
    {
        $commentModel = new AdminComment;
        $del = $commentModel->deleteComment();
        if($del){
            return $this->success('评论状态删除成功','Comment/index');
        }else{
            return $this->error('评论状态删除失败,评论cmtid不存在');
        }
    }

    //管理回收站
    public function recycle()
    {
        //根据recycle传的状态判断： 0 恢复文章； 1 彻底删除文章
        $request = Request::instance();
        $requestArr = $request->param();    //只有一个文章aid 的数组
        $commentModel = new AdminComment;
        $resRecycle = $commentModel->recycleComment($requestArr);
        if($resRecycle){
            return $this->success('回收站操作成功','Recycle/comment');
        }else{
            return $this->error('回收站操作失败','Recycle/comment');
        }
    }

}

