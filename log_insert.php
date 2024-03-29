<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$tmp = array();
if($sido)
    $tmp[] = $sido;
if($gugun)
    $tmp[] = $gugun;
if($q)
    $tmp[] = $q;

$word = implode(',', $tmp);
$exec_time = $res['time'];

// 검색 로그 기록
$sql = " insert into {$config['search_log_table']}
            set sl_type      = '$sl_type',
                sl_host      = '{$g5['remote_host']}',
                sl_date      = '".G5_TIME_YMD."',
                sl_time      = '".G5_TIME_HIS."',
                sl_word      = '$word',
                sl_ip        = '{$_SERVER['REMOTE_ADDR']}',
                sl_referer   = '{$_SERVER['HTTP_REFERER']}',
                sl_exec_time = '$exec_time' ";
sql_query($sql, false);

// 검색 카운트 기록
$sql = " select sn
            from {$config['search_count_table']}
            where sc_host = '{$g5['remote_host']}'
              and sc_date = '".G5_TIME_YMD."' ";
$row = sql_fetch($sql);

if($row['sn']) {
    $sql = " update {$config['search_count_table']}
                set sc_count = sc_count + 1
                where sc_host = '{$g5['remote_host']}'
                  and sc_date = '".G5_TIME_YMD."' ";
    sql_query($sql, false);
} else {
    $sql = " insert into {$config['search_count_table']}
                set sc_host     = '{$g5['remote_host']}',
                    sc_date     = '".G5_TIME_YMD."',
                    sc_count    = '1' ";
    sql_query($sql, false);
}

unset($word);
unset($row);
?>