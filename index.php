<?php
define("DOC_ROOT", $_SERVER["DOCUMENT_ROOT"] . "/");
define("URL_DB", DOC_ROOT . "project/DB/db_conn.php");

include_once(URL_DB);

// DB 연결 객체 가져오기
$db_conn = get_db_conn();
if (!$db_conn) {
    // DB 연결 실패 시, 예외 발생
    throw new Exception("DB 연결에 실패했습니다.");
}


// 전체 데이터 수 가져오기
// inner join으로 task와 category table join 하기
$stmt = $db_conn->prepare('SELECT t.*, c.category_name FROM task t INNER JOIN category c ON t.category_no = c.category_no LIMIT :start_index, :page_data_count');

$stmt->bindParam(':start_index', $start_data_index, PDO::PARAM_INT);
$stmt->bindParam(':page_data_count', $page_data_count, PDO::PARAM_INT);
$stmt->execute();
$task_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_data_count = $db_conn->query('SELECT COUNT(*) FROM task')->fetchColumn();
$page_data_count = 5; // 페이지당 보여줄 데이터 수
$total_page_count = ceil($total_data_count / $page_data_count);

// 현재 페이지 번호 구하기
$current_page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page_no = max($current_page_no, 1); // 페이지 번호는 1 이상이어야 함
$current_page_no = min($current_page_no, $total_page_count); // 페이지 번호는 전체 페이지 수 이하이어야 함

// 해당 페이지에 보여줄 데이터 구하기
$start_data_index = ($current_page_no - 1) * $page_data_count; // 페이지의 시작 데이터 인덱스
$stmt = $db_conn->prepare('SELECT t.*, c.category_name FROM task t INNER JOIN category c ON t.category_no = c.category_no LIMIT :start_index, :page_data_count');
$stmt->bindParam(':start_index', $start_data_index, PDO::PARAM_INT);
$stmt->bindParam(':page_data_count', $page_data_count, PDO::PARAM_INT);
$stmt->execute();
$task_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

//checked 시 수행 여부 업데이트
function update_is_com($param_arr = array())
{
    $result_cnt = 0;
    $sql =
        " UPDATE "
        . " task "
        . " SET "
        . " is_com = :is_com "
        . " WHERE "
        . " task_no = :task_no ";

    $arr_prepare =
        array(
            ":is_com" => isset($param_arr["is_com"][0]) ? $param_arr["is_com"][0] : 0,
            ":task_no" => isset($param_arr["task_no"][0]) ? $param_arr["task_no"][0] : 0
        );

    $db_conn = null;
    try {
        $db_conn = get_db_conn();
        $db_conn->beginTransaction();
        $stmt = $db_conn->prepare($sql);
        $stmt->execute($arr_prepare);
        $result = $stmt->rowCount();
        $db_conn->commit();
    } catch (Exception $e) {
        $db_conn->rollback();
        return $e->getMessage();
    } finally {
        if ($db_conn !== null) {
            $db_conn = null;
        }
    }

    return $result;
}




$http_method = $_SERVER["REQUEST_METHOD"];


if ($http_method === "POST") {
    $arr_post = $_POST;
    $is_com_old =
        array(
            "is_com" => $arr_post["is_com"],
            "task_no" => $arr_post["task_no"][0]
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
    <!-- css 링크 -->
    <link rel="stylesheet" href="./css/main.css">
</head>

<body>
    <div class="sidebox">
        <div class="top"></div>
        <div class="bottom"></div>
    </div>
    <div class="contianer">
        <div class="title top">
            <h1>MIRACLE MORNING</h1>
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
                        <?php foreach ($task_data as $data) { ?>
                            <tr <?php echo $data['is_com'] == '1' ? 'style="text-decoration:line-through;"' : ''; ?>>
                                <!-- 데이터 출력 시 htmlspecialchars 함수를 사용하여 보안 이슈 방지
                                문장내에 HTML코드가 들어가는 특수문자를 포함시켜 입력하고 화면으로 출력할 때,
                                HTML의 특수문자가 HTML태그로 적용되는 것이아니라 일반 문자로 인식되어 그대로 출력되게 해주는 역할이다.
                                바꾸는 문자로는 예시로
                                &는 &amp;로 바꾼다. "는 &quot;로 바꾼다. '는 &#039;로 바꾼다. <는 &lt로 바꾼다. >는 &gt로 바꾼다.-->
                                <td><?php echo htmlspecialchars($data['task_no']); ?></td>
                                <td><?php echo htmlspecialchars($data['task_date']); ?></td>
                                <td><?php echo htmlspecialchars($data['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($data['task_title']); ?></td>
                                <!-- is_done 이 1이면 취소선 추가 -->
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="task_no[]" value="<?php echo $data['task_no']; ?>">
                                        <input type="checkbox" name="is_com[]" value="1" <?php echo $data['is_com'] == '1' ? 'checked' : ''; ?> onchange="if(this.checked){this.value='1';}else{this.value='0';};this.form.submit();">
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
</body>

</html>