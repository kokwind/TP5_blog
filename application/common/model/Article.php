<?php

namespace app\common\model;
use think\Model;
use think\Db;       //使用数据库
use think\Paginator;     //使用分页
use think\Validate;     //使用tp5的验证器
use think\Request;          //获取当前请求信息
use Michelf\Markdown;

class Article extends Model
{

    //定义一个实现添加文章数据的验证
    //对提交内容进行判断：必须选择标签，文章标题必填，作者必填，文章内容必填
    public function addRule($data){
        $validate = new Validate([
            'tid' => 'require', 
            'title' => 'require',
            'author' => 'require',
            'content' => 'require'
            ]);
        $checkData = [
            'tid' => $data['tid'], 
            'title' => $data['title'],
            'author' => $data['author'],
            'content' => $data['content']
        ];

        //返回验证结果  
        if($validate->check($checkData)){
            return $validate->check($checkData);       //成功是   bool(true) 
        }else{
            return $validate->getError();       //不成功是提示字符串         
        }
    
    }

    //设置自动完成的字段，支持键值对数组和索引数组,新增和更新时都会使用
    protected $auto = [
        'click'=>0,           //新增和修改时，把click字段设置为0
        'is_delete'=>0,       //新增和修改时，把is_delete字段设置为0
        'addtime',           //新增时，在addtime字段写入当前时间戳
        'description',     //新增和修改时,description字段为方法的返回值
        'keywords'
        ];

    // 获得描述；供自动完成调用,新增和修改文章时
    protected function setDescriptionAttr(){
        $description = input('post.description');
        if(!empty($description)){
            return $description;
        }else{
            $data = input('post.content');
            $des = htmlspecialchars_decode($data);
            $des = Markdown::defaultTransform($des);
            $des = mb_substr(strip_tags($des),0,200,'utf-8');
            
            return $des;
        }
    }

    // 将keywords的顿号转换为英文逗号
    protected function setKeywordsAttr(){
        $keywords = input('post.keywords');
        return str_replace('、', ',', $keywords);
    }

     // 给添加时间赋值当前时间戳
     protected function setAddtimeAttr(){
        return time();
    }


    /**
     * 获得后台文章全部，带有分页信息
     * @return array $articleList 带有分页信息的全部文章
     */
    public function getPageDate()
    {
        //查询文章的相关信息
        //需要显示的字段，在文章表(tpblog_article)中：  aid,title,author,is_original,is_show,is_top,click,addtime
       $articleList = $this->where('is_show',1)
                            ->where('is_delete',0)
                            ->paginate(10,false,[
                                'type'     => 'Bootstrap',
                                'var_page' => 'page',
                                //'path'=>'javascript:AjaxPage([PAGE]);',
                                'query' => request()->param()
                            ]);

       return $articleList;
    }

    /**
     * 实现添加文章的功能，同时要处理一个中间关联表 tpblog_article_tag
     */
    public function addArticle()
    {
        
        //使用模型的data方法批量赋值
        $this->data($_POST);
        //过滤post数组中的非数据表字段数据，增加数据，save方法会出发自动完成数据，实现
        $resAdd = $this->allowField(true)->save();
        //获取新增文章的自增 aid
        $articleId = $this->aid;
       
        //获取提交传入的数组 tid ,添加到tpblog_article_tag 表
        $tags = input('post.tid/a');        // '/a'为变量修饰符
        $tagData = [];
        for($i=0;$i<count($tags);$i++){
            $tagData[] = ['aid'=>$articleId,'tid'=>$tags[$i]];
        }
        //插入标签数组
        $resTag = Db::name('article_tag')->insertAll($tagData);
       if(!$resAdd || !$resTag){
           return false;
       }
        return true;
        
    }

