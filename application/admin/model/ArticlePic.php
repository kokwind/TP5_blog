<?php

namespace app\admin\model;
use think\Model;
use think\Request;          //获取当前请求信息
use think\Db;       //使用数据库
use think\Paginator;     //使用分页


class ArticlePic extends Model
{
    public function picList()
    {
        $picList = $this->select();
        return $picList;
    }

    public function addPic($data)
    {
        $add = $this->insert($data);
        return $add;
    }

    public function deleteData($ap_id)
    {
        $res = $this->where('ap_id',$ap_id)
                    ->delete();
        return $res; 
    }
}