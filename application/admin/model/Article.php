<?php

namespace app\admin\model;
use think\Model;
use think\Db;       //使用数据库
use think\Paginator;     //使用分页
use think\Validate;     //使用tp5的验证器

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
        //return $validate->check($checkData);
        if($validate->check($checkData)){
            return $validate->check($checkData);
            //dump($validate->check($checkData));     //成功是   bool(true)
        }else{
            return $validate->getError();
            //echo $validate->getError();         //不成功是提示字符串
        }
    
    }
/*
    // 自动验证
    protected $_validate=array(
        array('tid','require','必须选择栏目'),
        array('title','require','文章标题必填'),
        array('author','require','作者必填'),
        array('content','require','文章内容必填'),
        );
*/

    //设置自动完成的字段，支持键值对数组和索引数组
    //新增和更新时都会使用
    // 自动完成
    protected $auto = [
        array('click',0),
        array('is_delete',0),
        array('addtime','time()',1,'function'),
        array('description','getDescription',3,'callback'),
        array('keywords','comma2coa',3,'callback')
        ];

    // 获得描述；供自动完成调用
    protected function getDescription($description){
        if(!empty($description)){
            return $description;
        }else{
            $data=I('post.content');
            $des=htmlspecialchars_decode($data);
            $des=re_substr(strip_tags($des),0,200,false);
            return $des;
        }
    }

    // 顿号转换为英文逗号
    protected function comma2coa($keywords){
        return str_replace('、', ',', $keywords);
    }


    /**
     * 获得文章分页数据
     * @param strind $cid 分类id 'all'为全部分类
     * @param strind $tid 标签id 'all'为全部标签
     * @param strind $is_show   是否显示 1为显示 0为显示
     * @param strind $is_delete 状态 1为删除 0为正常
     * @param strind $limit 分页条数
     * @return array $data 分页样式 和 分页数据
     */
    public function getPageDate()
    {

        //查询文章的相关信息
        //需要显示的字段，在文章表(tpblog_article)中：  aid,title,author,is_original,is_show,is_top,click,addtime
        //分类：cid        在分类表(tpblog_category)中找     cname
        //标签:tid        在标签表(tpblog_article_tag)中找      tname

        //查出所有的数据
        //$articleList = Db::name('article')->field('aid,title,author,is_original,is_show,is_top,click,addtime')->select();
        $total = Db::name('article')->count();
        // 查询 is_show=1 的用户数据 并且每页显示10条数据 总记录数为 $total
        $list = Db::name('article')->alias('a')->field('a.aid,title,author,is_original,is_show,is_top,click,addtime,cname,a.cid,tname')->where('is_show',1)->join('tpblog_category c','a.cid=c.cid')->join('tpblog_article_tag at','a.aid=at.aid')->join('tpblog_tag tt','at.tid=tt.tid')->paginate(10,$total);
        
        //试着查询其他model的数据表
        
        // echo article::getLastSql();
       
        // 获取分页显示
        $page = $list->render();
        
        $data['articleList'] = $list;
        $data['page'] = $page;

        return $data;
    }

    //显示增加文章界面的可供选择的分类和标签
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
    public function addArticle($data)
    {
        //实现增加文章
        //先从数组中移除tid字段，此字段属于表 tpblog_article_tag
        $articleTagDate['tid'] = $data['tid'];     //为数组
        if($data['tid']){
            unset($data['tid']);
        }
        
        //插入article表
        $this->auto=[];
        $resArticle = Db::name('article')->insert($data);
        $articleId = Db::name('article')->getLastInsID();       //返回新增数据的自增主键
        //echo article::getLastSql();

   /*     //插入tpblog_article_tag表的数据
        $articleTagDate['aid'] = $articleId;
        dump($articleTagDate);
        $resTag = Db::name('article_tag')->insert($articleTagDate);
        echo article::getLastSql();
        exit;
    */
        if($resArticle){
            return true;
        }else{
            return false;
        }
        
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
            //删除文章表中的文章
            $resDelArticle = Db::name('article')->where('aid',$data['aid'])->delete();
            //删除标签表中 aid 的信息
            $resDelTags = Db::name('article_tag')->where('aid',$data['aid'])->delete();
            if($resDelArticle && $resDelTags){
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
    
}