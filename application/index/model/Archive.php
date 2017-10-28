<?php

namespace app\index\model;
use think\Model;
use think\Db;       //使用数据库
use think\Paginator;     //使用分页
use think\Validate;     //使用tp5的验证器
use think\Request;      //获取当前请求信息 

class Archive extends Model
{

    public function getAllArchive()
    {
        //显示归档的全部内容
        //需要的信息: 归档日期，文章标题，文章id，归档日期下文章数量
        //           tpblog_article表中           addtime对应的数量
        //           tpblog_article表中           aid,title,addtime
        //获取文章列表,使用原生查询
        $articleList = Db::query("select aid,title,addtime,from_unixtime(addtime,'%Y') as year from tpblog_article where is_show=1 and is_delete=0");
        //获取每年文章数目
        $yearNum = Db::query("select from_unixtime(addtime,'%Y') as year,count(aid) as num from tpblog_article where is_show=1 and is_delete=0 group by from_unixtime(addtime,'%Y')");
        
        
        //合并数据
        $data['articleList'] = $articleList;
        $data['yearNum'] = $yearNum;
        
        return $data;
        
    }

}