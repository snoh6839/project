-- 
-- insert into category
-- (
-- category_name
-- )
-- 
-- values
-- ('독서')
-- ,('운동')
-- ,('공부')
-- ,('기상')
-- ,('취미')
-- ,('회의')
-- ,('쇼핑')
-- ,('요리')
-- ,('청소')
-- ,('친구')
-- ,('가족')
-- ,('여행')
-- ,('영화')
-- ,('휴식')
-- ,('기타')
-- ,('병원')
-- ,('식사')
-- ;
-- 
-- COMMIT;
-- 
DELIMITER $$
DROP PROCEDURE IF EXISTS loopInsert$$
 
CREATE PROCEDURE loopInsert()
BEGIN
    DECLARE i INT DEFAULT 1;
        
    WHILE i <= 500 DO
		INSERT INTO task( task_title, task_memo, task_date, start_time, end_time,  category_no) VALUES
		( CONCAT('독서 제목글 ', i), CONCAT('독서 메모글 ', i), date_add(now(), INTERVAL + i DAY), date_add(now(), INTERVAL + i SECOND), date_add(now(), INTERVAL + (1 + i) SECOND), 1 );
        SET i = i + 1;
    END WHILE;
END$$
DELIMITER $$

CALL loopInsert;