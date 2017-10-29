<?php

namespace app\common\model;
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


    //前台操作需要的方法
    
    //显示评论
    public function displayComment($aid)
    {
        //根据 aid 显示评论
    }


    //回复评论
    public function replyComment($contentAid,$ouip)
    {
        //得到评论的 aid,pid,content     以及 用户ip
        //存入数据库
        // cmtid 自增  ouid
        if($contentAid['pid'] == 0){
            //评论的是文章，需要作者审核,pid默认为0
            $data['status'] = 0;
            $data['ouip'] = $ouip;
            $data['aid'] = $contentAid['aid'];
            $data['content'] = $contentAid['content'];
            $data['date'] = time();
            //存入数据
            $resADD = Db::name('comment')->insert($data);
            //得到自增主键id 
            $cmtid = DB::name('comment')->getLastInsID();
            
            //得到返回数据
            $returnData = DB::name('comment')->where('cmtid',$cmtid)->find();
            $returnData['date'] = date('Y-m-d H:i:s',$returnData['date']);
            return $returnData;
        }else{
            //评论的是用户，不需要作者审核,pid为传过来的pid
            
            $data['ouip'] = $ouip;
            $data['pid'] = $contentAid['pid'];
            $data['aid'] = $contentAid['aid'];
            $data['content'] = $contentAid['content'];
            $data['date'] = time();
            $data['status'] = 1;
            //存入数据
            $resADD = Db::name('comment')->insert($data);
            //得到自增主键id 
            $cmtid = DB::name('comment')->getLastInsID();
            
            //得到返回数据
            $returnData = DB::name('comment')->where('cmtid',$cmtid)->find();
            $returnData['date'] = date('Y-m-d H:i:s',$returnData['date']);
            return $returnData;
        }
    }

}