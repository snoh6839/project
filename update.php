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
                "task_no"       => $arr_post["task_no"]
                ,"task_date"	=> $arr_post["task_date"]
                ,"start_time"	=> $arr_post["start_time"]
                ,"end_time" 	=> $arr_post["end_time"]
                ,"task_title"	=> $arr_post["task_title"]
                ,"is_com"		=> $arr_post["is_com"]
                ,"task_memo"	=> $arr_post["task_memo"]
                ,"category_no"	=> $arr_post["category_no"]
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

<form method="post" action="update.php" id="form">
    <input type="hidden" value="<?php echo $result_info["task_no"] ?>" name="task_no">
    <label for="date"></label>
    <input type="date" value="<?php echo $result_info["task_date"] ?>" name="task_date">

    <div>
        <label for="start_time">시작시간 </label>
        <input type="time" value="<?php echo $result_info["start_time"] ?>" name="start_time">
        <label for="end_time">종료시간 </label>
        <input type="time" value="<?php echo $result_info["end_time"] ?>" name="end_time">
    </div>
    <div>
        <label for="category">카테고리
        <select name = "category_no">
            <option value= 1>독서</option>
            <option value= 2>운동</option>
            <option value= 3>공부</option>
            <option value= 4>기상</option>
            <option value= 5>취미</option>
            <option value= 6>회의</option>
            <option value= 7>쇼핑</option>
            <option value= 8>요리</option>
            <option value= 9>청소</option>
            <option value= 10>친구</option>
            <option value= 11>가족</option>
            <option value= 12>여행</option>
            <option value= 13>영화</option>
            <option value= 14>휴식</option>
            <option value= 15>기타</option>
            <option value= 16>병원</option>
            <option value= 17>식사</option>
        </select>
        </label>
    </div>
    <div>
        <label for="title">제목 </label>
        <input type="text" value="<?php echo $result_info["task_title"] ?>" name="task_title">
    </div>
    <div>
        <label for="complete">수행여부 완료</label>
        <input type="hidden" name="is_com" value="0">
        <input type="checkbox" name="is_com" value="1" <?php echo $result_info['is_com'] == '1' ? 'checked' : '' ?>>
    </div>
    <div>
        <label for="title">메모 </label>
        <input type="text" value="<?php echo $result_info["task_memo"] ?>" name="task_memo">
    </div>
    <div>
        <button type="submit">저장</button>
        <button type="button" onclick="location.href='index.php'">리스트</button>
    </div>
</form>

</body>
</html>