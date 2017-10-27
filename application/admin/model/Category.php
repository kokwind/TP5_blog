<?php

namespace app\admin\model;
use think\Model;
use think\Db;       //使用数据库
use think\Request;          //获取当前请求信息

class Category extends Model
{
    //实现文章分类的增删改查


    //获得全部数据
    public function getAllData(){
        $data = $this->select();
        
        return $data;   
    }

    public function getOneData($cid){
        $data = $this->where('cid',$cid)->find();
        return $data;
    }

    public function addData(){
        //使用模型的data方法批量赋值
        $this->data($_POST);
        //过滤post数组中的非数据表字段数据，增加数据，save方法会出发自动完成数据，实现
        $resAdd = $this->allowField(true)->save();

        return $resAdd;
    }

    public function editData(){
        //根据主键 cid 实现修改
        $cid = input('post.cid');
        if(!empty($cid)){
            $resEdit = $this->allowField(['cname','keywords','description'])->save($_POST, ['cid' => $cid]);
            return $resEdit;
        }else{
            return false;
        }
    }

    public function deleteData(){
        //实现删除分类功能
        //需要检查当前分类是否有文章，有文章不能删除
        $cid = input('cid');
        //cid 不为空，判断
        $articleData = Db::name('article')->field('aid')->where('cid',$cid)->where('is_show',1)->where('is_delete',0)->select();
        if(!empty($articleData)){
            $this->error='请先删除此分类下的文章';
            return false;
        }else{
            //可以删除,模型删除
            $resDelete = $this->where('cid',$cid)->delete();
            if($resDelete){
                return true;
            }else{
                return false;
            }
        }
    }

}