<?php
define( "DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]."/" ); 
define( "URL_DB", DOC_ROOT."project/DB/db_conn.php"); 
include_once( URL_DB );


db_conn($conn);



$http_method = $_SERVER["REQUEST_METHOD"];
if ($http_method === "POST"  ) 
{
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}


// 최근 일주일간의 기상카테고리 시작시간 평균 구하는 쿼리

$wake_up_result = wake_up_fnc();

// 최근 한달간의 카테고리별 사용 횟수 구하는 쿼리


$count_month_fnc = month_cnt();




$avg_fnc = value_avg_fnc();
// var_dump($result3);
// $sql3 = " SELECT 
// 	(SELECT COUNT(*) FROM task where is_com = '1')/
// 	(SELECT COUNT(*) from task )";
// $result3 = $conn->query($sql3);
// $evalu = $result3->fetchAll();
// $result_val = $evalu[0];



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
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/graph.css">
    <title>Document</title>
</head>
<body>
<div class="sidebox">
    <div class="top"></div>
    <div class="bottom">
        
            <div id="slider">
                <ul>
                    <li>
                    <div class="slider-container">
                        <p>"하루의 시작은 아침에 결정된다. 아침에 마음을 다잡고</p> 
                        <p> 열심히 하루를 시작하면 좋은 결과가 따를 것이다."</p>
                        <p> - 드웨인 존슨 - </p>
                    </div>
                    </li>
                    
                    <li>
                        <div class="slider-container">
                        <p>"아침에 잘 시작하면 좋은 결실을 맺을 수 있다." </p>
                        <p>- 이사야 토마스 - </p>
                    </div>
                    </li>
                    
                    <li>
                        <div class="slider-container">
                        <p>"아침 해를 보며 미소 짓는 것은 하루를</p>
                        <p>  긍정적으로 시작하는 가장 좋은 방법이다."</p>
                        
                        <p> - 마크 트웨인 - </p>
                    </div>
                    </li>
                    
                    <li>
                        <div class="slider-container">
                        <p>"아침에 일찍 일어나는 것은 성공의 첫걸음이다."</p>
                        <p> - 아리스토텔레스 - </p>
                    </div>
                    </li>
                    <li>
                        <div class="slider-container">
                        <p>"아침에 일어나서 감사하는 마음으로 하루를 시작하면,</P>
                        <p> 행복과 희망이 가득한 하루가 될 것이다."</p>
                        <p> - 루이스 헤이 - </p>
                    </div>
                    </li>
                    <li>
                        <div class="slider-container">
                        <p>"새로운 날은 새로운 시작을 의미한다. 아침은 어제의 실수를 바로잡고</p>
                        <p> 더 나은 삶을 시작할 수 있는 시간이다."</p>
                        <p> - 스티브 마를렛 - </p>
                    </div>
                    </li>
                    <li>
                        <div class="slider-container">
                        <p>"새벽에 눈을 뜨면 하루를 더 살아갈 수 있는 특별한 선물을 받은 것이다."</P>
                        <p> - 이자크 바쉐비스 싱어 - </p>
                    </div>
                    </li>
                    <li>
                        <div class="slider-container">
                        <p>"사람의 삶은 매일 아침에 새롭게 시작된다." </p>
                        <p> - 레프 톨스토이 - </p>
                    </div>
                    </li>
                </ul>
               
        </div>
        <div class="photo"></div>
         <!-- <img src="./SOURCE/arrow1.png" alt=""> -->
    </div>
</div>
<div class = "contianer">
    <div class = "title top">
        <h1><img src="./source/sun.png" id="sun">&nbsp;&nbsp;My Analytics&nbsp;&nbsp;<img src="./source/sun.png" id="sun"></h1> 
    </div>
        <div class = "bottom">
            <div class="listTable">
                <table>
                <tbody>
                    
                        <tr>
                            <td>최근 한달 평균 기상시간: </td>
                            <td> <?php foreach ($wake_up_result as $avg){ ?> <?php echo gmdate("H시 i분 s초", $avg["avg_start_time"]);?> <?php } ?></td>
                        </tr>

                        <tr>
                            <td>최근 한달 활동 TOP3<span>(기상제외)</span> <?php $i = 1; foreach ($count_month_fnc as $row2) { ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $i; ?> 위: <?php echo $row2['category_name']; ?>
                            </td>
                            <td> 활동 횟수: <?php echo $row2['num_count']?> 
                                <?php if($i++ == 3) 
                            { 
                                break;
                            }}?>
                            </td>
                        </tr>
            <div>
                <tr>
                        <td>
                            수행율: <?php foreach ($avg_fnc as $completion_ratio) {  echo $completion_ratio['completion_ratio']* 100; }?> % 
                        </td>
                        <td>
                            <div class = "sticy">
                                <?php 
                                if ($completion_ratio['completion_ratio']*100 >= 80) { 
                                ?>
                                    <div class = "img"> <img src="./SOURCE/1.png" alt=""></div>
                                <?php
                                }
                                elseif ($completion_ratio['completion_ratio']*100 >= 70) { 
                                ?>
                                    <div class = "img"> <img src="./SOURCE/2.png" alt=""></div>
                                <?php 
                                } 
                                elseif ($completion_ratio['completion_ratio']*100 >= 60) { 
                                ?>
                                    <div class = "img"> <img src="./SOURCE/3.png" alt=""></div>
                                <?php
                                }
                                elseif ($completion_ratio['completion_ratio']*100 >= 50) { 
                                ?>
                                    <div class = "img"> <img src="./SOURCE/4.png" alt=""></div>
                                <?php
                                }
                                else {
                                ?>
                                    <div class = "img"> <img src="./SOURCE/5.png" alt=""></div>
                                <?php
                                }
                                ?>
                            </div>
                        </td>
                    </tr>     
                </div>
                </table>
            </div>   
        </div>
</div>
<div class="btn-wrap">
                    <button type="button" onclick="location.href='index.php'" class="btn index2">리스트</button>
                </div>    
</body>
</html>