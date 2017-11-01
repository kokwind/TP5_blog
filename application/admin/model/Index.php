<?php

namespace app\admin\model;
use think\Model;
use think\Db;       //使用数据库

class Index extends Model
{
    
    //展示右侧desk显示的信息
    public function getDeskData(){
        //需要显示文章总数，已删除文章数，不显示文章数，评论总数
        $all_article = Db::name('article')->count();
        $delete_article = Db::name('article')->where('is_delete',1)->count();
        $hide_article = Db::name('article')->where('is_show',0)->count();
        $all_comment = Db::name('comment')->count();
        $delete_comment = Db::name('comment')->where('is_delete',1)->count();
        $all_link = Db::name('link')->count();
        $hide_link = Db::name('link')->where('is_show',0)->count();
        $delete_link = Db::name('link')->where('is_delete',1)->count();

        $data = [
            'all_article' => $all_article,
            'delete_article' => $delete_article,
            'hide_article' => $hide_article,
            'all_comment' => $all_comment,
            'delete_comment' => $delete_comment,
            'all_link' => $all_link,
            'hide_link' => $hide_link,
            'delete_link' => $delete_link
        ];

        return $data;
    }
}