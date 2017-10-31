<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Link as AdminLink;
use think\Request;          //获取当前请求信息
use think\Validate;     //使用tp5的验证器

class Link extends Controller
{
    //实现友情链接的相关操作

    public function index()
    {
        $linkModel = new AdminLink;
        $linkList = $linkModel->getAllData();
        $this->assign('linkList',$linkList);
        return $this->fetch('Link/index');
    }
    
    public function add()
    {
        //先检查是否有post请求
        $request_action = isset($_POST['request']) ? $_POST['request']:'';
        $linkModel = new AdminLink;

        //如果有post请求
        if($request_action == 'addpost'){
            //使用控制器内验证的方式,调用控制器类提供的 validate 方法进行验
            $lname = input('post.lname');
            $url = input('post.url');
            $result = $this->validate(
            [
            'lname' => $lname,
            'url' => $url
            ],
            [
            'lname' => 'require|max:25',
            'url' => 'url'
            ]);
        if(true !== $result){
            //验证失败，输出错误信息
            return $this->error($result);
        }else{
            //验证成功
            $resAdd = $linkModel->addData();
            if($resAdd){
                return $this->success('友链添加成功','Link/index');
            }else{
                return $this->error('友链添加失败');
            }
        }

        }else{
            return $this->fetch('Link/add');
        }
 
    }

    public function edit()
    {
        $request = Request::instance();
        $method = $request->method();
        $linkModel = new AdminLink;

        //get请求，显示要修改的分类
        if($method == 'GET'){
            $lid = input('lid');
            if(!empty($lid)){
                $link = $linkModel->getOneData($lid);
                if($link){
                    $this->assign('link',$link);
                    
                    return $this->fetch('Link/edit');
                }else{
                return $this->error('友链修改失败');
                }
                
            }else{
                return $this->error('友链lid为空');
            }
            
        }
        else if($method == 'POST'){
            //执行修改动作
            //使用控制器内验证的方式,调用控制器类提供的 validate 方法进行验
            $lname = input('post.lname');
            $url = input('post.url');
            $result = $this->validate(
            [
            'lname' => $lname,
            'url' => $url
            ],
            [
            'lname' => 'require|max:25',
            'url' => 'url'
            ]);
            if(true !== $result){
                //验证失败，输出错误信息
                return $this->error($result);
            }else{
                $lid = input('post.lid');
                if(!empty($lid)){
                    $resEdit = $linkModel->editData($lid);
                    if($resEdit){
                        return $this->success('友链修改成功','Link/index');
                    }else{
                        return $this->error('友链修改失败');
                    }
                }else{
                    return $this->error('友链修改失败');
                }
            }
        }
        
    }

    public function delete()
    {
        $linkModel = new AdminLink;
        $lid = input('lid');
        if(!empty($lid)){
            //lid 不为空，删除友链
            $resDelete = $linkModel->deleteData($lid);
            if($resDelete){
                return $this->success('友链删除成功','Link/index');
            }else{
                return $this->error('友链删除失败');
            }
        }else{
            return $this->error('lid不存在，友链删除失败');
        }
    
    }

    //管理回收站
    public function recycle()
    {
        //根据recycle传的状态判断： 0 恢复文章； 1 彻底删除文章
        $request = Request::instance();
        $requestArr = $request->param();    //只有一个文章aid 的数组
        if(array_key_exists('lid',$requestArr)){
            $linkModel = new AdminLink;
            $resRecycle = $linkModel->recycleLink($requestArr);
            if($resRecycle){
                return $this->success('回收站操作成功','Recycle/link');
            }else{
                return $this->error('回收站操作失败','Recycle/link');
            }
        }else{
            return $this->error('回收站操作失败','Recycle/link');
        }  
    }

}

