<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;          //获取当前请求信息
use think\Validate;     //使用tp5的验证器
use app\common\model\Article as AdminArticle;
use app\common\model\Category as AdminCategory;
use app\common\model\Tag as AdminTag;

class Article extends Controller
{
    //实现文章的增删改查
    
    //显示文章列表
    public function index(){
        
        //获取分页的文章信息
        $articleModel = new AdminArticle;
        $articleList = $articleModel->getPageDate();
        //获取文章的分类信息
        $categoryModel = new AdminCategory;
        $categoryList = $categoryModel->getAllData();
        //获取文章的标签信息
        $tagModel = new AdminTag;
        $tagList = $tagModel->getArticleTag();
        
        $this->assign('articleList',$articleList);
        $this->assign('categoryList',$categoryList);
        $this->assign('tagList',$tagList);
       
        return $this->fetch('Article/index');
    }


    //添加文章
    public function add()
    {
        //先检查是否有post请求
        $request_action = isset($_POST['request']) ? $_POST['request']:'';
        
        if($request_action == 'addpost'){
            
            $request = Request::instance();
            $requestArr = $request->except(['request']);
            $requestArr['tid'] = isset($requestArr['tid']) ? $requestArr['tid']:'';
            
            //实例化文章model
            $articleModel = new AdminArticle;
            //获得全部的post内容
            $data = $requestArr;
            //将标签数组转为字符串，用于验证
            if($requestArr['tid']){
                $data['tid'] = implode(' ',$data['tid']);
            }

            //对提交内容进行判断：必须选择标签，文章标题必填，作者必填，文章内容必填
            $resCheck = $articleModel->addRule($data);
            if(is_bool($resCheck)){     //成功返回 bool(true)
                //证明必填内容都填了
                //准备添加文章
                $resAddArticle = $articleModel->addArticle();
                if($resAddArticle){
                    //添加成功
                    return $this->success('文章添加成功','Article/index');
                }else{
                    //必填内容需要补全,输出错误提示,加载error
                    return $this->error("添加失败");
                }

            }
            else if(is_string($resCheck)){     //不成功返回 提示字符串
                //必填内容需要补全,输出错误提示,加载error
                return $this->error($resCheck);
            }
        }else{
            //增加文章界面需要显示的信息
            //可供选择的分类信息
            $categoryModel = new AdminCategory;
            $allCategory = $categoryModel->getAllData();
            //可供选择的标签信息
            $tagModel = new AdminTag;
            $allTag = $tagModel->getAllTag();
            // $articleModel = new AdminArticle;
            // $data = $articleModel->getAddIndex();
            //赋值
            $this->assign('allCategory',$allCategory);
            $this->assign('allTag',$allTag);
            //渲染模板
            return $this->fetch();
        }
        
    }

    public function edit(){

        //先检查是否有post请求
        //$request_action = isset($_GET['request']) ? $_GET['request']:'';
        $request = Request::instance();
        //echo '请求方法：' . $request->method() . '<br/>';
        $method = $request->method();
        $articleModel = new AdminArticle;

        //get请求，显示要修改的文章
        if($method == 'GET'){
            //实例化请求信息类
            $request = Request::instance();
            //get请求的数据
            $requestArr = $request->param();        //数组
            if(array_key_exists('aid',$requestArr) && $requestArr['aid'] != ''){
                //数组中有文章id

                //在article 模型内查找单篇文章相关信息
                $editArticle = $articleModel->updateArticle($method,$requestArr);
                //查找全部的tag信息，包含了aid，  aid,tid,tname
                $tagModel = new AdminTag;
                $tagList = $tagModel->getArticleTag();
                //可供选择的分类信息
                $categoryModel = new AdminCategory;
                $allCategory = $categoryModel->getAllData();
                
                $this->assign('editArticle',$editArticle);
                $this->assign('tagList',$tagList);
                $this->assign('categoryList',$allCategory);
                return $this->fetch('Article/edit');

            }else{
               
                return $this->error('文章不存在','Article/index');
            }

        }else if($method == 'POST'){        //post请求执行修改
            //post请求的数据
            $requestArr = $request->except(['request']);        //数组
            //删除一个凭空多出来的数据
            unset($requestArr['test']);
            
            $requestArr['tid'] = isset($requestArr['tid']) ? $requestArr['tid']:'';
            //获得全部的post内容
            $data = $requestArr;
            //将标签数组转为字符串，用于验证
            if($requestArr['tid']){
                $data['tid'] = implode(' ',$data['tid']);
            }
            //对提交内容进行判断：必须选择标签，文章标题必填，作者必填，文章内容必填
            $resCheck = $articleModel->addRule($data);
            if(is_bool($resCheck)){     //成功返回 bool(true)
                //证明必填内容都填了
                //恢复标签
                $data['tid'] = explode(' ',$data['tid']);
                //修改文章分2部分，一部分修改文章表内容，一部分修改标签文章表内容，标签文章表是中间关联表
                //准备修改文章
                $resUpdateArticle = $articleModel->updateArticle($method,$data);
                if($resUpdateArticle){
                    
                    return $this->success('文章修改成功','Article/index');
                }else{
                    //必填内容需要补全,输出错误提示
                    return $this->error("修改失败");
                }

            }else if(is_string($resCheck)){     //不成功返回 提示字符串
                //必填内容需要补全,输出错误提示
                return $this->error($resCheck);
            }
        }else{

            return $this->error('文章不存在','Article/index');
        }

    }

    public function delete()
    {

        $request = Request::instance();
        $requestArr = $request->param();    //只有一个文章aid 的数组
        if(array_key_exists('aid',$requestArr)){
            $articleModel = new AdminArticle;
            $resDelArticle = $articleModel->deleteArticle($requestArr);
            if($resDelArticle){
                return $this->success('文章删除成功','Article/index');
            }else{
                return $this->error('文章删除失败','Article/index');
            }
        }else{
            //文章 aid 不存在
            return $this->error('文章aid不存在','Article/index');
        }
        
    }
    
    public function recycle()
    {
        //根据recycle传的状态判断： 0 恢复文章； 1 彻底删除文章
        $request = Request::instance();
        $requestArr = $request->param();    //只有一个文章aid 的数组
        if(array_key_exists('aid',$requestArr)){
            $articleModel = new AdminArticle;
            $resRecycle = $articleModel->recycleArticle($requestArr);
            if($resRecycle){
                return $this->success('回收站操作成功','Recycle/article');
            }else{
                return $this->error('回收站操作失败','Recycle/article');
            }
        }else{
            //文章 aid 不存在
            return $this->error('文章aid不存在','Recycle/article');
        }
        
    }
    
}