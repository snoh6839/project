<?php
define( "DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/" ); 
define( "URL_DB", DOC_ROOT."project/DB/db_conn.php"); 
include_once( URL_DB );

// 입력한 데이터들을 받아와서 최종적으로 index페이지로 전송해 주도록 하는 구문
$http_method = $_SERVER["REQUEST_METHOD"];
if ( $http_method === "POST" )
{
    $arr_post = $_POST;
    
    $result_write = write_info( $arr_post );
    header( "location:index.php" );
    exit();
}


//DB에 입력될 데이터 레코드를 입력하는 sql문
function write_info(&$param_arr)
{
    $sql = " INSERT INTO task( "
        ." task_date "
        ." ,start_time "
        ." ,end_time "
        ." ,task_title "
        // ." ,is_com "
        ." ,task_memo "
        ." ,category_no "
        ." ) "

        ." VALUES ( " 
        ." :task_date "
        ." ,:start_time "
        ." ,:end_time "
        ." ,:task_title "
        // ." ,:is_com "
        ." ,:task_memo "
        ." ,:category_no "
        ." ) "
        ;
// prepare로 데이터들의 배열을 입력
        $arr_prepare = 
        array(
            ":task_date" => $param_arr["task_date"]
            ,":start_time" => $param_arr["start_time"]
            ,":end_time" => $param_arr["end_time"]
            ,":task_title" => $param_arr["task_title"]
            // ,":is_com" => $param_arr["is_com"]
            ,":task_memo" => $param_arr["task_memo"]
            ,":category_no" => $param_arr["category_no"]
        );


        $db_conn = null;
        try 
        {
            $db_conn= get_db_conn(); //PDO object 셋
            $db_conn->beginTransaction(); //Transaction 시작 : 데이터를 변경하기(insert, update, delete) 때문에 일련의 연산이 완료되면 commit 실패시 rollback을 통해서 데이터를 관리 하게 시킨다. 
            $stmt = $db_conn->prepare( $sql ); //statement object 셋팅
            $stmt->execute( $arr_prepare ); //DB request
            $result_cnt = $stmt->rowCount(); // 업데이트 되서 영향을 받은 행의 숫자를 가져온다.
            $db_conn->commit();
            
        } 
        catch ( Exception $e) 
        {
            $db_conn->rollback(); // 트랜잭션이 진행중에 오류가 나면 롤백을 시켜서 돌려 준다.
            return $e->getMessage();
        }
        finally //성공여부와 상관없이 null로 커넥션을 초기화 시켜준다.
        {
            $db_conn = null;
        }
}




?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/write.css">
    <title>작성페이지</title>
</head>
<body>
    <form method = "post" action = "">
        <div class = top>
            <label for = "date_title">
            <input type="date" name = "task_date" ></label>
        </div>
        <div class = bottom>
        <br>
            <div class = listTable>
                <table>
                    <thead>
                        <th>시작시간<input type="time" name = "start_time" ></label></th>
                        <th>종료시간<input type="time" name = "end_time" ></label></th>
                    </thead>
                    <tbody>
                    <label for ="category">카테고리
                    
                        <select name = "category_no">
                            <!-- <option value= 0>카테고리를 선택해 주세요</option> -->
                            <option value= 1 >독서</option>
                            <option value= 2 >운동</option>
                            <option value= 3 >공부</option>
                            <option value= 4 >기상</option>
                            <option value= 5 >취미</option>
                            <option value= 6 >회의</option>
                            <option value= 7 >쇼핑</option>
                            <option value= 8 >요리</option>
                            <option value= 9 >청소</option>
                            <option value= 10 >친구</option>
                            <option value= 11 >가족</option>
                            <option value= 12 >여행</option>
                            <option value= 13 >영화</option>
                            <option value= 14 >휴식</option>
                            <option value= 15 >기타</option>
                            <option value= 16 >병원</option>
                            <option value= 17 >식사</option>
                        </select>
                    </label>
                    <br>
                    <label for ="task_title">제목 <input type="text" name ="task_title"></label>
                    <br>
                    <!-- <label for ="is_com">수행여부 <input type="checkbox" name = "is_com"></label> -->
                    <br>
                    <label for ="task_memo">메모 <input type="text" name = "task_memo"></label>
                </table>
                <br>
                <button type = "submit">작성</button>
            </div>
        </div>
    </form>





</body>
</html>

