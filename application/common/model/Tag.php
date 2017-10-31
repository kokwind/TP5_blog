<?php

namespace app\common\model;
use think\Model;
use think\Db;       //使用数据库
use think\Paginator;     //使用分页
use think\Validate;     //使用tp5的验证器
use think\Request;          //获取当前请求信息

class Tag extends Model
{
    //定义一个添加标签数据的验证
    //对提交内容进行判断：标签内容不能为空
    public function addRule($data){
        $validate = new Validate([
            'tnames' => 'require'
            ]);
        $checkData = [
            'tnames' => $data['tnames']
        ];
        //返回验证结果
        //return $validate->check($checkData);
        if($validate->check($checkData)){
            return $validate->check($checkData);
            //dump($validate->check($checkData));     //成功是   bool(true)
        }else{
            return $validate->getError();
            //echo $validate->getError();         //不成功是提示字符串
        }
    
    }

    // 添加标签
    public function addData(){

        $tags = input('post.tnames');        // 得到提交的 标签数据
        $tagArr = explode('、',$tags);
        foreach($tagArr as $k=>$v){
            $v = trim($v);      //去除字符串首尾处的空白字符（或者其他字符）
            if(!empty($v)){
                $data['tname'] = $v;
                $this->insert($data);
            }
        }
        
        return true;
    }

    // 修改数据
    public function editData(){
        $tid = input('post.tid');
        $tname = input('post.tname');
        $res = $this->where('tid',$tid)->update(['tname'=>$tname]);
        return $res;
    }

    // 删除数据
    public function deleteData(){
        
        $tid = input('tid');        //input('get.id')获取值有限制
        if(!empty($tid)){
            $res = $this->where('tid',$tid)->delete();
            return $res;
        }
        
    }

    //获得全部数据
    public function getAllData(){

        $data = $this->select();
        return $data;   
    }

    
    //前台需要的功能
    
    /**
     * 获得全部的标签数据
     * @return array $tagTotal 全部的标签，包含 tname,tid,couunt
     */
    public function getAllTag()
    {
        //显示Tag的全部内容        
        //tname,tid,num
        $tagTotal = $this->alias('t')
                        ->field('tname,t.tid,count(at.aid) as num')
                        ->join('tpblog_article_tag at','at.tid=t.tid')
                        ->join('tpblog_article a','a.aid=at.aid')
                        ->where('is_show',1)
                        ->where('is_delete',0)
                        ->group('tid')
                        ->select();
        
        return $tagTotal;
        
    }

    /**
     * 获得与文章关联的标签数据
     * @param strind $aid 文章id 'all'为显示全部的的关联数据
     * @return array $tags 一篇文章的多个标签
     */
    public function getArticleTag($aid='all')
    {
        if($aid != 'all'){
            //需要的一篇文章的标签名称
            $tags = $this->alias('tt')
                        ->join('tpblog_article_tag at','at.tid=tt.tid')
                        ->where('aid',$aid)
                        ->select();

            return $tags;
        }
        else if($aid == 'all'){
            //需要全部的文章标签关联数据
            $tagAll = $this->alias('tt')
                        ->join('tpblog_article_tag at','at.tid=tt.tid')
                        ->select();

            return $tagAll;
        }
        
    }

    
}