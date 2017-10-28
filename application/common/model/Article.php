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


    //获得文章的分页数据
    public function getPageDate()
    {

        //查询文章的相关信息
        //需要显示的字段，在文章表(tpblog_article)中：  aid,title,author,is_original,is_show,is_top,click,addtime
        //分类：cid        在分类表(tpblog_category)中找     cname
        //标签:tid        在标签表(tpblog_article_tag)中找      tname

       $articleList = Article::where('is_show',1)->where('is_delete',0)->paginate(10,false,[
        'type'     => 'Bootstrap',
        'var_page' => 'page',
        //'path'=>'javascript:AjaxPage([PAGE]);',
        'query' => request()->param()
       ]);

       //得到 cid,cname
       $categoryList = model('Category')->all();
       //得到 aid,tid，tname
       $tagList = Db::name('article_tag')->alias('at')->field('aid,at.tid,tname')->join('tpblog_tag tt','at.tid=tt.tid')->select();
       
       //组合数据
       $data['articleList'] = $articleList;
       $data['categoryList'] = $categoryList;
       $data['tagList'] =$tagList;
       return $data;
    }

    //显示增加文章界面的可供选择的分类和标签,添加文章使用 admin
    public function getAddIndex()
    {
        //分类表：tpblog_category       分类主键id：cid      分类名称：cname
        $allCategory = Db::name('category')->field('cid,cname')->select();

        //标签表：tpblog_tag        标签主键：tid        标签名：tname
        $allTag = Db::name('tag')->field('tid,tname')->select();

        $data = [
            'allCategory' => $allCategory,
            'allTag' => $allTag
        ];

        return $data;
    }

    //增加文章功能实现
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
    public function updateArticle($method,$data)
    {
        
        if($method == 'GET'){
            //根据aid查找修改文章,给模板显示
            //dump($data);
            //exit;
            $article = Db::name('article')->where('aid',$data['aid'])->find();
            //测试找其他表
            $tnames = Db::name('article_tag')->alias('a')->field('a.tid,tname')->join('tpblog_tag t','a.tid=t.tid')->where('aid',$data['aid'])->select();
            //cid文章表中有
            //$article['cname'] = $data['cname'];
            
            $article['tnames'] = $tnames;
          // dump($article);
           //exit;
            return $article;
        }
        else if($method == 'POST'){
            //根据aid实现文章修改
              
            //分类不用考虑，修改了cid就行

            $tagData = $data['tid'];      //数组
            //先删除原来的标签，再插入新的标签
            $delTag = Db::name('article_tag')->where('aid',$data['aid'])->delete();
            for($i=0;$i<count($tagData);$i++){
                $resTag[] = Db::name('article_tag')->insert([ 'aid'=>$data['aid'],'tid'=>$tagData[$i] ]);
            }
            
            //剩下的数据是文章表的 $data
            unset($data['tid']);        
            //修改文章表
            $resArticle = Db::name('article')->where('aid',$data['aid'])->update($data);
            //修改分类表
            $resCategory = Db::name('category')->where('cid',$data['cid']);

            if($resTag && $resArticle && $resCategory){
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
        if(array_key_exists('aid',$data)){
            //aid字段存在
            //执行删除
            //删除文章表中的文章,设置 is_show=0,is_delete=1
            $resDelArticle = Article::where('aid',$data['aid'])->update(['is_show'=>0,'is_delete'=>1]);
            //删除标签表中 aid 的信息
            //$resDelTags = Db::name('article_tag')->where('aid',$data['aid'])->delete();
            if($resDelArticle){
                //删除成功
                return true;
            }else{
                return false;
            }
        }else{
            //文章 aid 不存在
            return false;
        }
    }

    public function recycleArticle($data)
    {
        if(array_key_exists('aid',$data)){
            //文章 aid存在
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
    }


    //下面是 index 前台需要的功能
    
    //得到一篇文章的具体信息
    public function articleDetail($aid)
    {
        //根据文章 aid 查询需要的数据
        //查询文章表 tpblog_article 以及分类表
        $article = DB::name('article')->alias('a')->field('aid,title,author,content,a.keywords,click,addtime,a.cid,cname')->
        join('tpblog_category tc','a.cid=tc.cid')->where('aid',$aid)->find();
        //需要的标签名称
        $tags = Db::name('article_tag')->alias('at')->join('tpblog_tag tt','at.tid=tt.tid')->where('aid',$aid)->select();
        //需要的分类信息   cname,cid
        $category = Db::name('category')->alias('c')->field('cname,c.cid')->join('tpblog_article a','c.cid=a.cid')->where('aid',$aid)->find();
        //转义文章内容
        $article['content'] = Markdown::defaultTransform($article['content']);
    
        $data['article'] = $article;
        $data['tags'] = $tags;
        $data['category'] = $category;
       
        return $data;
    }


}