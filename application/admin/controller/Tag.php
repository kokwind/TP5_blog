<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\common\model\Tag as AdminTag;
use think\Request;          //获取当前请求信息
use think\Session;

class Tag extends Base
{
    //实现文章标签的相关操作

    //显示现有标签
    public function index(){
        
        $tagModel = new AdminTag;
        $data = $tagModel->getAllData();
        $this->assign('tagList',$data);
        return $this->fetch('Tag/index');
    }

    //添加标签
    public function add()
    {
        //先检查是否有post请求
        $request_action = isset($_POST['request']) ? $_POST['request']:'';
        
        if($request_action == 'addpost'){
            
            $request = Request::instance();
            //实例化标签model
            $tagModel = new AdminTag;
            //获得全部的post内容
            $data = $request->param();
            //对提交内容进行判断：添加标签内容不能为空
            $resCheck = $tagModel->addRule($data);
            if(is_bool($resCheck)){     //成功返回 bool(true)
                $resAdd = $tagModel->addData();
                if($resAdd){
                    return $this->success('标签添加成功','Tag/index');
                }else{
                    return $this->error('标签添加失败');
                }
            }
            else if(is_string($resCheck)){     //不成功返回 提示字符串
                //必填内容需要补全,输出错误提示,加载error
                return $this->error($resCheck);
            }

        }else{
            return $this->fetch('Tag/add');
        }
        
    }

    //修改标签
    public function edit(){

        $request = Request::instance();
        $method = $request->method();
        $tagModel = new AdminTag;
        
        //get请求，显示要修改的标签
        if($method == 'GET'){
            //实例化请求信息类
            $request = Request::instance();
            //get请求的数据
            $requestArr = $request->param();        //数组
            if(array_key_exists('tid',$requestArr) && $requestArr['tid'] != ''){
                //数组中有标签 tid,同时也传了 tname ，不同去数据库找了
            
                $this->assign('tag',$requestArr);
                return $this->fetch('Tag/edit');

            }else{

                return $this->error('标签不存在');
            }
        }
        else if($method == 'POST'){
            //提交修改结果
            $data['tid'] = input('post.tid');
            $data['tname'] = input('post.tname');
            $resEdit = $tagModel->editData($data);
            if($resEdit){
                return $this->success('标签修改成功','Tag/index');
            }else{
                return $this->error('标签修改失败');
            }
        }

    }

    //删除标签
    public function delete(){
        $tid = input('tid');        //input('get.id')获取值有限制
        if(!empty($tid)){
            $tagModel = new AdminTag;
            $resDel = $tagModel->deleteData($tid);
            if($resDel){
                return $this->success('标签删除成功','Tag/index');
            }else{
                return $this->error('标签删除失败');
            }
        }else{
            return $this->error('标签删除失败');
        }
    }


}