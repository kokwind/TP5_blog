<?php
namespace app\admin\controller;
use think\Controller;
use app\common\model\Category as AdminCategory;
use think\Request;          //获取当前请求信息

class Category extends Controller
{
    //实现文章分类的相关操作

    public function index(){
        $categoryModel = new AdminCategory;
        $allCategory = $categoryModel->getAllData();
        $this->assign('allCategory',$allCategory);
        return $this->fetch('Category/index');
    }


    //添加分类
    public function add()
    {
        //先检查是否有post请求
        $request_action = isset($_POST['request']) ? $_POST['request']:'';
        $categoryModel = new AdminCategory;

        //有请求是执行添加动作
        if($request_action == 'addpost'){
            //需要得到添加的内容进行验证
            //使用控制器内验证的方式,调用控制器类提供的 validate 方法进行验
            $cname = input('post.cname');
            $result = $this->validate(
                [
                'cname' => $cname
                ],
                [
                'cname' => 'require|max:25'
                ]);
            if(true !== $result){
                //验证失败，输出错误信息
                return $this->error($result);
            }else{
                //验证成功
                $resAdd = $categoryModel->addData();
                if($resAdd){
                    return $this->success('分类添加成功','Category/index');
                }else{
                    return $this->error('分类添加失败');
                }
            }

        }else{
            $allCategory = $categoryModel->getAllData();
            $this->assign('allCategory',$allCategory);
            return $this->fetch('Category/add');
        }
        
    }

    public function edit(){

        $request = Request::instance();
        $method = $request->method();
        $categoryModel = new AdminCategory;

        //get请求，显示要修改的分类
        if($method == 'GET'){
            $cid = input('cid');
            if(!empty($cid)){
                $allCategory = $categoryModel->getAllData();
                $this->assign('allCategory',$allCategory);
                //显示要修改的分类
                $data = $categoryModel->getOneData($cid);
                $this->assign('editCategory',$data);
                return $this->fetch('Category/edit');
            }else{
                return $this->error('分类 cid 不存在');
            }
        }
        else if($method == 'POST'){
            //执行修改动作
            $resEdit = $categoryModel->editData();
            if($resEdit){
                return $this->success('分类修改成功','Category/index');
            }else{
                return $this->error('分类修改失败');
            }
        }

    }

    public function delete(){
        //删除分类
        $categoryModel = new AdminCategory;
        $cid = input('cid');
        if(!empty($cid)){
            //cid 不为空，删除分类
            $resDelete = $categoryModel->deleteData();
            if($resDelete){
                return $this->success('分类删除成功','Category/index');
            }else{
                return $this->error('分类删除失败,请先删除此分类下的文章');
            }
        }else{
            return $this->error('cid不存在，分类删除失败');
        }
        
    }

    
}

