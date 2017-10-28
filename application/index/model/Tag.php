<?php

namespace app\index\model;
use think\Model;
use think\Db;       //使用数据库
use think\Paginator;     //使用分页
use think\Validate;     //使用tp5的验证器
use think\Request;      //获取当前请求信息 

class Tag extends Model
{

    public function getAllTag()
    {
        //显示Tag的全部内容
        //需要的信息: tpblog_tag表中   tid,tname
        //           tpblog_article_tag表中      aid,tid
        //           tpblog_article表中           aid,title,addtime
        //获取文章列表
        $articleList = Db::name('article')->alias('a')->field('a.aid,title,addtime,tid')->
        join('tpblog_article_tag at','at.aid=a.aid')->where('is_show',1)->where('is_delete',0)->select();
        //获取每种标签文章数目
        //$tagNum = Db::name('article_tag')->field('tid,count(aid) AS count')->group('tid')->select();
        //需要的标签总数
        //$tagAll = Db::name('tag')->select();
        //tname,tid,num
        $tagTotal = Db::name('article_tag')->alias('at')->field('tname,t.tid,count(at.aid) as num')->join('tpblog_tag t','at.tid=t.tid')->join('tpblog_article a','a.aid=at.aid')->where('is_show',1)->where('is_delete',0)->group('tid')->select();
        //dump($tagTotal);
        //exit;
        //合并数据
        $data['articleList'] = $articleList;
        //$data['tagAll'] = $tagAll;
        //$data['tagNum'] = $tagNum;
        $data['tagTotal'] = $tagTotal;
        
        return $data;
        
    }

}