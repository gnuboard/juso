<?php
include_once('./_common.php');

if(!$q) {
    $juso['error'] = '검색어를 입력해 주십시오.';

    echo $_GET['callback'].'('.json_encode($juso).')';
    exit;
}

// spninx api load
require ( G5_LIB_PATH.'/sphinx/sphinxapi.php' );

$cl = new SphinxClient ();

$host = $config['cf_sphinx_host'];
$port = $config['cf_sphinx_port'];
$index = $config['cf_sphinx_index'];
$rows = $config['cf_page_rows'];

$cl->SetServer ( $host, $port );
$cl->SetConnectTimeout ( 1 );
$cl->SetArrayResult ( true );
$cl->SetWeights ( array ( 100, 1 ) );
$cl->SetMatchMode ( SPH_MATCH_EXTENDED );
//$cl->setSortMode( SPH_SORT_ATTR_ASC, 'sn' );

if ($page == '') $page = 1;
$offset = ($page - 1) * $rows;
$cl->SetLimits ( $offset, $rows, $config['cf_max_rows']);

$query = '';

if($sido)
    $query .= ' @sido "'.$sido.'" ';

if($gugun)
    $query .= ' @gugun "'.$gugun.'" ';

$sword = explode(' ', trim($q));

$field = '(doro,jibeon)';

foreach($sword as $val) {
    $word = trim($val);
    if(!$word)
        continue;

    $query .= ' @'.$field.' "'.$word.'*" ';
}

$res = $cl->Query ( $query, $index );

$error = '';
if ($res === false) {
    $error = '검색실패 : ' . $cl->GetLastError();
} else {
    $total_count = $res['total'];
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산

    $count = count($res['matches']);

    if($_GET['link'] != 'false')
        $link = true;
    else
        $link = false;

    ob_start();
?>

<div class="result_msg">검색결과 <b><?php echo number_format($total_count); ?></b></div>

<?php
    for($i=0; $i<$count; $i++) {
        $data = $res['matches'][$i]['attrs'];

        $eupmyeon = false;
        $zipcode = preg_replace('/([0-9]{3})([0-9]{3})/', '\\1-\\2', $data['zipcode']);
        $zip = explode('-', $zipcode);
        $zip1 = $zip[0];
        $zip2 = $zip[1];
        $addr1 = $data['sido'].' '.$data['gugun'];
        if($data['beopname'] && preg_match('/(읍|면)$/', $data['beopname'])) {
            $addr1 .= ' '.$data['beopname'];
            $eupmyeon = true;
        }
        $addr1 .= ' '.$data['doroname'];
        if($data['jiha'])
            $jiha = ' 지하';
        else
            $jiha = ' ';
        $addr1 .= $jiha.$data['geonbon'];
        if($data['geonbu'])
            $addr1 .= '-'.$data['geonbu'];

        $addr3 = '';
        if($data['beopname'] && !$eupmyeon)
            $addr3 = ' ('.$data['beopname'];
        if($data['geonname'] || $data['geonsangse']) {
            if($addr3)
                $addr3 .= ', ';
            else
                $addr3 = ' (';
        }
        if($data['geonname']) {
            $addr3 .= $data['geonname'];
        } else {
            if($data['geonsangse'])
                $addr3 .= $data['geonsangse'];
            else {
                if($data['daryang'] && !$data['geonname2']) {
                    if($addr3)
                        $addr3 .= ', '.$data['daryang'];
                    else
                        $addr3 .= ' ('.$data['daryang'];
                } else if(!$data['daryang'] && $data['geonname2']) {
                    if($addr3)
                        $addr3 .= ', '.$data['geonname2'];
                    else
                        $addr3 .= ' ('.$data['geonname2'];
                }
            }
        }
        if($addr3)
            $addr3 .= ')';

        $addr_ji = $data['sido'].' '.$data['gugun'];
        if($data['beopname'])
            $addr_ji .= ' '.$data['beopname'];
        if($data['ri'])
            $addr_ji .= ' '.$data['ri'];
        if($data['san'])
            $san = ' 산';
        else
            $san = ' ';
        $addr_ji .= $san.$data['jibon'];
        if($data['jibu'])
            $addr_ji .= '-'.$data['jibu'];

        if($data['geonname']) {
            $addr_ji .= ' '.$data['geonname'];
        } else {
            if($data['geonsangse'])
                $addr_ji .= ' '.$data['geonsangse'];
        }

        $addr3 = htmlentities($addr3, ENT_QUOTES, "UTF-8");
        $addr_ji = htmlentities($addr_ji, ENT_QUOTES, "UTF-8");

        if($i == 0)
            echo '<ul>'.PHP_EOL;
        echo '<li>'.PHP_EOL;
        echo '<span></span>';
        if($link)
            echo "<a href='#' onclick='put_data(\"".$zip1."\", \"".$zip2."\", \"".trim($addr1)."\", \"".trim($addr3)."\", \"".trim($addr_ji)."\"); return false;'>";
        echo '<strong>'.$zipcode.'</strong>';
        echo ' '.$addr1;
        echo $addr3;
        if($link)
            echo '</a>';
        echo '<div>(지번주소) '.$addr_ji.'</div>';
        echo '</li>'.PHP_EOL;
    }

    if($i > 0)
        echo '</ul>';
    else
        echo '<div class="result_msg result_fail">검색결과가 없습니다.</div>';

    //echo '<p>실행시간 : '.$res['time'].'</p>';

    $pagelist = get_paging($is_mobile ? $config['cf_mobile_list_pages'] : $config['cf_list_pages'], $page, $total_page);
    echo $pagelist;

    $contents = ob_get_contents();
    ob_end_clean();
}

// 방문자수의 접속을 남김
$sl_type = 'address';
include_once(G5_PATH.'/log_insert.php');

$jusu = array();
$juso['error'] = $error;
$juso['juso'] = $contents;

echo $_GET['callback'].'('.json_encode($juso).')';
?>