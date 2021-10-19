<?php
namespace app\index\model;
use think\Model;

class Member extends Model {
    
    
    public function findByCond($field, $value) {
        return db('member')
        ->where($field, $value)
        //->field('id, phone, memberName, portrait, defaultAddress')
        ->find();
    }
    
}
?>
