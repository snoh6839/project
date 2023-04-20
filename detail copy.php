<?php
    define( "DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/" ); // $_SERVER : 슈퍼글로벌 변수 ($_대문자) / 현재 사이트가 위치한 서버상의 위치
    define( "URL_DB", DOC_ROOT."project/DB/db_conn.php" );
    include_once( URL_DB );

    // Request Parameter 획득(GET)
    $arr_get = $_GET;

    // DB에서 게시글 정보 획득
    $result_info = select_task_info_no( $arr_get["task_no"] );
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 상세페이지</title>
    <link href="./css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<label for="date"></label>
<input type="date" value="<?php echo $result_info["task_date"] ?>" readonly>

<form>
<div>
    <label for="start_time">시작시간 </label>
    <input type="time" value="<?php echo $result_info["start_time"] ?>" readonly>
    <label for="end_time">종료시간 </label>
    <input type="time" value="<?php echo $result_info["end_time"] ?>" readonly>
</div>
<div>
    <label for="category">카테고리 </label> <?php echo $result_info["category_name"] ?>
</div>
<div>
    <label for="title">제목 </label>
    <input type="text" value="<?php echo $result_info["task_title"] ?>" readonly>
</div>
<div>
    <label for="complete">수행여부 완료</label>
    <input type="radio" value="1" <?php echo $result_info["is_com"]=="1" ? "checked" : "" ?> readonly>
</div>
<div>
    <label for="title">메모 </label>
    <input type="text" value="<?php echo $result_info["task_memo"] ?>" readonly>
</div>
</form>

<div>
    <button type="button" onclick="location.href='update.php?task_no=<?php echo $result_info['task_no'] ?>'">수정</button>
    <button type="button" onclick="location.href='index.php'">리스트</button>
</div>

</body>
</html>