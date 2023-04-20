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
$total_data_count = $db_conn->query('SELECT COUNT(*) FROM task')->fetchColumn();

// task table과 category table을 별도로 조회한 후 PHP에서 조합하여 출력
$sql = 'SELECT t.*, c.category_name FROM task t, category c WHERE t.category_no = c.category_no ORDER BY task_no DESC LIMIT :page_data_count OFFSET :start_index';
$stmt = $db_conn->prepare($sql);
$page_data_count = 5; // 페이지당 보여줄 데이터 수
$total_page_count = ceil($total_data_count / $page_data_count);

// 해당 페이지에 보여줄 데이터 구하기
$current_page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page_no = max($current_page_no, 1); // 페이지 번호는 1 이상이어야 함
$current_page_no = min($current_page_no, $total_page_count); // 페이지 번호는 전체 페이지 수 이하이어야 함
$start_data_index = ($current_page_no - 1) * $page_data_count; // 페이지의 시작 데이터 인덱스
$stmt->bindParam(':start_index', $start_data_index, PDO::PARAM_INT);
$stmt->bindParam(':page_data_count', $page_data_count, PDO::PARAM_INT);
$stmt->execute();
$task_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

//checked 시 수행 여부 업데이트
function update_is_com($param_arr)
{
    $sql =
        "UPDATE task
        SET is_com = :is_com
        WHERE task_no = :task_no";

    $arr_prepare = array(
        ":is_com" => $param_arr["is_com"],
        ":task_no" => $param_arr["task_no"]
    );

    $db_conn = get_db_conn();

    try {
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
        <a href="/project/graph.php" class="btn index1">그래프</a>
        <a href="/project/write.php" class="btn index2">추가</a>
    </div>
</body>

</html>