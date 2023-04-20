<?php
    define( "DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/" ); // $_SERVER : 슈퍼글로벌 변수 ($_대문자) / 현재 사이트가 위치한 서버상의 위치
    define( "URL_DB", DOC_ROOT."project/DB/db_conn.php" );
    include_once( URL_DB );
    
    // Request Method를 획득
    $http_method = $_SERVER["REQUEST_METHOD"];

    // GET 일 때
    if ( $http_method === "GET" )
    {
        $task_no = 1;
        if( array_key_exists( "task_no", $_GET ) )
        {
            $task_no = $_GET["task_no"];
        }
        $result_info = select_task_info_no( $task_no );
    }
    // POST 일 때
    else
    {
        $arr_post = $_POST;
        $arr_info= 
            array(
                ":task_date"	=> $param_arr["task_date"]
                ,":start_time"	=> $param_arr["start_time"]
                ,":end_time"	=> $param_arr["end_time"]
                ,":task_title"	=> $param_arr["task_title"]
                ,":is_com"		=> $param_arr["is_com"]
                ,":task_memo"	=> $param_arr["task_memo"]
                ,":category_no"	=> $param_arr["category_no"]
            );
            
		// update
		$result_cnt = update_task_info_no( $arr_info );

        header( "Location: detail.php?task_no=".$arr_post["task_no"] );
        exit(); // 36행에서 redirect 했기 때문에 이후의 소스코드는 실행할 필요가 없다.
    }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 수정페이지</title>
    <link href="./css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<label for="date"></label>
<input type="date" value="<?php echo $result_info["task_date"] ?>">

<form method="post" action="update.php" id="form">
<div>
    <label for="start_time">시작시간 </label>
    <input type="time" value="<?php echo $result_info["start_time"] ?>">
    <label for="end_time">종료시간 </label>
    <input type="time" value="<?php echo $result_info["end_time"] ?>">
</div>
<div>
    <!-- <label for="category">카테고리 </label> <?php echo $result_info["category_name"] ?> -->
    <label for="category">카테고리
        <select>
            <option value="">카테고리를 선택해 주세요</option>
            <option value="book" id = "category">독서</option>
            <option value="exer" id = "category">운동</option>
            <option value="study" id = "category">공부</option>
            <option value="wake" id = "category">기상</option>
            <option value="hobby" id = "category">취미</option>
            <option value="meeting" id = "category">회의</option>
            <option value="shopping" id = "category">쇼핑</option>
            <option value="cook" id = "category">요리</option>
            <option value="clean" id = "category">청소</option>
            <option value="friend" id = "category">친구</option>
            <option value="family" id = "category">가족</option>
            <option value="tuor" id = "category">여행</option>
            <option value="movie" id = "category">영화</option>
            <option value="rest" id = "category">휴식</option>
            <option value="opt" id = "category">기타</option>
            <option value="hospital" id = "category">병원</option>
            <option value="eat" id = "category">식사</option>
        </select>
    </label>
</div>
<div>
    <label for="title">제목 </label>
    <input type="text" value="<?php echo $result_info["task_title"] ?>">
</div>
<div>
    <label for="complete">수행여부 완료</label>
    <input type="radio" value="1" <?php echo $result_info["is_com"]=="1" ? "checked" : "" ?>>
</div>
<div>
    <label for="title">메모 </label>
    <input type="text" value="<?php echo $result_info["task_memo"] ?>">
</div>
<div>
    <button type="submit">저장</button>
    <button type="button" onclick="location.href='index.php'">리스트</button>
</div>
</form>
