<?php

// DB 연결 객체를 가져오는 함수
// function get_db_conn() {
// 	$host = "localhost";
// 	$user = "root";
// 	$pass = "root506";
// 	$charset = "utf8mb4";
// 	$db_name = "morning_project";
// 	$dsn = "mysql:host=".$host.";dbname=".$db_name.";charset=".$charset;
// 	$pdo_option =
// 		array(
// 			PDO::ATTR_EMULATE_PREPARES		=> false,
// 			PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION,
// 			PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC
// 		);
	
// 	try {
// 		$db_conn = new PDO( $dsn, $user, $pass, $pdo_option );
// 		return $db_conn;
// 	} catch( PDOException $e ) {
// 		// DB 연결 실패 시, null 반환
// 		return null;
// 	}
	
// }

// ---------------------------------
// 함수명	: db_conn
// 기능		: DB Connection
// 파라미터	: Obj	&$param_conn
// 리턴값	: 없음
// ---------------------------------
function db_conn( &$param_conn )
{
	$host = "localhost";
	$user = "root";
	$pass = "root506";
	$charset = "utf8mb4";
	$db_name = "morning_project";
	$dns = "mysql:host=".$host.";dbname=".$db_name.";charset=".$charset;
	$pdo_option =
		array(
			PDO::ATTR_EMULATE_PREPARES		=> false
			,PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION
			,PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC
		);
	
	try
	{
		$param_conn = new PDO( $dns, $user, $pass, $pdo_option );
		return $param_conn;
	}
	catch( Exception $e )
	{
		$param_conn = null;
		throw new Exception( $e->getMessage() );
	}
	
}

// 전체 데이터 수 가져오기
function total_data()
{

    $sql = 'SELECT COUNT(*) FROM task';
    $arr_prepare= array();

    
    try 
    {
        db_conn($conn);
        $stmt = $conn->prepare($sql);
        $stmt->execute($arr_prepare);
        $result_cnt = $stmt->fetchColumn();
    } 
    catch (Exception $e) 
    {
        $conn = null;
		throw new Exception( $e->getMessage() );
    }
    finally
    {
        $conn = null;
    }
    return $result_cnt;
}

$total_data_count = total_data();
// var_dump($total_data_count);


// task table과 category table을 별도로 조회한 후 PHP에서 조합하여 출력

function list_page($start_data_index, $page_data_count)
{

    $sql = 'SELECT t.*, c.category_name FROM task t, category c WHERE t.category_no = c.category_no ORDER BY is_com asc,task_no DESC LIMIT :page_data_count OFFSET :start_index';

    
    try 
    {
        $stmt = db_conn($conn);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':start_index', $start_data_index, PDO::PARAM_INT);
        $stmt->bindParam(':page_data_count', $page_data_count, PDO::PARAM_INT);
        $stmt->execute();
        $task_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    catch (Exception $e) 
    {
        $conn = null;
        throw new Exception($e ->getMessage());
    }
    finally
    {
        
        $conn = null;
    }
    return $task_data;

}

// ---------------------------------
// 함수명	: select_task_info_no
// 기능		: 게시판 특정 게시글 정보 검색
// 파라미터	: Array		&$param_no
// 리턴값	: Array		$result
// ---------------------------------
function select_task_info_no( &$param_no )
{
    // task 테이블, category 테이블 값을 표시하는 쿼리
	$sql =
		" SELECT "
		."	ts.task_no "
		."	,ts.task_date "
		."	,ts.start_time "
		."	,ts.end_time "
		."	,ct.category_name "
		."	,ts.task_title "
		."	,ts.is_com "
		."	,ts.task_memo "
		." FROM "
		."	task ts "
		." INNER JOIN category ct ON ts.category_no = ct.category_no "
		." WHERE "
		."	ts.task_no = :task_no "
		;

        // task_no로 매칭
	$arr_prepare =
		array(
			":task_no"	=> $param_no
		);

	$conn = null;
	try
	{
		db_conn( $conn ); // PDO object set(DB연결)
		$stmt = $conn->prepare( $sql ); // statement object set
		$stmt->execute( $arr_prepare ); // DB request
		$result = $stmt->fetchAll();
	}
	catch( Exception $e )
	{
		return $e->getMessage(); // DB연결 실패시 메세지 표시
	}
	finally
	{
		$conn = null; // PDO 파기
	}

	return $result[0]; // 조건을 PK로 걸어줘서, 리턴값이 1개만 있기 때문에 [0]을 적어줌.
}

