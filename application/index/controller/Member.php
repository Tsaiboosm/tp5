<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Member as MemberModel;


class Member extends Controller {
    
    public function checkCaptcha() {
        return captcha_check(input('post.captcha'));
    }
    
    public function isPhoneExist() {
        $model = new MemberModel();
        return empty($model->findByCond('phone', input('post.mobile_phone')));
    }
    public function isPhoneemail() {
        $model = new MemberModel();
        return empty($model->findByCond('email', input('post.mobile_email')));
    }
    
    public function isMemberNameExist() {
        $model = new MemberModel();
        return empty($model->findByCond('memberName', input('post.username')));
    }
    public function isportrait() {
        $model = new MemberModel();
        return empty($model->findByCond('portrait', input('post.portrait')));
    }
    
    
    public function register() {
        if (request()->isPost()) {
            //var_dump(input('post.'));            
            $memberName = input('post.username');
            $phone = input('post.mobile_phone');
            $phone = input('post.mobile_phone');
            $email = input('post.mobile_email');
            $password = input('post.password');
           
            
            $model = new MemberModel();
            $model->phone = $phone;
            $model->email = $email;
            $model->password = md5($password);
            $model->memberName = $memberName;
            $model->time = date('Y-m-d H:i:s');;
            if ($model->save()) {
                //session('member', $model->findById($model->id));
                //var_dump(session('member'));
                $this->success('注册成功，请手动登录', 'login');
            } else {
                $this->error('注册失败' . $model->getError());
            }
        } else {
            return view();
        }
    }
    
    public function login() {
        //var_dump(session('member'));exit();
        
        if (!empty(session('member'))) {
            $this->error('请勿重复登录', 'index/index');
        }
        
        if (request()->isPost()) {
            $phone = input('post.mobile_phone');
            $password = input('post.password');
            
            $model = new MemberModel();
            $member = $model->findByCond('phone', $phone);
           dump($model);
            if (empty($member)) {
                // 之所以要重定向，是为了刷新验证码
                $this->error('手机号尚未注册', 'member/login');
            } else{
                if ($member['password'] != md5($password)) {
                    $this->error('密码错误', 'member/login');
                } else {
                    unset($member['password']);
                    session('member', $member);
                // var_dump(session('member', $member));exit();
                    $this->success('登录成功', 'index/index');
                }
            }
        } else {
            return view();
        }
    }
    
    public function logout() { 
        if (empty(session('member'))) {
            $this->error('请先登录');
        }
        
        session('member', null);
        $this->success('退出成功', 'index/index');
    }
    
  
}

?>
