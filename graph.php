<?php
define( "DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/" ); 
define( "URL_DB", DOC_ROOT."project/DB/db_conn.php"); 
include_once( URL_DB );


$conn = get_db_conn();



$http_method = $_SERVER["REQUEST_METHOD"];
if ($http_method === "POST"  ) 
{
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}
// 최근 일주일간의 기상카테고리 시작시간 평균 구하는 쿼리
$sql1 = "SELECT floor(AVG(TIME_TO_SEC(start_time))) AS avg_start_time
        FROM Task
        WHERE category_no = (SELECT category_no FROM Category WHERE category_name = '기상')
        AND task_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) ";
  
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

// foreach ($row1 as $avg) {
//     "최근 한달간의 기상카테고리 시작시간 평균: " . gmdate("H:i:s", $avg["avg_start_time"]) . "<br><br>";
// }

// foreach ($result2 as $row2) {
//     echo "최근 한달간의 카테고리별 사용 횟수: " . $row2['category_name'] . ": " . $row2['num_count'] . "<br>";
// }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <thead>
        <tr> 최근 한달간의 평균 기상시간:
            <td>
                <?php foreach ($row1 as $avg){ ?> 
                <?php
                echo gmdate("H:i:s", $avg["avg_start_time"]);
                ?>
                <?php } ?>
                </tr>
            </td>
    최근 한달간의 주요 수행횟수 : <?php foreach ($result2 as $row2) { ?>
    DO LIST: <?php echo $row2['category_name'] ?>
    AVER COUNT : <?php echo $row2['num_count']?>
    <?php }?>
    </thead>

    </form>
</body>
</html>