    //修改文章功能实现
    /**
     * 实现文章的修改功能
     */
    public function updateArticle($method,$data)
    {
        
        if($method == 'GET'){
            //根据aid查找修改文章,给模板显示
            $article = $this->where('aid',$data['aid'])
                            ->find();

            return $article;
        }
        else if($method == 'POST'){
            //根据aid实现文章修改   
            //分类不用考虑，修改了cid就行   
            $tagData = $data['tid'];      //数组
            //先删除原来的标签，再插入新的标签
            $delTag = Db::name('article_tag')
                        ->where('aid',$data['aid'])
                        ->delete();
            for($i=0;$i<count($tagData);$i++){
                $resTag[] = Db::name('article_tag')
                            ->insert([ 'aid'=>$data['aid'],'tid'=>$tagData[$i] ]);
            }
            
            //剩下的数据是文章表的 $data
            unset($data['tid']);        
            //修改文章表
            $resArticle = $this->where('aid',$data['aid'])
                            ->update($data);
            
            if($resTag && $resArticle){
                //修改成功
                return true;
            }else{
                return false;
            }
        }
    }

    //删除文章
    public function deleteArticle($data)
    {
        //删除文章表中的文章,设置 is_show=0,is_delete=1
        $resDelArticle = $this->where('aid',$data['aid'])
                            ->update(['is_show'=>0,'is_delete'=>1]);

        return $resDelArticle;
    }

    public function recycleArticle($data)
    {
        //判断是回复文章还是彻底删除
        if($data['status'] == 0){
            //恢复文章，is_delete 设为 0
            $res = $this->where('aid',$data['aid'])->update(['is_delete'=>0,'is_show'=>1]);
            return $res;
        }
        else if($data['status'] == 1){
            //彻底删除文章
            //删除文章表信息
            $resArticle = $this->where('aid',$data['aid'])->delete();
            //删除 tpblog_article_tag 中的 tid 标签信息
            $resTag = Db::name('article_tag')->where('aid',$data['aid'])->delete();

            if($resArticle && $resTag){
                return true;
            }else{
                return false;
            }
            
        }
    }

    /**
     * 显示已标记的删除文章，包含分页信息
     * @return array $recycleList 全部删除的文章信息
     */
    public function showRecycleArticle()
    {
        $recycleList = $this->alias('a')
                            ->field('aid,cname,author,title')
                            ->join('tpblog_category c','a.cid=c.cid')
                            ->where('a.is_delete',1)
                            ->paginate(10,false,[
                                'type'     => 'Bootstrap',
                                'var_page' => 'page',
                                //'path'=>'javascript:AjaxPage([PAGE]);',
                                'query' => request()->param()
                            ]);

        return $recycleList;
    }

    //下面是 index 前台需要的功能
    
    /**
     * 获得单篇文章详细内容
     * @param strind $aid 文章id 单篇文章
     * @return array $article 一篇文章的全部信息
     */
    public function articleDetail($aid)
    {
        //根据文章 aid 查询需要的数据
        //先增加文章的点击数,自增或自减一个字段的值
        $addClick = $this->where('aid',$aid)->setInc('click');
        //查询文章表 tpblog_article 以及分类表
        $article = $this->alias('a')
                    ->field('aid,title,author,content,a.keywords,click,addtime,a.cid,cname')
                    ->join('tpblog_category tc','a.cid=tc.cid')
                    ->where('aid',$aid)
                    ->find();
        
        //转义文章内容
        $article['content'] = Markdown::defaultTransform($article['content']);
       
        return $article;
    }

    /**
     * 获得全部文章列表，用于文章列表形式显示，主要需要 aid,title,addtime,cid,tid
     * @return array $articleList 全部文章列表信息,不要分页信息,包含 tid
     */
    public function articleList()
    {
        //需要的信息:文章表信息 tpblog_article  标签名 tpblog_article ==> tpblog_tag
        $articleList = $this->alias('a')
                            ->join('tpblog_article_tag at','at.aid=a.aid')
                            ->where('is_show',1)
                            ->where('is_delete',0)
                            ->select();
        
        return $articleList;
    }

    /**
     * 根据 cid 获得文章列表，用于文章特定分类的显示，主要需要 aid,title,addtime,cid
     * @return array $articleCategory 特定分类的文章列表信息,不要分页信息
     */
    public function articleCategory($cid)
    {
        $articleCategory = $this->where('cid',$cid)
                                ->where('is_show',1)
                                ->select();
            
        return $articleCategory;
    }
}