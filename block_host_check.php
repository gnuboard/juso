<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!trim($g5['remote_host']))
    die_jsonp('Connection Failure.');

if(trim($config['blocked_host'])) {
    $block = array_map('trim', explode(',', $config['blocked_host']));

    foreach($block as $hostname) {
        $hostname = str_replace('.', '\.', $hostname);

        if(preg_match('/'.$hostname.'/i', $g5['remote_host'])) {
            die_jsonp('Connection Failure..');
        }
    }
}
?>