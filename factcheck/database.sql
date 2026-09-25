-- Run this file in phpMyAdmin (Import) or via `mysql -u root -p < database.sql`
-- This creates the database and all tables for the Fact-Check project

CREATE DATABASE IF NOT EXISTS factcheck_db;
USE factcheck_db;

CREATE TABLE USER (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  user_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  role VARCHAR(50) NOT NULL DEFAULT 'user',   -- 'user', 'fact_checker', 'admin'
  password VARCHAR(255) NOT NULL              -- stores a hashed password
);

CREATE TABLE CLAIM (
  claim_id INT PRIMARY KEY AUTO_INCREMENT,
  claim_text TEXT NOT NULL,
  category VARCHAR(100),
  status VARCHAR(50) NOT NULL DEFAULT 'pending',  -- pending, verified, rejected
  flagged TINYINT(1) NOT NULL DEFAULT 0,           -- admin marks a claim as reviewed/checked
  submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES USER(user_id) ON DELETE CASCADE
);

CREATE TABLE MEDIA (
  media_id INT PRIMARY KEY AUTO_INCREMENT,
  media_type VARCHAR(50),
  verification_status VARCHAR(50) DEFAULT 'unverified',
  file_url VARCHAR(500),
  claim_id INT NOT NULL,
  FOREIGN KEY (claim_id) REFERENCES CLAIM(claim_id) ON DELETE CASCADE
);

CREATE TABLE SOURCE (
  source_id INT PRIMARY KEY AUTO_INCREMENT,
  source_name VARCHAR(255),
  url VARCHAR(500),
  source_type VARCHAR(100)
);

CREATE TABLE EVIDENCE (
  evidence_id INT PRIMARY KEY AUTO_INCREMENT,
  evidence_text TEXT,
  evidence_type VARCHAR(100),
  claim_id INT NOT NULL,
  source_id INT,
  FOREIGN KEY (claim_id) REFERENCES CLAIM(claim_id) ON DELETE CASCADE,
  FOREIGN KEY (source_id) REFERENCES SOURCE(source_id) ON DELETE SET NULL
);

CREATE TABLE FACT_CHECK (
  fact_check_id INT PRIMARY KEY AUTO_INCREMENT,
  verdict VARCHAR(100),
  explanation TEXT,
  checked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  claim_id INT NOT NULL UNIQUE,
  verifier_id INT,
  FOREIGN KEY (claim_id) REFERENCES CLAIM(claim_id) ON DELETE CASCADE,
  FOREIGN KEY (verifier_id) REFERENCES USER(user_id) ON DELETE SET NULL
);

CREATE TABLE COMMENT (
  comment_id INT PRIMARY KEY AUTO_INCREMENT,
  comment_text TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  claim_id INT NOT NULL,
  user_id INT NOT NULL,
  reactions VARCHAR(255) DEFAULT '{}',
  FOREIGN KEY (claim_id) REFERENCES CLAIM(claim_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES USER(user_id) ON DELETE CASCADE
);
