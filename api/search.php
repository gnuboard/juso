<?php
include_once('./_common.php');

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

//$field = '(doroname,geonbon,geonbu,geonname,geonsangse,beopname,eupmyeon,ri,jibon,jibu)';
$field = '(doro,jibeon)';
$query .= ' @'.$field.' "'.$q.'" ';

$res = $cl->Query ( $query, $index );

$error = '';
if ($res === false) {
    $error = '검색실패 : ' . $cl->GetLastError();
} else {
    $total_count = $res['total'];
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산

    $count = count($res['matches']);

    ob_start();
?>

<div class="result_msg">검색결과 : <?php echo number_format($total_count); ?>건</div>

<?php
    for($i=0; $i<$count; $i++) {
        $data = $res['matches'][$i]['attrs'];

        $zipcode = preg_replace('/([0-9]{3})([0-9]{3})/', '\\1-\\2', $data['zipcode']);
        $zip = explode('-', $zipcode);
        $zip1 = $zip[0];
        $zip2 = $zip[1];
        $addr1 = $data['sido'].' '.$data['gugun'].' '.$data['doroname'];
        $addr1 .= ' '.$data['geonbon'];
        if($data['geonbu'])
            $addr1 .= '-'.$data['geonbu'];
        if($data['beopname'])
            $addr1 .= ' ('.$data['beopname'];
        if($data['geonname'] || $data['geonsangse'])
            $addr1 .= ',';
        if($data['geonname']) {
            $addr1 .= ' '.$data['geonname'];
        } else {
            if($data['geonsangse'])
                $addr1 .= ' '.$data['geonsangse'];
        }
        $addr1 .= ')';

        $addr_ji = $data['sido'].' '.$data['gugun'];
        if($data['beopname'])
            $addr_ji .= ' '.$data['beopname'];
        if($data['eupmyeon'])
            $addr_ji .= ' '.$data['eupmyeon'];
        $addr_ji .=' '.$data['jibon'];
        if($data['jibu'])
            $addr_ji .= '-'.$data['jibu'];

        if($data['geonname']) {
            $addr_ji .= ' '.$data['geonname'];
        } else {
            if($data['geonsangse'])
                $addr_ji .= ' '.$data['geonsangse'];
        }

        if($i == 0)
            echo '<ul>'.PHP_EOL;
        echo '<li>'.PHP_EOL;
        if($link != 'false')
            echo '<a href="#" onclick="put_data(\''.$zip1.'\', \''.$zip2.'\', \''.trim($addr1).'\', \''.trim($addr2).'\', \''.trim($addr_ji).'\'); return false;">';
        echo $zipcode;
        echo ' '.$addr1;
        echo ' '.$addr2;
        if($link != 'false')
            echo '</a>';
        echo '<div>(지번주소) '.$addr_ji.'</div>';
        echo '</li>'.PHP_EOL;
    }

    if($i > 0)
        echo '</ul>';
    else
        echo '<div class="result_msg result_fail">검색결과가 없습니다.</div>';

    //echo '<p>실행시간 : '.$res['time'].'</p>';

    $pagelist = get_paging($config['cf_list_pages'], $page, $total_page);
    echo $pagelist;

    $contents = ob_get_contents();
    ob_end_clean();
}

$jusu = array();
$juso['error'] = $error;
$juso['juso'] = $contents;

echo $_GET['callback'].'('.json_encode($juso).')';
?>