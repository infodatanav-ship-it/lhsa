CREATE DATABASE IF NOT EXISTS lhsa_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lhsa_web;

CREATE TABLE users (
	id         INT AUTO_INCREMENT PRIMARY KEY,
	username   VARCHAR(50)  NOT NULL UNIQUE,
	email      VARCHAR(100) NOT NULL UNIQUE,
	password   VARCHAR(255) NOT NULL,
	role       ENUM('user','admin') DEFAULT 'user',
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE documents (
	id         INT AUTO_INCREMENT PRIMARY KEY,
	user_id    INT NOT NULL,
	filename   VARCHAR(255) NOT NULL,
	stored_name VARCHAR(255) NOT NULL,
	size       INT NOT NULL,
	uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);