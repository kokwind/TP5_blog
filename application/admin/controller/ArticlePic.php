<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use think\Request;          //获取当前请求信息
use think\Validate;     //使用tp5的验证器
use think\Session;
use think\File;
use app\common\model\Article as AdminArticle;
use app\admin\model\ArticlePic as AdminArticlePic;

class ArticlePic extends Base
{
    public function index()
    {
        $picModel = new AdminArticlePic;
        $picList = $picModel->picList();
        if($picList){
            $this->assign('picList',$picList);
            
            return $this->fetch('ArticlePic/index');
        }else{
            return $this->error('查询失败！');
        }
        
    }

    public function add()
    {
        //先检查是否有post请求
        $request_action = isset($_POST['request']) ? $_POST['request']:'';

        if($request_action == 'addpost'){
            //执行添加
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('pic');
            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                // 移动到服务器的上传目录 并且设置不覆盖
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads',true,false);
                
                if($info){
                // 成功上传后 获取上传信息
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getSaveName().'<br>';
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getFilename().'<br><br><br>';

                $request = Request::instance();
                $data = $request->except('request');      // aid,description
                $data['path'] = '/public' . DS . 'uploads/'.$info->getSaveName().'<br>';
                $picModel = new AdminArticlePic;
                $addRes = $picModel->addPic($data);

                if($addRes){
                    $this->success('图片添加成功！','ArticlePic/index');
                }
                }else{
                // 上传失败获取错误信息
                echo $file->getError();
                }
            }
           
        }else{
            return $this->fetch('ArticlePic/add');
        }
    }

    public function delete()
    {
        $ap_id = input('ap_id');        //input('get.id')获取值有限制
        if(!empty($ap_id)){
            $picModel = new AdminArticlePic;
            $resDel = $picModel->deleteData($ap_id);
            if($resDel){
                return $this->success('标签删除成功','ArticlePic/index');
            }else{
                return $this->error('标签删除失败');
            }
        }else{
            return $this->error('标签删除失败');
        }
    
    }


}