<?php

namespace app\common\model;
use think\Model;
use think\Db;       //使用数据库
use think\Request;          //获取当前请求信息
use think\Paginator;     //使用分页
use think\Validate;     //使用tp5的验证器

class Category extends Model
{
    //实现文章分类的增删改查

    //获得全部数据
    public function getAllData(){
        $data = $this->select();
        
        return $data;   
    }

    public function getOneData($cid){
        $category = $this->where('cid',$cid)->find();
        return $category;
    }

    public function addData(){
        //使用模型的data方法批量赋值
        $this->data($_POST);
        //过滤post数组中的非数据表字段数据，增加数据，save方法会出发自动完成数据，实现
        $resAdd = $this->allowField(true)->save();

        return $resAdd;
    }

    public function editData(){
        //根据主键 cid 实现修改
        $cid = input('post.cid');
        if(!empty($cid)){
            $resEdit = $this->allowField(['cname','keywords','description'])->save($_POST, ['cid' => $cid]);
            return $resEdit;
        }else{
            return false;
        }
    }

    public function deleteData(){
        //实现删除分类功能
        //需要检查当前分类是否有文章，有文章不能删除
        $cid = input('cid');
        //cid 不为空，判断
        $articleData = Db::name('article')->field('aid')->where('cid',$cid)->where('is_show',1)->where('is_delete',0)->select();
        if(!empty($articleData)){
            $this->error='请先删除此分类下的文章';
            return false;
        }else{
            //可以删除,模型删除
            $resDelete = $this->where('cid',$cid)->delete();
            if($resDelete){
                return true;
            }else{
                return false;
            }
        }
    }

    //前台需要的方法
    
    /**
     * 获得全部分类信息 各分类的cid,cname,num
     * @return array $categoryTotal 全部分类信息
     */
    public function getAllCategory()
    {
        //用于主页显示和分类界面显示
        $categoryTotal = $this->alias('c')
                            ->field('cname,c.cid,count(aid) as num')
                            ->join('tpblog_article a','c.cid=a.cid')
                            ->where('a.is_show',1)
                            ->where('a.is_delete',0)
                            ->group('c.cid')
                            ->select();
        
        return $categoryTotal;
        
    }

    /**
     * 根据 cid 获得特定分类的文章信息
     * @param strind $cid 分类id 
     * @return array $categoryTotal 指定分类的所有信息
     */
    public function getList($cid)
    {
        //根据分类 cid ，显示主页的全部文章
        //分类的总数信息 cname,cid,num
        $categoryTotal = $this->alias('c')
                            ->field('cname,c.cid,count(aid) as num')
                            ->join('tpblog_article a','c.cid=a.cid')
                            ->where('c.cid',$cid)
                            ->where('a.is_show',1)
                            ->where('a.is_delete',0)
                            ->group('c.cid')
                            ->select();
        
        return $categoryTotal;
        
    }

    /**
     * 获得单篇文章分类信息
     * @param strind $aid 文章id 单篇文章
     * @return array $category 一篇文章的一个分类信息
     */
    public function getArticleCategory($aid)
    {
        //需要的分类信息   cname,cid
        $category = $this->alias('c')
                        ->field('cname,c.cid')
                        ->join('tpblog_article a','c.cid=a.cid')
                        ->where('aid',$aid)
                        ->find();
        
        return $category;
    }
}