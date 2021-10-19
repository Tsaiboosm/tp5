<?php
namespace app\index\model;
use think\Model;


class Category extends Model {
    
    // 获取所有分类，不分页
    public function getAll() {
        return db('category')
                    ->order(['sort' => 'desc', 'id' => 'desc'])
                    ->select();
    }
    
}

?>
