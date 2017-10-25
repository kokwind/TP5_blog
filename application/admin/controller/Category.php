<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Category as AdminCategory;
use think\Request;          //获取当前请求信息

class Category extends Controller
{
    //实现文章分类的相关操作

    public function index(){
        
        return $this->fetch();
    }

    public function desk(){
        return $this->fetch();
    }

    //添加文章
    public function add()
    {
        //先检查是否有post请求
        $request_action = isset($_POST['request']) ? $_POST['request']:'';
        
        if($request_action == 'addpost'){
            //category:所属分类； title:文章标题；  author：作者；    tid：标签
            //keywords:关键词;     description:描述      content:内容（markdown）
            //is_original:是否原创 1,0      is_top：是否置顶 1,0     is_show：是否显示 1,0   
            $request = Request::instance();
            echo '请求方法：' . $request->method() . '<br/>';
            echo '资源类型：' . $request->type() . '<br/>';
            echo '访问ip地址：' . $request->ip() . '<br/>';
            echo '是否AJax请求：' . var_export($request->isAjax(), true) . '<br/>';
            echo '请求参数：';
            dump($request->param());

        }else{
            return $this->fetch();
        }
        
    }

    public function edit(){

        //先检查是否有post请求
        $request_action = isset($_POST['request']) ? $_POST['request']:'';
        
        if($request_action == 'editpost'){
            //category:所属分类； title:文章标题；  author：作者；    tid：标签
            //keywords:关键词;     description:描述      content:内容（markdown）
            //is_original:是否原创 1,0      is_top：是否置顶 1,0     is_show：是否显示 1,0   
            $request = Request::instance();
            echo '请求方法：' . $request->method() . '<br/>';
            echo '资源类型：' . $request->type() . '<br/>';
            echo '访问ip地址：' . $request->ip() . '<br/>';
            echo '是否AJax请求：' . var_export($request->isAjax(), true) . '<br/>';
            echo '请求参数：';
            dump($request->param());

        }else{
            return $this->fetch();
        }

    }

    public function delete(){


    }

    
}

