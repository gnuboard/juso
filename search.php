<?php
/*
include_once('./_common.php');

// 접근금지 host 체크
require(G5_PATH.'/block_host_check.php');

// get으로 넘어온 변수 필터링
$charset = strtolower(str_replace('-', '', $_GET['charset']));
if($charset == 'euckr') {
    if($_GET['sido'])
        $sido = iconv_utf8($_GET['sido']);

    if($_GET['gugun'])
        $gugun = iconv_utf8($_GET['gugun']);

    $q = iconv_utf8($_GET['q']);
}

$sido = escape_trim(preg_replace('/[[:punct:]]/u', '', $sido));
$gugun = escape_trim(preg_replace('/[[:punct:]]/u', '', $gugun));
$q = escape_trim(preg_replace("/[#\&\+%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/u", "", strip_tags($q)));

include_once('./'.$config['cf_api_dir'].'/search.php');
*/

include_once('./api/juso_end.php');
?>