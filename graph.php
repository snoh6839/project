<?php
define( "DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/" ); 
define( "URL_DB", DOC_ROOT."project/DB/db_conn.php"); 
include_once( URL_DB );


$conn = get_db_conn();


// 최근 일주일간의 기상카테고리 시작시간 평균 구하는 쿼리
$sql1 = "SELECT AVG(TIME_TO_SEC(start_time)) AS avg_start_time
        FROM Task
        WHERE category_no = (SELECT category_no FROM Category WHERE category_name = '기상')
        AND task_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$result1 = $conn->query($sql1);
$row1 = $result1->fetchAll();

// 최근 한달간의 카테고리별 사용 횟수 구하는 쿼리
$sql2 = "SELECT category_name, COUNT(category_name) AS num_count
        FROM Category c
        JOIN Task t ON c.category_no = t.category_no
        WHERE task_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
        GROUP BY c.category_name";
$result2 = $conn->query($sql2);


// 결과 출력
foreach ($row1 as $avg) {
    echo "최근 일주일간의 기상카테고리 시작시간 평균: " . gmdate("H:i:s", $avg["avg_start_time"]) . "<br><br>";
}

foreach ($result2 as $row2) {
    echo $row2['category_name'] . ": " . $row2['num_count'] . "<br>";
}
