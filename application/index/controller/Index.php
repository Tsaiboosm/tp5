<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Goods as GoodsModel;
use app\index\model\Cart;
use Util\data\Sysdb;
 

class Index extends Controller
{
	public function __construct(){
        // 调用父类方法防止子类清除前面调用重新调用
		parent::__construct();
		$this->db = new Sysdb;
	}
	public function index()
    {
        // 幻灯片
    	$slide_list = $this->db->table('slide')->where(array('type'=>1))->lists();
		//商品头部
		$slide_list_to =$this->db->table('goods')->where(array('sop'=>1))->pages(3);
		$slide_list_do =$this->db->table('goods')->where(array('sop'=>0))->pages(6);
		$slide_list_is =$this->db->table('goods')->where(array('sop'=>2))->pages(6);
		


        // 导航标签
		$this->assign('slide_to_do',$slide_list_do['lists']);
		$this->assign('slide_to_is',$slide_list_is['lists']);
        $this->assign('slide_to',$slide_list_to['lists']);
    	$this->assign('data',$slide_list);
        return $this->fetch();
    }
	
    public function proDer(){
        $slide_list = $this->db->table('slide')->where(array('type'=>1))->lists();
        $slide_list_to =$this->db->table('goods')->where(array('sop'=>3))->pages(4);
        // dump($slide_list_to);
        $this->assign('slide_to',$slide_list_to['lists']);
        $goodsId = input('get.id');
        //var_dump($goodsId);exit();
        $goodsModel = new GoodsModel();
        $proDer = $goodsModel->findById($goodsId);
      
           //var_dump($conds);exit();
           $cartItemCount = 0;
           if (session('member')) {
               $cartModel = new Cart();
               $cartItemCount = $cartModel->goodsItemCount(session('member.id'));
           }
        $this->assign('proDer', $proDer);
        $this->assign('cartItemCount', $cartItemCount);
        return $this->fetch();
    }

    
}
