CREATE DATABASE IF NOT EXISTS pool_route DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
USE pool_route;

CREATE TABLE IF NOT EXISTS urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

INSERT INTO admins (username, password_hash) 
VALUES ('admin', '$2y$10$FRnS692dKoOfiD/84k4tDeuzNdaj3KDwYqwY7UzbmJ8iG9aH4Roaa') 
ON DUPLICATE KEY UPDATE username=username;
