CREATE DATABASE blog;
USE blog;

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL
);

INSERT INTO posts (title, body) VALUES
('First Post', 'This is the first post.'),
('Second Post', 'This is the second post.');