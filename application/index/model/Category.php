<?php

namespace app\index\model;
use think\Model;
use think\Db;       //使用数据库
use think\Paginator;     //使用分页
use think\Validate;     //使用tp5的验证器
use think\Request;      //获取当前请求信息 
use phpMarkdownLib\Michelf\Markdown;     //Get Markdown class 

class Category extends Model
{

    //定义一个函数用于解析markdown的文章内容
    public function getMarkdown($content){

        // Install PSR-0-compatible class autoloader
        spl_autoload_register(function($class){
            require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
        });

        // Get Markdown class

        // Read file and pass content through the Markdown parser
        //$text = file_get_contents('Readme.md');
        $html = Markdown::defaultTransform($content);
        return $html;
    }

    public function getAllCategory()
    {
        //显示主页的全部文章
        //需要的信息:文章表信息 tpblog_article  标签名 tpblog_article ==> tpblog_tag
        $articleAll = Db::name('article')->where('is_show',1)->where('is_delete',0)->select();
        //获取文章分类数
        $categoryNum = Db::name('article')->field('cid,count(aid) AS count')->group('cid')->select();
        //需要的标签名称
        $tagAll = Db::name('article_tag')->alias('at')->join('tpblog_tag tt','at.tid=tt.tid')->select();
        //需要的分类信息
        $categoryAll = Db::name('category')->field('cid,cname')->select();
        //合并数据
        $data['articleAll'] = $articleAll;
        $data['tagAll'] = $tagAll;
        $data['categoryAll'] = $categoryAll;
        $data['categoryNum'] = $categoryNum;
        
        return $data;
        
    }

    public function getList($cid)
    {
        //根据分类 cid ，显示主页的全部文章
        //需要的信息:文章表信息 tpblog_article  标签名 tpblog_article ==> tpblog_tag
        $articleAll = Db::name('article')->where('cid',$cid)->where('is_show',1)->where('is_delete',0)->select();
        //获取文章分类数
        $categoryNum = Db::name('article')->field('cid,count(aid) AS count')->group('cid')->select();
        //需要的标签名称
        $tagAll = Db::name('article_tag')->alias('at')->join('tpblog_tag tt','at.tid=tt.tid')->select();
        //需要的分类信息
        $categoryAll = Db::name('category')->field('cid,cname')->select();
        //合并数据
        $data['articleAll'] = $articleAll;
        $data['tagAll'] = $tagAll;
        $data['categoryAll'] = $categoryAll;
        $data['categoryNum'] = $categoryNum;
        
        return $data;
        
    }

}