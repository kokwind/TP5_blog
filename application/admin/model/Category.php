<?php

namespace app\admin\model;
use think\Model;

class Category extends Model
{
    //实现文章分类的增删改查



    //获得全部数据
    public function getAllData(){
        $data=$this->select();
        
        return $data;   
    }

}