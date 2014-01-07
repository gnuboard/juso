<?php
include_once('./_common.php');

// get으로 넘어온 변수 필터링
$charset = strtolower(str_replace('-', '', $_GET['charset']));
if($charset == 'euckr') {
    if($_GET['sido'])
        $sido = iconv_utf8($_GET['sido']);

    if($_GET['gugun'])
        $gugun = iconv_utf8($_GET['gugun']);
}

$sido = escape_trim(preg_replace('/[[:punct:]]/u', '', $sido));
$gugun = escape_trim(preg_replace('/[[:punct:]]/u', '', $gugun));

include_once('./'.$config['cf_api_dir'].'/zipcode.php');
?>