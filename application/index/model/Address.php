<?php
namespace app\index\model;
use think\Model;

class Address extends Model {
  
    public function CartItemCount($memberId) {
        return Address::where([
            'memberId' => $memberId
        ])->count();
        
    }
}
?>