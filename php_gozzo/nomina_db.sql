DROP DATABASE IF EXISTS nomina_db;
CREATE DATABASE nomina_db;
USE nomina_db;

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

INSERT INTO departments (name) VALUES 
('Development'), ('Design'), ('QA'), ('Marketing'), ('Accounting and Sales'), ('Human Resources');

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    id_department INT,
    base_salary DECIMAL(10, 2) DEFAULT 0.00,
    bonus DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (id_department) REFERENCES departments(id)
);

-- My Admin User with this password: luisinho
INSERT INTO employees (name, last_name, start_date, email, password, id_department, base_salary) 
VALUES ('Luis', 'Gozzo', '2006-09-14', 'lgozzo@supercines.com', 'luisinho', 6, 500.00);