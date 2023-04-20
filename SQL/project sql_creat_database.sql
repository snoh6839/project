-- DB 생성
CREATE database morning_project;  

-- Category Table 생성
CREATE TABLE Category (
    category_no INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL UNIQUE
);

-- Task Table 생성
CREATE TABLE Task (
    task_no INT AUTO_INCREMENT PRIMARY KEY,
    task_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME,
    task_title VARCHAR(255) NOT NULL,
    is_com ENUM('0','1') NOT NULL DEFAULT '0',
    task_memo TEXT,
    category_no INT NOT NULL,
    FOREIGN KEY (category_no) REFERENCES Category(category_no),
    CHECK (end_time > start_time)
);