CREATE DATABASE IF NOT EXISTS test_db;

USE test_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create remember_tokens table for "Remember Me" functionality
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create applications table to store application.html page information
-- Modified to allow applications without user accounts
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(255) DEFAULT 'Student Application',
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    submission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table for application form fields (if you need to store specific form data)
CREATE TABLE IF NOT EXISTS application_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_value TEXT,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Personal Information
CREATE TABLE IF NOT EXISTS personal_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Address Information
CREATE TABLE IF NOT EXISTS address_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    street_address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state_province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Academic Information
CREATE TABLE IF NOT EXISTS academic_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    program ENUM('undergraduate', 'graduate', 'research') NOT NULL,
    intended_major VARCHAR(255) NOT NULL,
    highest_education ENUM('high_school', 'associate', 'bachelor', 'master', 'doctorate') NOT NULL,
    gpa DECIMAL(3,2),
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Additional Information
CREATE TABLE IF NOT EXISTS additional_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    personal_statement TEXT NOT NULL,
    terms_accepted BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Application Documents (for future file uploads)
CREATE TABLE IF NOT EXISTS application_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Create a simplified applications table for direct form submissions
-- This can be used for the application.html form if you don't want to use the relational structure above
CREATE TABLE IF NOT EXISTS simple_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    zip VARCHAR(20) NOT NULL,
    country VARCHAR(50) NOT NULL,
    program VARCHAR(50) NOT NULL,
    major VARCHAR(100) NOT NULL,
    education VARCHAR(50) NOT NULL,
    gpa VARCHAR(10),
    statement TEXT NOT NULL,
    terms BOOLEAN NOT NULL,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE contact_messages (
    id INT(11) NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submission_date DATETIME NOT NULL,
    PRIMARY KEY (id)
);