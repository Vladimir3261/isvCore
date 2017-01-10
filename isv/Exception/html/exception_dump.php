<?php
/**
 * Powered by ISVTeam
 * @var $code int
 * @var $message string
 * @var $array array
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Error</title>
    </head>
    <style>
        .block-top{color: #e57373;font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 36px;cursor: default;}
        .block-top span a{color: #337ab7;text-decoration: none;transition: 0.4s;}
        .block-top span a:hover{color: #23527c;text-decoration: underline;transition: 0.4s;}
        .stack{font-family: cursive;color: #444444;font-size: 20px;margin-bottom: 15px;}
        .block-fid{background-color: #dff0d8;line-height: 1.42857143;vertical-align: top;border-top: 1px solid #ddd;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;}
        .block-fid span{padding: 5px 5px;display: inline-block;}
        .boot-block{background-color: #fcf8e3;border-bottom: solid 1px #ddd;margin-bottom: 10px;}
        .boot-block span{background-color: #fcf8e3;}
        .block-fid .tr1{width: 2%;}
        .block-fid .tr2{width: 45%;}
        .block-fid .tr3{width: 10%;}
        .block-fid .tr4{width: 20%;}
        .block-fid .tr5{float: right;width: 15%;}
        .block-fid .tr6{background-color: #d9edf7;}
        .block-centr{width: 80%;margin: 0 auto;cursor: default;}
        @media (max-width: 1350px){.block-centr {width: 100%;}}
    </style>
<body>
<section>
    <div class="block-centr">

        <div class="block-top">
					<span><a href="#"><?=$code?></a></span><?=$message?>
        </div>

        <div class="stack">Stack Trace...</div>

        <div class="block-fid">
            <span class="tr1">#</span>
            <span class="tr2">file</span>
            <span class="tr3">line</span>
            <span class="tr4">function</span>
            <span class="tr5">class</span>
        </div>
        <?php $i=1; foreach($array as $k => $v){ ?>
            <div class="block-fid boot-block">
                <span class="tr1 tr6"><?=$i?></span>
                <span class="tr2"><?=$v['file']?></span>
                <span class="tr3"><?=$v['line']?></span>
                <span class="tr4"><?=$v['function']?></span>
                <span class="tr5"><?=$v['class']?></span>
            </div>
        <?php $i++; } ?>
    </div>
</section>
</body>
</html>