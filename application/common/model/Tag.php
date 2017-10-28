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
    
    public function getAllTag()
    {
        //显示Tag的全部内容
        //需要的信息: tpblog_tag表中   tid,tname
        //           tpblog_article_tag表中      aid,tid
        //           tpblog_article表中           aid,title,addtime
        //获取文章列表
        $articleList = Db::name('article')->alias('a')->field('a.aid,title,addtime,tid')->
        join('tpblog_article_tag at','at.aid=a.aid')->where('is_show',1)->where('is_delete',0)->select();
        
        //tname,tid,num
        $tagTotal = Db::name('article_tag')->alias('at')->field('tname,t.tid,count(at.aid) as num')->join('tpblog_tag t','at.tid=t.tid')->join('tpblog_article a','a.aid=at.aid')->where('is_show',1)->where('is_delete',0)->group('tid')->select();
        
        //合并数据
        $data['articleList'] = $articleList;
        $data['tagTotal'] = $tagTotal;
        
        return $data;
        
    }

}