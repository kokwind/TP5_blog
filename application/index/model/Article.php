<?php

namespace app\index\model;
use think\Model;
use think\Db;       //使用数据库
use think\Paginator;     //使用分页
use think\Validate;     //使用tp5的验证器
use think\Request;      //获取当前请求信息 
use Michelf\Markdown;

class Article extends Model
{
    

    //得到一篇文章的具体信息
    public function articleDetail($aid)
    {
        //根据文章 aid 查询需要的数据
        //查询文章表 tpblog_article 以及分类表
        $article = DB::name('article')->alias('a')->field('aid,title,author,content,a.keywords,click,addtime,a.cid,cname')->
        join('tpblog_category tc','a.cid=tc.cid')->where('aid',$aid)->find();
        //需要的标签名称
        $tags = Db::name('article_tag')->alias('at')->join('tpblog_tag tt','at.tid=tt.tid')->where('aid',$aid)->select();
        //需要的分类信息   cname,cid
        $category = Db::name('category')->alias('c')->field('cname,c.cid')->join('tpblog_article a','c.cid=a.cid')->where('aid',$aid)->find();
        //转义文章内容
        $article['content'] = Markdown::defaultTransform($article['content']);
    
        $data['article'] = $article;
        $data['tags'] = $tags;
        $data['category'] = $category;
       
        return $data;
    }
}