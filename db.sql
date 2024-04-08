CREATE DATABASE IF NOT EXISTS todo_list;
USE todo_list;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT NOT NULL,
    due_date DATE NOT NULL,
    category_id INT DEFAULT NULL,
    priority INT DEFAULT NULL,
    status ENUM('active', 'completed', 'overdue') DEFAULT 'active',
    completed_on DATE DEFAULT NULL, -- Add this line for the completed_on column
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

