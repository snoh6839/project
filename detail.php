<?php
define("DOC_ROOT", $_SERVER["DOCUMENT_ROOT"] . "/"); // $_SERVER : 슈퍼글로벌 변수 ($_대문자) / 현재 사이트가 위치한 서버상의 위치
define("URL_DB", DOC_ROOT . "project/DB/db_conn.php");
include_once(URL_DB);

// Request Parameter 획득(GET)
$arr_get = $_GET;

// DB에서 게시글 정보 획득
$result_info = select_task_info_no($arr_get["task_no"]);
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 상세페이지</title>
    <link href="./css/main.css" rel="stylesheet" type="text/css">.
    <link href="./css/detail.css" rel="stylesheet" type="text/css">
</head>

<body>

    <div class="sidebox">
        <div class="top"></div>
        <div class="bottom">
            <div class="update">
                <img src="./source/door.png" id="img"><br>
                '아침 10분은 밤 1시간만큼의 생산성과 가치가 있다'라는 말에서 알 수 있듯이 아침에 일찍 일어나는 것이 얼마나 효율적인지는 알고 있을 것입니다.
            </div>
        </div>
    </div>
    <div class="contianer">
        <div class="title top">
            <input type="hidden" value="<?php echo $result_info["task_no"] ?>" name="task_no">
            <div class="sun">
                <label for="date"></label><img src="./source/sun.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="date" value="<?php echo $result_info["task_date"] ?>" name="task_date" readonly><img src="./source/sun.png">
            </div>
        </div>
        <div class="bottom">
            <div class="listTable">
                <ul>
                    <li>
                        <label for="start_time">시작시간 </label>
                        <input type="time" value="<?php echo $result_info["start_time"] ?>" name="start_time" readonly>
                    </li>
                    <li>
                        <label for="end_time">종료시간 </label>
                        <input type="time" value="<?php echo $result_info["end_time"] ?>" name="end_time" readonly>
                    </li>
                    <li>
                        <label for="category">카테고리 </label>
                        <input type="text" value="<?php echo $result_info["category_name"] ?>" readonly>
                    </li>
                    <li>
                        <label for="title">제목 </label>
                        <input type="text" value="<?php echo $result_info["task_title"] ?>" id="title" readonly>
                    </li>
                    <li>
                        <label for="complete">수행여부 완료</label>
                        <?php if ($result_info['is_com'] == '1') { ?>
                            <button type="button" class="checkbox_btn_com" id="complete"></button>
                        <?php } else { ?>
                            <button type="button" class="checkbox_btn" id="complete"></button>
                        <?php } ?>
                        <input type="hidden" name="is_com" value="<?php echo $result_info['is_com'] == '1' ? '0' : '1' ?>">
                    </li>
                    <li>
                        <label for="memo">메모 </label>
                        <input type="text" value="<?php echo $result_info["task_memo"] ?>" id="memo" readonly>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="btn-wrap">
        <a href="index.php" class="btn index2">리스트</a>
        <a href="update.php?task_no=<?php echo $result_info['task_no'] ?>" class="btn index1">수정</a>
    </div>

</body>

</html>