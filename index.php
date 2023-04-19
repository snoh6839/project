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
$total_data_count = $db_conn->query('SELECT COUNT(*) FROM Task')->fetchColumn();
$page_data_count = 8; // 페이지당 보여줄 데이터 수
$total_page_count = ceil($total_data_count / $page_data_count);

// 현재 페이지 번호 구하기
$current_page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page_no = max($current_page_no, 1); // 페이지 번호는 1 이상이어야 함
$current_page_no = min($current_page_no, $total_page_count); // 페이지 번호는 전체 페이지 수 이하이어야 함

// 해당 페이지에 보여줄 데이터 구하기
$start_data_index = ($current_page_no - 1) * $page_data_count; // 페이지의 시작 데이터 인덱스
$stmt = $db_conn->prepare('SELECT * FROM Task LIMIT :start_index, :page_data_count');
$stmt->bindParam(':start_index', $start_data_index, PDO::PARAM_INT);
$stmt->bindParam(':page_data_count', $page_data_count, PDO::PARAM_INT);
$stmt->execute();
$task_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
<!-- 데이터 출력 -->

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
                            <tr>
                                <!-- 데이터 출력 시 htmlspecialchars 함수를 사용하여 보안 이슈 방지
                                문장내에 HTML코드가 들어가는 특수문자를 포함시켜 입력하고 화면으로 출력할 때,
                                HTML의 특수문자가 HTML태그로 적용되는 것이아니라 일반 문자로 인식되어 그대로 출력되게 해주는 역할이다.
                                바꾸는 문자로는 예시로
                                &는 &amp;로 바꾼다. "는 &quot;로 바꾼다. '는 &#039;로 바꾼다. <는 &lt로 바꾼다. >는 &gt로 바꾼다.-->
                                <td><?php echo htmlspecialchars($data['task_no']); ?></td>
                                <td><?php echo htmlspecialchars($data['task_date']); ?></td>
                                <td><?php echo htmlspecialchars($data['category_no']); ?></td>
                                <td><?php echo htmlspecialchars($data['task_title']); ?></td>
                                <td>
                                    <input type="checkbox" <?php echo $data['is_com'] == '1' ? 'checked' : ''; ?> onclick="if(this.checked){this.parentNode.parentNode.style.textDecoration='line-through';}else{this.parentNode.parentNode.style.textDecoration='none';}">
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- 페이지네이션 출력 -->
            <ul class="paging-wrap">
    
                <?php
                $prevPage = ($current_page_no == 1) ? $total_page_count : $current_page_no - 1;
                $nextPage = ($current_page_no == $total_page_count) ? 1 : $current_page_no + 1;
                if ($current_page_no > 1) {
                ?>
                    <li><a href='index.php?page=<?php echo $prevPage; ?>'>Prev</a></li>
                <?php } ?>
                <?php
                for ($i = 1; $i <= $total_page_count; $i++) {
                    if ($i === (int)$current_page_no) {
                ?>
                        <li><a href="/project/index.php?page=<?php echo $i ?>" class="page-icon active"><?php echo $i ?></a></li>
                    <?php
                    } else {
                    ?>
                        <li><a href="/project/index.php?page=<?php echo $i ?>" class="page-icon"><?php echo $i ?></a></li>
                    <?php
                    }
                }
                if ((int)$current_page_no < $total_page_count) {
                    ?>
                    <li><a href='/project/index.php?page=<?php echo $nextPage; ?>'>Next</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</body>

</html>