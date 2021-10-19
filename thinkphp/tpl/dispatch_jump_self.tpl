{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- 不设置的话，手机端不会进行响应式布局 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>跳转中...</title>

    <!-- 引入Bootstrap核心样式文件 -->
    <link rel="stylesheet" href="__lib__/bootstrap4/css/bootstrap.css">
    <style type="text/css">

        .box {
                height: 600px;
          
            margin-top: 100px;
            box-shadow: 0 0 10px #ddd;
        }

        .msg {
            line-height: 48px;
            margin-left: 12px;
        }

        .run {
            width: 70%;
            display: block;
            margin: 0 auto;
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="row" style="
    width: 900px;
    height: 700px;
    margin: auto;
">
            <div class="col-md-6 offset-md-3 col-12 text-center box">
                <div class="d-flex justify-content-center px-5 mt-4 mb-2">
                	<?php switch ($code) {?>
			            <?php case 1:?>
			            	 <img src="__STATIC__/index/img/success.svg" width="48" alt="">
			            <?php break;?>
			            <?php case 0:?>
			            	 <img src="__STATIC__/index/img/error.svg" width="48" alt="">
			            <?php break;?>
			        <?php } ?>
                   	<h4 class="msg"> <?php echo(strip_tags($msg));?> </h4>
                </div>
                <p class="detail text-muted"></p>
                <h5 class="text-muted">
                    <b id="wait"><?php echo($wait);?></b> s 后页面自动
                    <a id="href" href="<?php echo($url);?>" class="text-decoration-none">跳转</a>
                </h5>
                <img src="__STATIC__/index/img/run.gif" alt="" class="run my-3">
            </div>
        </div>
    </div>

    <!-- js的顺序不能乱 -->
    <script src="__lib__/jquery/jquery.min.js"></script>
    <!-- bootstrap.min.js依赖jquery -->
    <script src="__lib__/bootstrap4/js/bootstrap.min.js"></script>
    
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
</body>
</html>
