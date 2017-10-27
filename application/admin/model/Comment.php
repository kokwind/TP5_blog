<?php

namespace app\admin\model;
use think\Model;
use think\Request;          //获取当前请求信息
use think\Db;       //使用数据库
use think\Paginator;     //使用分页

class Comment extends Model
{
    //得到全部显示的信息
    public function getPageDate()
    {
        //分页查询,查询出未删除的评论
        $commentList = $this->alias('c')->field('cmtid,ouid,date,c.content,status,title')->where('c.is_delete',0)->join('tpblog_article ta','c.aid=ta.aid')->paginate(10,false,[
            'type'     => 'Bootstrap',
            'var_page' => 'page',
            //'path'=>'javascript:AjaxPage([PAGE]);',
            'query' => request()->param()
           ]);

        return $commentList;
    }

    public function changeCommentStatus()
    {
        //接收修改的 cmtid 和状态 status
        // 获取当前请求的所有变量（经过过滤）
        $data = Request::instance()->param();
        if(!empty($data['cmtid'])){
            //评论id不为空
            $resChange = $this->where('cmtid',$data['cmtid'])->update(['status'=>$data['status']]);
            return $resChange;
        }else{
            return false;
        }
    }

    public function deleteComment()
    {
        $cmtid = input('cmtid');
        if(!empty($cmtid)){
            //评论id不为空
            $delChange = $this->where('cmtid',$cmtid)->update(['is_delete'=>1]);
            return $delChange;
        }else{
            return false;
        }
    }

    public function recycleComment($data)
    {
        if(array_key_exists('cmtid',$data)){
            //评论 cmtid存在
            if($data['status'] == 0){
                //恢复评论，is_delete 设为 0
                $res = $this->where('cmtid',$data['cmtid'])->update(['is_delete'=>0,'status'=>1]);
                return $res;
            }
            else if($data['status'] == 1){
                //彻底删除评论
                //删除文章表信息
                $res = $this->where('cmtid',$data['cmtid'])->delete();

                if($res){
                    return true;
                }else{
                    return false;
                }
                
            }
        }
    }

}