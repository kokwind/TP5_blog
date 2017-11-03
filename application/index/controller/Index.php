<?php
namespace app\index\controller;
use think\Controller;
use think\Request;          //获取当前请求信息
use app\common\model\Article as IndexArticle;
use app\index\model\Archive as IndexArchive;
use app\common\model\Category as IndexCategory;
use app\common\model\Tag as IndexTag;
use app\common\model\Comment as IndexComment;


class Index extends Controller
{

    public function index()
    {
        //获取首页文章的全部分页信息
        $indexModel = new IndexArticle;
        $articleList = $indexModel->getPageDate();
        //获取全部的分类信息
        $categoryModel = new IndexCategory;
        $categoryTotal = $categoryModel->getAllCategory();
        //获取全部标签信息
        $tagModel = new IndexTag;
        $tagAll = $tagModel->getArticleTag();
            
        $this->assign('articleAll',$articleList);
        $this->assign('tagAll',$tagAll);
        $this->assign('categoryAll',$categoryTotal);
        return $this->fetch('Index/index');

    }

    //文章详细显示
    public function article()
    {
        //得到要显示的文章aid
        $aid = input('aid');
        if(!empty($aid)){
            //文章 aid 不为空
            $articleModel = new IndexArticle;
            $article = $articleModel->articleDetail($aid); 
            //获取单篇文章的标签信息
            $tagModel = new IndexTag;
            $tags = $tagModel->getArticleTag($aid);
            //获取单篇文章的分类信息
            $categoryModel = new IndexCategory;
            $category = $categoryModel->getArticleCategory($aid);
            //获取单篇文章的全部评论信息
            $commentModel = new IndexComment;
            $commentAll = $commentModel->getArticleComments($aid);
            $totalComment = count($commentAll);
            $parentComment = [];
            $childComment = [];
            //分离评论信息
            for($i=0;$i<$totalComment;$i++){
                if($commentAll[$i]['pid'] == 0){
                    $parentComment[] = $commentAll[$i];
                }else{
                    $childComment[] = $commentAll[$i];
                }
            }

            if(empty($article)){
                //文章内容为空
                return $this->error('文章内容为空');
            }else{
                $this->assign('article',$article);
                $this->assign('tags',$tags);
                $this->assign('category',$category);
                $this->assign('parentComment',$parentComment);
                $this->assign('childComment',$childComment);
                $this->assign('totalComment',$totalComment);
                
                return $this->fetch('Article/index');
            }
        }else{
            return $this->error('文章 aid 不存在');
        }
    }

    //归档内容详细显示
    public function archive()
    {
        $archiveModel = new IndexArchive;
        $data = $archiveModel->getAllArchive();
        if($data['yearNum']){
            $this->assign('articleList',$data['articleList']);
            $this->assign('yearNum',$data['yearNum']);
            
            return $this->fetch('Archive/index');
        }else{
            return $this->error('无归档日期');
        }  
        
    }

    //用于分类内容详细显示
    public function category()
    {
        //获取全部分类信息
        $categoryModel = new IndexCategory;
        $categoryTotal = $categoryModel->getAllCategory();
        //获取全部文章信息,包含 tid
        $articleModel = new IndexArticle;
        $articleList = $articleModel->articleList();
        //获取全部文章关联的标签信息
        $tagModel = new IndexTag;
        $tagAll = $tagModel->getArticleTag();
        
        if($categoryTotal){
            $this->assign('articleAll',$articleList);
            $this->assign('tagAll',$tagAll);
            $this->assign('categoryTotal',$categoryTotal);
            
            return $this->fetch('Category/index');
        }else{
            return $this->error('暂无分类');
        }
        
    }

    //根据cid显示特定的分类信息,属于分类功能
    public function categoryList()
    {
        $cid = input('cid');
        if(!empty($cid)){
            // 分类 cid 不为空，根据cid显示
            $categoryModel = new IndexCategory;
            $categoryTotal = $categoryModel->getList($cid);
            //根据cid获取文章的相关信息，不要分页
            $articleModel = new IndexArticle;
            $articleCategory = $articleModel->articleCategory($cid);
            //获取全部的标签信息
            $tagModel = new IndexTag;
            $tagAll = $tagModel->getArticleTag();

            if($articleCategory){
                $this->assign('articleAll',$articleCategory);
                $this->assign('tagAll',$tagAll);
                $this->assign('categoryTotal',$categoryTotal);
    
                return $this->fetch('Category/index');
            }else{
                return $this->error('此分类无文章');
            }
            
        }
    }

    //标签内容详细显示
    public function tag()
    {
        //获取全部的tag内容
        $tagModel = new IndexTag;
        $tagTotal = $tagModel->getAllTag();
        //获取文章列表，包含tid信息
        $articleModel = new IndexArticle;
        $articleList = $articleModel->articleList();

        if($tagTotal){
            $this->assign('articleList',$articleList);
            $this->assign('tagTotal',$tagTotal);
            
            return $this->fetch('Tag/index');
        }else{
            return $this->error('还没有标签');
        }      
    }


    //评论操作
    public function comment()
    {
        //获取请求输入的数据
        $request = Request::instance();
        $contentAid = $request->param();       //aid,pid,content
        //处理下获取的内容
        $message['aid'] = $contentAid['aid'];           //评论的文章 aid
        $message['pid'] = $contentAid['pid'];           //评论的父级 pid
        $message['content'] = $contentAid['content'];   //评论内容
        $message['ouip'] = $request->ip();      //评论用户ip
        if($message['content'] != ""){
            //评论内容不为空
            $commentModel = new IndexComment;
            $responseList = $commentModel->replyComment($message);
           
            if($responseList){
                $responseList = json_encode($responseList);
                return json($responseList);
            }else{
                $err = ['cmtid'=>0];
                $err = json_encode($err);
                return json($err);
            }   

        }else{
            $err = ['cmtid'=>0];
            $err = json_encode($err);
            return json($err);
        }
        
        
    }

}
