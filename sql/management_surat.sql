CREATE DATABASE management_surat;

USE management_surat;

CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE department (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    draft_file VARCHAR(255) NOT NULL,
    no_surat VARCHAR(255),
    status VARCHAR(50) DEFAULT 'pending',
    FOREIGN KEY (department_id) REFERENCES department(id)
);
