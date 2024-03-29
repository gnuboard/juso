<?php
include_once('./_common.php');
include_once(G5_PATH.'/head.sub.php');
?>

<h1><?php echo $config['cf_title']; ?></h1>
<div>
    <strong style="color:red;font-size:16px">SIR 주소검색 서비스가 2014년 10월 31일 종료됐습니다.</strong>
    <p>
        다른 도로명 주소검색 서비스로 변경하는 방법은 다음 공지를 참고하시기 바랍니다.<br>
        <a href="http://sir.co.kr/bbs/board.php?bo_table=co_notice&wr_id=1165" target="_blank">http://sir.co.kr/bbs/board.php?bo_table=co_notice&wr_id=1165</a>
    </p>
</div>

<?php /*
<header>
    <div id="logo">
        <a href="<?php echo G5_URL; ?>/"><img src="<?php echo G5_URL; ?>/img/logo.png" alt="SIR"><span>주소검색</span></a>
    </div>

    <div id="search">
        <form name="fjuso" method="get" onsubmit="search_call(); return false;" autocomplete="off">
        <div class="search-select-wrp">
            <label for="sido" class="sir_sr">시도선택</label>
            <span class="search-select">
                <select name="sido" id="sido">
                    <option value="">시도 선택</option>
                </select>
            </span>
            <label for="gugun" class="sir_sr">시군구</label>
            <span class="search-select">
                <select name="gugun" id="gugun">
                    <option value="">시군구 선택</option>
                </select>
            </span>
        </div>
        <div class="search-q">
            <div class="q-info"><span></span>정확하고 빠른 검색을 위해 아래의 예시처럼 입력해 주세요.<br><br>입력예1) 강남대로37길 24-6<br>입력예2) 서초동 1362-19<br>입력예3) 서초2동 1362-19</div>
            <label for="q" class="sir_sr">검색어</label>
            <input type="text" name="q" id="q" class="q-inp">
            <input type="image" src="<?php echo G5_URL; ?>/img/btn-sch.png" alt="검색" id="q-submit">
            <span class="q-bg"></span>
        </div>
        </form>
    </div>
</header>

<div id="container">

    <div id="result"></div>

</div>

<footer>
<p><strong id="end_notice">SIR 주소검색 서비스는 2014년 10월 31일 17시까지만 운영됩니다.</strong><br><br>
    <strong>그누보드와 영카트에 제공되던 주소검색은 Daum 우편번호 서비스를 이용하도록 변경됩니다.<br>
    SIR 주소검색 서비스 종료 전까지 배포된 패치를 참고하시어 변경된 코드를 적용하여 주시기 바랍니다.</strong><br><br>
    시도 및 시군구 선택없이 도로명, 읍/면/동, 건물명 등으로 검색하실 수 있습니다.<br>
    만약 검색결과에 찾으시는 주소가 없을 때는 시도와 시군구를 선택하신 후 다시 검색해 주십시오.<br>
    (검색결과는 최대 1,000건만 표시됩니다.)
</p>
</footer>

<script>
$(function() {
    // 로딩시 시도 option 을 만든다.
    for (var i=0; i<sido.length; i++) {
        $("#sido").append($('<option></option>').val(sido[i][0]).text(sido[i][1]));
    }

    $("#sido").bind("change", function() {
        var sido = $(this).val();

        gugun_make(sido);
    });

    <?php if($is_mobile) { ?>
    var msg_alert = true;
    $("input#q").bind("focusin", function() {
        if(!msg_alert) {
            return false;
        } else {
            var msg = $(".q-info").html().replace(/<br>/g, "\n").replace("<span></span>", "");
            alert(msg);
            msg_alert = false;
        }
    });
    <?php } else { ?>
    $("input#q").bind("focusin", function() {
        $(".q-info").fadeIn(200);
    });

    $("input#q").bind("focusout", function() {
        $(".q-info").fadeOut(200);
    });
    <?php } ?>
});

function gugun_make(sido)
{
    var gugun  = document.getElementById("gugun");

    gugun.options.length = 1;
    gugun.options[0].value = "";
    gugun.options[0].text  = "- 시군구 선택 -";
    gugun.options[0].selected = true;
    if (!sido) {
        return;
    }

    sojae = sigungu[sido].split(",");
    gugun.options.length = sojae.length+1;
    for (i=0; i<sojae.length; i++) {
        gugun.options[i+1].value = (sojae[i] == "없음") ? "" : sojae[i];
        gugun.options[i+1].text = sojae[i];
    }
}

function search_call(page)
{
    $(".q-info").fadeOut(200);

    var sido = $("#sido").val();
    var gugun = $("#gugun").val();
    var q = $.trim($("#q").val());

    if(q.length < 1) {
        alert('검색어를 입력해 주십시오.');
        return false;
    }

    if(!page)
        page = 1;

    $.ajax({
        type: "GET",
        url: "<?php echo G5_URL; ?>/search.php",
        async: false,
        dataType: "jsonp",
        jsonp: "callback",
        data: {
            "sido": sido,
            "gugun": gugun,
            "page": page,
            "q": q,
            "link": "false"
        },
        success:function(data) {
            $("#result").empty();

            if(data.error) {
                alert(data.error);
                return false;
            }

            $("#result").html(data.juso);
        }
    });
}

var sido = new Array(
    Array('서울특별시'     , '서울'),
    Array('부산광역시'     , '부산'),
    Array('대구광역시'     , '대구'),
    Array('인천광역시'     , '인천'),
    Array('광주광역시'     , '광주'),
    Array('대전광역시'     , '대전'),
    Array('울산광역시'     , '울산'),
    Array('세종특별자치시' , '세종'),
    Array('경기도'         , '경기'),
    Array('강원도'         , '강원'),
    Array('충청북도'       , '충북'),
    Array('충청남도'       , '충남'),
    Array('전라북도'       , '전북'),
    Array('전라남도'       , '전남'),
    Array('경상북도'       , '경북'),
    Array('경상남도'       , '경남'),
    Array('제주특별자치도' , '제주')
);

// 시군구 정보
var sigungu = new Array();

sigungu['서울특별시'] = '강남구,강동구,강북구,강서구,관악구,광진구,구로구,금천구,노원구,도봉구,동대문구,동작구,마포구,서대문구,서초구,성동구,성북구,송파구,양천구,영등포구,용산구,은평구,종로구,중구,중랑구';
sigungu['부산광역시'] = '강서구,금정구,기장군,남구,동구,동래구,부산진구,북구,사상구,사하구,서구,수영구,연제구,영도구,중구,해운대구';
sigungu['대구광역시'] = '남구,달서구,달성군,동구,북구,서구,수성구,중구';
sigungu['인천광역시'] = '강화군,계양구,남구,남동구,동구,부평구,서구,연수구,옹진군,중구';
sigungu['광주광역시'] = '광산구,남구,동구,북구,서구';
sigungu['대전광역시'] = '대덕구,동구,서구,유성구,중구';
sigungu['울산광역시'] = '남구,동구,북구,울주군,중구';
sigungu['세종특별자치시'] = '없음';
sigungu['경기도'] = '가평군,고양시 덕양구,고양시 일산동구,고양시 일산서구,과천시,광명시,광주시,구리시,군포시,김포시,남양주시,동두천시,부천시 소사구,부천시 오정구,부천시 원미구,성남시 분당구,성남시 수정구,성남시 중원구,수원시 권선구,수원시 영통구,수원시 장안구,수원시 팔달구,시흥시,안산시 단원구,안산시 상록구,안성시,안양시 동안구,안양시 만안구,양주시,양평군,여주시,연천군,오산시,용인시 기흥구,용인시 수지구,용인시 처인구,의왕시,의정부시,이천시,파주시,평택시,포천시,하남시,화성시';
sigungu['강원도'] = '강릉시,고성군,동해시,삼척시,속초시,양구군,양양군,영월군,원주시,인제군,정선군,철원군,춘천시,태백시,평창군,홍천군,화천군,횡성군';
sigungu['충청북도'] = '괴산군,단양군,보은군,영동군,옥천군,음성군,제천시,증평군,진천군,청원군,청주시 상당구,청주시 흥덕구,충주시';
sigungu['충청남도'] = '계룡시,공주시,금산군,논산시,당진시,보령시,부여군,서산시,서천군,아산시,예산군,천안시 동남구,천안시 서북구,청양군,태안군,홍성군';
sigungu['전라북도'] = '전주시 완산구,전주시 덕진구,군산시,익산시,정읍시,남원시,김제시,완주군,진안군,무주군,장수군,임실군,순창군,고창군,부안군';
sigungu['전라남도'] = '강진군,고흥군,곡성군,광양시,구례군,나주시,담양군,목포시,무안군,보성군,순천시,신안군,여수시,영광군,영암군,완도군,장성군,장흥군,진도군,함평군,해남군,화순군';
sigungu['경상북도'] = '경산시,경주시,고령군,구미시,군위군,김천시,문경시,봉화군,상주시,성주군,안동시,영덕군,영양군,영주시,영천시,예천군,울릉군,울진군,의성군,청도군,청송군,칠곡군,포항시 남구,포항시 북구';
sigungu['경상남도'] = '거제시,거창군,고성군,김해시,남해군,밀양시,사천시,산청군,양산시,의령군,진주시,창녕군,창원시 마산합포구,창원시 마산회원구,창원시 성산구,창원시 의창구,창원시 진해구,통영시,하동군,함안군,함양군,합천군';
sigungu['제주특별자치도'] = '서귀포시,제주시';
</script>

<?php
*/
include_once(G5_PATH.'/tail.sub.php');
?>