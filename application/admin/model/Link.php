<?php

namespace app\admin\model;
use think\Model;
use think\Request;          //获取当前请求信息
use think\Db;       //使用数据库
use think\Paginator;     //使用分页


class Link extends Model
{
    //自动完成数据更新
    protected $auto = [
        'is_delete'=>0,       //新增和修改时，把is_delete字段设置为0
    ];

    public function getAllData()
    {
        $linkList = $this->where('is_show',1)
                        ->where('is_delete',0)
                        ->paginate(10,false,[
                            'type'     => 'Bootstrap',
                            'var_page' => 'page',
                            //'path'=>'javascript:AjaxPage([PAGE]);',
                            'query' => request()->param()
                        ]);

        return $linkList;
    }

    public function getOneData($lid)
    {
        $link = $this->where('lid',$lid)
                    ->find();
        return $link;
    }

    public function addData()
    {
        //执行添加数据
        //使用模型的data方法批量赋值
        $this->data($_POST);
        //过滤post数组中的非数据表字段数据，增加数据，save方法会出发自动完成数据，实现
        $resAdd = $this->allowField(true)
                    ->save();

        return $resAdd;
    }

    public function editData($lid)
    {
        //执行修改数据
        //根据主键 lid 实现修改
        $resEdit = $this->allowField(['lname','url','is_show'])
                        ->save($_POST, ['lid' => $lid]);
        return $resEdit;
    }

    public function deleteData($lid)
    {
        //执行删除数据
        $resDelete = $this->where('lid',$lid)
                        ->update(['is_delete'=>1,'is_show'=>0]);
        return $resDelete;

    }

    public function recycleLink($data)
    {
        if(array_key_exists('lid',$data)){
            //评论 cmtid存在
            if($data['status'] == 0){
                //恢复友联，is_delete 设为 0
                $res = $this->where('lid',$data['lid'])
                            ->update(['is_delete'=>0,'is_show'=>1]);
                return $res;
            }
            else if($data['status'] == 1){
                //彻底删除友链
                $res = $this->where('lid',$data['lid'])
                            ->delete();
                return $res;
            }
        }
    }

    public function showRecycleLink()
    {
        $linkList = $this->where('is_delete',1)
                        ->paginate(10,false,[
                            'type'     => 'Bootstrap',
                            'var_page' => 'page',
                            //'path'=>'javascript:AjaxPage([PAGE]);',
                            'query' => request()->param()
                        ]);

        return $linkList;
    }

}