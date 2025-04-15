CREATE DATABASE IF NOT EXISTS erp_leave_management;

USE erp_leave_management;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level ENUM('user', 'moderator', 'admin') NOT NULL UNIQUE
);

INSERT INTO roles (level) VALUES 
('user'), 
('moderator'), 
('admin');

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(40) NOT NULL,
    lastname VARCHAR(40) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    birth_date DATE NOT NULL,
    gender VARCHAR(10) NOT NULL,
    role_id INT NOT NULL DEFAULT 1,
    employed_from DATE NOT NULL,
    account_created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO users (firstname, lastname, email, password, birth_date, gender, role_id, employed_from) VALUES 
('User', 'User', 'user@example.com', 'qwe123', '2001-05-15', 'male', 1, '2015-06-01'),
('Moderator', 'Moderator', 'moderator@example.com', 'qwe123', '2000-08-20', 'female', 2, '2018-03-15'),
('Admin', 'Admin', 'admin@example.com', 'qwe123', '1998', 'male', 3, '2010-01-10');

CREATE TABLE leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days_count INT NOT NULL,
    status ENUM('Oczekujący', 'Zatwierdzony', 'Odrzucony') NOT NULL DEFAULT 'Oczekujący',
    notes VARCHAR(255),
    CHECK (start_date <= end_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO leaves (user_id, start_date, end_date, days_count, status, notes) VALUES
(1, '2025-06-01', '2025-06-10', 10, 'Oczekujący', 'Urlop wakacyjny'),
(2, '2025-04-15', '2025-04-20', 6, 'Zatwierdzony', 'Wyjazd rodzinny'),
(3, '2025-09-01', '2025-09-15', 15, 'Odrzucony', 'Za dużo wniosków na ten termin');
