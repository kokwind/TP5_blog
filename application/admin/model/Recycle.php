<?php

namespace app\admin\model;
use think\Model;
use think\Db;       //使用数据库
use think\Paginator;     //使用分页
use think\Validate;     //使用tp5的验证器
use think\Request;          //获取当前请求信息

class Recycle extends Model
{

    public function articleRecycle()
    {
        $articleList = model('Article')
                        ->alias('a')
                        ->field('aid,cname,author,title')
                        ->join('tpblog_category c','a.cid=c.cid')
                        ->where('a.is_delete',1)
                        ->paginate(10,false,[
                            'type'     => 'Bootstrap',
                            'var_page' => 'page',
                            //'path'=>'javascript:AjaxPage([PAGE]);',
                            'query' => request()->param()
                        ]);
    
        return $articleList;
    }

    public function commentRecycle()
    {
        $commentList = model('Comment')
                        ->where('is_delete',1)
                        ->paginate(10,false,[
                            'type'     => 'Bootstrap',
                            'var_page' => 'page',
                            //'path'=>'javascript:AjaxPage([PAGE]);',
                            'query' => request()->param()
                        ]);
    
        return $commentList;
    }

    public function linkRecycle()
    {
        $linkList = model('Link')
                    ->where('is_delete',1)
                    ->paginate(10,false,[
                        'type'     => 'Bootstrap',
                        'var_page' => 'page',
                        //'path'=>'javascript:AjaxPage([PAGE]);',
                        'query' => request()->param()
                    ]);
    
        return $linkList;
    }

}