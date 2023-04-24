<?php
define("DOC_ROOT", $_SERVER["DOCUMENT_ROOT"] . "/");
define("URL_DB", DOC_ROOT . "project/DB/db_conn.php");

include_once(URL_DB);

// DB 연결 객체 가져오기
db_conn($db_conn);
if (!$db_conn) {
    // DB 연결 실패 시, 예외 발생
    throw new Exception("DB 연결에 실패했습니다.");
}




$page_data_count = 5;

// 전체 페이지 수 계산
$total_page_count = ceil($total_data_count / $page_data_count);

// 해당 페이지에 보여줄 데이터 구하기
$current_page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page_no = max($current_page_no, 1); // 페이지 번호는 1 이상이어야 함
$current_page_no = min($current_page_no, $total_page_count); // 페이지 번호는 전체 페이지 수 이하이어야 함

// 페이지의 시작 데이터 인덱스
$start_data_index = ($current_page_no - 1) * $page_data_count;

// 함수 호출
$list_page_fnc = list_page($start_data_index, $page_data_count);
// var_dump($list_page_fnc);


$http_method = $_SERVER["REQUEST_METHOD"];
// $db_conn = get_db_conn();

if ($http_method === "POST") {
    $arr_post = $_POST;
    $test1 = isset($arr_post["is_com"]) ? $arr_post["is_com"] : 0;
    $test2 = isset($arr_post["task_no"]) ? $arr_post["task_no"] : 0;
    $is_com_old = array(
        "is_com" => $test1,
        "task_no" => $test2
    );
    $is_com = update_is_com($is_com_old);
}


// HTML 페이지에 표시할 코드 작성
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>미라클 모닝</title>
    <!-- favicon -->
    <link rel="icon" href="./SOURCE/sun2.png">
    <!-- css 링크 -->
    <link rel="stylesheet" href="./css/main.css">
</head>

<body>
    <div class="sidebox">
        <div class="top">
            <h1>미라클 모닝 <span>실천방법</span></h1>
        </div>
        <div class="bottom">
            <div class="update">
                <p>1) <span>침묵의 시간</span> 갖기 - 삶의 목적 찾기<br>
                    2) <span>확신</span>과 <span>다짐</span>의 말하기 - 잠재의식 프로그래밍<br>
                    3) <span>직관</span>의 <span>시각화</span> - 이상적인 하루와 나의 모습<br>
                    4) <span>아침 운동</span>하기 - 가장 좋아하고 몸 상태에 맞는<br>
                    5) <span>독서</span>하기 - 목적 독서<br>
                    6) <span>기록</span>하기 - 아침 일기</p>
            </div>
        </div>
    </div>
    <div class="contianer">
        <div class="title top">
            <h1><img src="./source/sun.png"> MIRACLE MORNING <img src="./source/sun.png"></h1>
        </div>
        <div class="bottom">
            <div class="listTable">
                <table>
                    <thead>
                        <tr>
                            <th>글번호</th>
                            <th>날짜</th>
                            <th>카테고리</th>
                            <th>제목</th>
                            <th>수행여부</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list_page_fnc as $data) { ?>
                            <tr <?php echo $data['is_com'] == '1' ? 'class="completed"' : '' ?>>

                                <td><a href="/project/detail.php?task_no=<?php echo $data["task_no"] ?>"><?php echo $data['task_no'] ?></a></td>
                                <td><a href="/project/detail.php?task_no=<?php echo $data["task_no"] ?>"><?php echo $data['task_date'] ?></a></td>
                                <td><a href="/project/detail.php?task_no=<?php echo $data["task_no"] ?>"><?php echo $data['category_name'] ?></a></td>
                                <td><a href="/project/detail.php?task_no=<?php echo $data["task_no"] ?>"><?php echo $data['task_title'] ?></a></td>

                                <td>
                                    <form action="" method="POST">
                                        <input type="hidden" name="task_no" value="<?php echo $data['task_no'] ?>">
                                        <?php if ($data['is_com'] == '1') { ?>
                                            <button class="checkbox_btn_com"></button>
                                        <?php } else { ?>
                                            <button class="checkbox_btn"></button>
                                        <?php } ?>
                                        <input type="hidden" name="is_com" value="<?php echo $data['is_com'] == '1' ? '0' : '1' ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- 페이지네이션 출력 -->
            <ul class="paging-wrap">
                <li><a href="<?php echo $_SERVER['PHP_SELF'] . '?page=1' ?>">◀◀</a></li>
                <?php
                $start_page = floor(($current_page_no - 1) / 5) * 5 + 1; // 시작 페이지 계산
                $end_page = $start_page + 4; // 끝 페이지 계산
                $end_page = min($end_page, $total_page_count); // 끝 페이지가 전체 페이지 수보다 많으면 전체 페이지 수로 설정

                if ($current_page_no >= 1) {
                ?>
                    <li><a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . ($current_page_no - 1); ?>">Prev</a></li>
                <?php
                }

                for ($page_no = $start_page; $page_no <= $end_page; $page_no++) {
                ?>
                    <li <?php echo $page_no == $current_page_no ? 'class="active"' : ''; ?>>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $page_no; ?>"><?php echo $page_no; ?></a>
                    </li>
                <?php
                }

                if ($end_page <= $total_page_count) {
                ?>
                    <li><a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . ($current_page_no + 1); ?>">Next</a></li>
                <?php
                }
                ?>
                <li><a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . ($total_page_count); ?>">▶▶</a></li>
            </ul>
        </div>
    </div>
    <div class="btn-wrap">
        <a href="/project/graph.php" class="btn index2">분석</a>
        <a href="/project/write.php" class="btn index1">추가</a>
    </div>
</body>

</html>