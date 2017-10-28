<?php
namespace app\index\controller;
use think\Controller;
//use app\index\model\Index as IndexIndex;
use think\Request;          //获取当前请求信息
use app\common\model\Article as IndexArticle;
use app\index\model\Archive as IndexArchive;
use app\common\model\Category as IndexCategory;
use app\common\model\Tag as IndexTag;


class Index extends Controller
{
    public function index()
    {
        $indexModel = new IndexArticle;
        $data = $indexModel->getPageDate();
            
        $this->assign('articleAll',$data['articleList']);
        $this->assign('tagAll',$data['tagList']);
        $this->assign('categoryAll',$data['categoryList']);
        return $this->fetch('Index/index');

    }

    //文章详细显示
    public function article()
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
        $categoryModel = new IndexCategory;
        $data = $categoryModel->getAllCategory();
            
        $this->assign('articleAll',$data['articleAll']);
        $this->assign('tagAll',$data['tagAll']);
        //$this->assign('categoryAll',$data['categoryAll']);
        //$this->assign('categoryNum',$data['categoryNum']);
        $this->assign('categoryTotal',$data['categoryTotal']);
        
        return $this->fetch('Category/index');

    }

    //根据cid显示特定的分类信息,属于分类功能
    public function categoryList()
    {
        $cid = input('cid');
        if(!empty($cid)){
            // 分类 cid 不为空，根据cid显示
            $categoryModel = new IndexCategory;
            $data = $categoryModel->getList($cid);
            if($data['articleAll']){
                $this->assign('articleAll',$data['articleAll']);
                $this->assign('tagAll',$data['tagAll']);
                //$this->assign('categoryAll',$data['categoryAll']);
                //$this->assign('categoryNum',$data['categoryNum']);
                $this->assign('categoryTotal',$data['categoryTotal']);
    
                return $this->fetch('Category/index');
            }else{
                return $this->error('此分类无文章');
            }
            
        }
    }

    //标签内容详细显示
    public function tag()
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
