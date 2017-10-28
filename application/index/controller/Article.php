<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Article as IndexArticle;
use think\Request;          //获取当前请求信息


class Article extends Controller
{
    public function index()
    {
        //得到要显示的文章aid
        $aid = input('aid');
        if(!empty($aid)){
            //文章 aid 不为空
            $archiveModel = new IndexArticle;
            $data = $archiveModel->articleDetail($aid);
            if(empty($data)){
                //文章内容为空
                return $this->error('文章内容为空');
            }else{
                $this->assign('article',$data['article']);
                $this->assign('tags',$data['tags']);
                $this->assign('category',$data['category']);
                
                return $this->fetch('Article/index');
            }
        }else{
            return $this->error('文章 aid 不存在');
        }
        

    }

    
}