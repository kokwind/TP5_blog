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
        $commentList = $this->alias('c')
                            ->field('cmtid,ouid,date,c.content,status,title,c.aid')
                            ->where('c.is_delete',0)
                            ->where('ta.is_delete',0)
                            ->join('tpblog_article ta','c.aid=ta.aid')
                            ->paginate(10,false,[
                                'type'     => 'Bootstrap',
                                'var_page' => 'page',
                                //'path'=>'javascript:AjaxPage([PAGE]);',
                                'query' => request()->param()
                            ]);

        return $commentList;
    }

    /**
     * @param strind $data 需要审核的评论cmtid和根据status判断通过还是不通过审核
     * @return array $resChange 审核结果
     */
    public function changeCommentStatus($data)
    {
        //接收修改的 cmtid 和状态 status
        //评论id不为空
        $resChange = $this->where('cmtid',$data['cmtid'])
                        ->update(['status'=>$data['status']]);
        return $resChange;
        
    }

    public function deleteComment($cmtid)
    {
        //评论id不为空
        $delChange = $this->where('cmtid',$cmtid)
                        ->update(['is_delete'=>1]);
        return $delChange;
        
    }

    public function recycleComment($data)
    {
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
                return $res;
            }

    }

    public function showRecycleComment()
    {
        $commentList = $this->where('is_delete',1)
                            ->paginate(10,false,[
                                'type'     => 'Bootstrap',
                                'var_page' => 'page',
                                //'path'=>'javascript:AjaxPage([PAGE]);',
                                'query' => request()->param()
                            ]);

        return $commentList;
    }


    //前台操作需要的方法
    
    //显示评论
    public function displayComment($aid)
    {
        //根据 aid 显示评论
    }


    /**
     * 实现回复评论的功能
     * @param strind $message 评论内容 aid,pid,content,ouip
     * @return array $commentAll 一篇文章的全部评论信息
     */
    public function replyComment($message)
    {
        //得到评论的 aid,pid,content     以及 用户ip
        //存入数据库
        // cmtid 自增  ouid
        if($message['pid'] == 0){
            //评论的是文章，需要作者审核,pid默认为0
            $data['status'] = 0;
            $data['ouip'] = $message['ouip'];
            $data['aid'] = $message['aid'];
            $data['content'] = $message['content'];
            $data['date'] = time();
            //存入数据
            $resADD = $this->insert($data);
            //得到自增主键id 
            $cmtid = $this->getLastInsID();
            
            //得到返回数据
            $returnData = $this->where('cmtid',$cmtid)->find();
            $returnData['date'] = date('Y-m-d H:i:s',$returnData['date']);
            return $returnData;
        }else{
            //评论的是用户，不需要作者审核,pid为传过来的pid
            
            $data['ouip'] = $message['ouip'];
            $data['pid'] = $message['pid'];
            $data['aid'] = $message['aid'];
            $data['content'] = $message['content'];
            $data['date'] = time();
            $data['status'] = 1;
            //存入数据
            $resADD = $this->insert($data);
            //得到自增主键id 
            $cmtid = $this->getLastInsID();
            
            //得到返回数据
            $returnData = $this->where('cmtid',$cmtid)->find();
            $returnData['date'] = date('Y-m-d H:i:s',$returnData['date']);
            return $returnData;
        }
    }

    /**
     * 获得单篇文章全部评论信息
     * @param strind $aid 文章id 单篇文章
     * @return array $commentAll 一篇文章的全部评论信息
     */
    public function getArticleComments($aid)
    {
        //直接获得文章的全部评论，由controller完成分离数据
        $commentAll = $this->where('aid',$aid)
                        ->where('is_delete',0)
                        ->order('date desc')
                        ->select();

        return $commentAll;
    }
}