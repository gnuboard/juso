<?php
ob_start();
?>

<div class="result_msg">
    <strong style="color:red;font-size:16px">SIR 주소검색 서비스가 2014년 10월 31일 종료됐습니다.</strong>
    <p>
        다른 도로명 주소검색 서비스로 변경하는 방법은 다음 공지를 참고하시기 바랍니다.<br>
        <a href="http://sir.co.kr/bbs/board.php?bo_table=co_notice&wr_id=1165" target="_blank">http://sir.co.kr/bbs/board.php?bo_table=co_notice&wr_id=1165</a>
    </p>
</div>

<?php
$contents = ob_get_contents();
ob_end_clean();

$error = '';
$juso = array();
$juso['error'] = $error;
$juso['juso'] = $contents;

echo $_GET['callback'].'('.json_encode($juso).')';
?>