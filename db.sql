-- Create the database if it doesn't exist and switch to it
CREATE DATABASE IF NOT EXISTS todo_list;
USE todo_list;

-- Create a table for categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Create a table for tasks
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT NOT NULL,
    due_date DATE NOT NULL,
    category_id INT DEFAULT NULL,
    priority INT DEFAULT NULL,
    status ENUM('active', 'completed', 'overdue') DEFAULT 'active',
    completed_on DATE DEFAULT NULL, -- Column to track when a task is completed
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Example: Insert a category
INSERT INTO categories (name) VALUES ('Work'), ('Personal'), ('Family');

-- Example: Insert some tasks
INSERT INTO tasks (description, due_date, category_id, priority, status) VALUES 
('Finish the project report', CURDATE() + INTERVAL 2 DAY, 1, 1, 'active'),
('Buy groceries', CURDATE(), 2, 2, 'active'),
('Plan family dinner', CURDATE() + INTERVAL 1 WEEK, 3, 3, 'active');

-- Note: Remember to update the 'completed_on' field when a task's status is changed to 'completed'