//'기상' 값의 평균만을 가져오는 쿼리문 - sql문을 이용해서 카테고리가 기상인 데이터를 추출해서 그안에서 최근 1달간의 날짜의 횟수를 뽑아 낸다.
function wake_up_fnc()
{
$sql1 = " SELECT floor(AVG(TIME_TO_SEC(start_time))) AS avg_start_time 
        FROM Task
        WHERE category_no = (SELECT category_no FROM Category WHERE category_name = '기상')
        AND task_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) ";

    try 
    {
        db_conn($conn); // PDO object set(DB연결)
        $result1 = $conn->query($sql1); //sql문 연결문
        $row1 = $result1->fetchAll(); // 전체 데이터의 레코드를 배열로 받아옵니다.
    } 
    catch (Exception $e) 
    {
        $conn = null;
		throw new Exception( $e->getMessage() );
    }
    finally
    {
        $conn = null;
    }
    return $row1;
}


//월별 카테고리별 수행 횟수를 추출하는 함수 - 최근 한달간 기상을 제외한 나머지 카테고리 수를 뽑아 오기 위한 쿼리문
function month_cnt()
{
$sql2 = " SELECT category_name, COUNT(category_name) AS num_count
        FROM Category c
        JOIN Task t ON c.category_no = t.category_no
        WHERE task_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
        AND c.category_name != '기상'
        GROUP BY c.category_name
        ORDER BY num_count DESC LIMIT 3 ";

// $limit_num = 5;
// $stmt->bindParam(':limit_num' , $limit_num, PDO::PARAM_INT);
// $stmt->execute();

    try 
    {
        db_conn($conn);
        $result2 = $conn->query($sql2);
        
    } 
    catch (Exception $e) 
    {
        $conn = null;
		throw new Exception( $e->getMessage() );
    }
    finally
    {
        $conn = null;
    }
    return $result2;
}

//월별 수행결과 횟수를 가져오는 함수 - 수행 결과를 1로 값을 받아서 1인 값들의 비율을 찾도록 하기 위함
function value_avg_fnc()
{
$sql3 = "SELECT 
            (SELECT COUNT(*) FROM task WHERE is_com = '1') /
            (SELECT COUNT(*) FROM task) AS completion_ratio";

    try 
    {
        db_conn($conn);
        $result3 = $conn->query($sql3);
        $evalu = $result3->fetchall();

        
    } 
    catch (Exception $e) 
    {
        $conn = null;
		throw new Exception( $e->getMessage() );
    }
    finally
    {
        $conn = null;
    }
    return $evalu;
    
}


// ---------------------------------
// 함수명	: update_task_info_no
// 기능		: 게시판 특정 게시글 정보 수정
// 파라미터	: Array			&$param_arr
// 리턴값	: INT/STRING	$result_cnt/ERRMSG
// ---------------------------------
function update_task_info_no( &$param_arr )
{
	$sql=
	" UPDATE "
	."	task "
	." SET "
	."	task_date = :task_date "
	."	,start_time = :start_time "
	."	,end_time = :end_time "
	."	,task_title = :task_title "
	."	,is_com = :is_com "
	."	,task_memo = :task_memo "
	."	,category_no = :category_no "
	." WHERE "
	."	task_no = :task_no "
	;
	$arr_prepare =
	array(
		":task_no"		=> $param_arr["task_no"]
		,":task_date"	=> $param_arr["task_date"]
		,":start_time"	=> $param_arr["start_time"]
		,":end_time"	=> $param_arr["end_time"]
		,":task_title"	=> $param_arr["task_title"]
		,":is_com"		=> $param_arr["is_com"]
		,":task_memo"	=> $param_arr["task_memo"]
		,":category_no"	=> $param_arr["category_no"]
	);
	
	$conn = null;
	try
	{
		db_conn( $conn ); // PDO object set(DB연결)
		$conn->beginTransaction(); // Transaction 시작
		$stmt = $conn->prepare( $sql ); // statement object set
		$stmt->execute( $arr_prepare ); // DB request
		$result_cnt = $stmt->rowCount(); // query 적용 recode 갯수
		$conn->commit();
	}
	catch( Exception $e )
	{
		$conn->rollback();
		return $e->getMessage();
	}
	finally
	{
		$conn = null; // PDO 파기
	}

	return $result_cnt;
}

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

    db_conn($db_conn);

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
            db_conn($db_conn); //PDO object 셋
            $db_conn->beginTransaction(); //Transaction 시작 : 데이터를 변경하기(insert, update, delete) 때문에 일련의 연산이 완료되면 commit 실패시 rollback을 통해서 데이터를 관리 하게 시킨다. 
            $stmt = $db_conn->prepare( $sql ); //statement object 셋팅
            $stmt->execute( $arr_prepare ); //DB request
            $result_cnt = $db_conn->lastInsertId();
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
        return $result_cnt;
}






?>