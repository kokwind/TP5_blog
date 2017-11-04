<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    // 路由参数name为可选
    'hello/[:name]' => 'index/hello',
     // 添加路由规则 路由到 index控制器的hello操作方法
     'index/[:name]' => 'index/index/index',
     'article/[:aid]' => 'index/index/article',
     'archive/[:year]' => 'index/index/archive',
     'category/[:cid]' => 'index/index/category',
     'categoryList/[:cid]' => 'index/index/categoryList',
     'tag/[:tid]' => 'index/index/tag',
     'comment/[:cmtid]' => 'index/index/comment',
];
