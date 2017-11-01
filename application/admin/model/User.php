<?php

namespace app\admin\model;
use think\Model;

class User extends Model
{
    public function checkUser($data)
    {
        //获取登陆账户的信息
        $user = $this->where('name',$data['username'])
                     ->where('password',md5("$data[password]"))
                     ->find();
        if($user){
            //用户名存在,密码正确，则更新登陆时间,登陆ip
            $update = $this->where('name',$data['username'])
                           ->update(['last_time'=>time(),'ip'=>$data['ip']]);
            return $user;
        }else{
            return false;
        }
    }
}
