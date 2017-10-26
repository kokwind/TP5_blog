<?php

namespace app\admin\model;
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

    /**
     * 获取tname
     * @param array $tids 文章id
     * @return array $tnames 标签名
     */
    public function getTnames($tids){
 /*       foreach ($tids as $k => $v) {
            $tnames[]=$this->where(array('tid'=>$v))->getField('tname');
        }
        return $tnames; */
    }   
}