<?php

function get_bytes($str)
{
    $unit=strtoupper(substr($str,-1));
    $num=substr($str,0,-1);
    $arr=array(
        '0'=>0,
        'K'=>1,
        'M'=>2,
        'G'=>3
    ); //注意单位不能超出G
    return $num*pow(1024,$arr[$unit]);  
}

/*
 * $save_path:文件上传到服务器的位置(文件保存的路径)
 * $max_size:程序作者设定的最大的上传文件的大小(该值小于PHP配置中的值)
 * $type:是允许用户上传的文件格式
 */
function upload($save_path='uploaded',$max_size='2M',$input_name='myfile',$type=array('jpg','jpeg','gif','png'))
{
    $phpini=ini_get('upload_max_filesize'); //获取php配置文件中的属性值 8m
    //同一转换成以byte为单位
    $phpini_bytes=get_bytes($phpini);
    $custom_bytes=get_bytes($max_size); 
    
    if($custom_bytes>$phpini_bytes)
    {
        $res['error']='传入的参数 $max_size(最大上传大小) 超出PHP配置文件中的值('.$phpini.')';
        $res['result']=false;
        return $res;
    }
    
    $errors=array(
        0=>'文件上传成功',
        1=>'上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
        2=>'上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
        3=>'文件只有部分被上传',
        4=>'没有文件被上传',
        6=>'找不到临时文件夹',
        7=>'文件写入失败'
    ); //注意没有5！！！
    
    /*if(empty($_FILES))
    {
        $res['error']='$_FILES为空，尚未开始上传';
        $res['result']=false;
        return $res;
    }*/
    
    if($_FILES[$input_name]['error']!=0)
    {
        $res['error']=$errors[$_FILES[$input_name]['error']];
        $res['result']=false;
        return $res;
    }
    
    if(!is_uploaded_file($_FILES[$input_name]['tmp_name'])) //文件的临时路径
    {
        $res['error']='文件不是通过 HTTP POST 上传的';
        $res['result']=false;
        return $res;
    }
    
    if($_FILES[$input_name]['size']>$custom_bytes)
    {
        $res['error']='上传文件的大小 超过 程序作者 设定的上限('.$max_size.')';
        $res['result']=false;
        return $res;
    }
    
    $file_name=pathinfo($_FILES[$input_name]['name']); //一个数组，分割的路径
    if(!isset($file_name['extension']))
        $file_name['extension']='';
    if(!in_array($file_name['extension'], $type)) //如果用户上传的文件的格式不在允许的格式范围之内
    {
        $res['error']='上传文件的后缀名必须是：'.implode(',',$type).' 其中的一个';
        $res['result']=false;
        return $res;
    }
    
    if(!file_exists($save_path)) //如果该路径不存在
    {
        if(!mkdir($save_path,0777,true))
        {
            $res['error']='所传路径 $save_path 不存在，尝试创建失败，请检查权限';
            $res['result']=false;
            return $res;
        }     
    }
    
    $new_filename=str_replace('.','',uniqid(mt_rand(1000,9999),true));
    if($file_name['extension']!='')
        $new_filename.=".{$file_name['extension']}";
    $save_path=rtrim($save_path,'/')."/{$new_filename}";
    //var_dump($save_path);
    
    if(!move_uploaded_file($_FILES[$input_name]['tmp_name'],$save_path))
    {
        $res['error']='临时文件移动失败，请检查权限';
        $res['result']=false;
        return $res;
    }

    $res['result']=true;
    $res['filename']=$new_filename;
    $res['save_path']=$save_path;
    return $res;
}


?>


