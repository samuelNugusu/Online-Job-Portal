DROP DATABASE IF EXISTS job_portal;
CREATE DATABASE job_portal;
USE job_portal;

CREATE TABLE employers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(100) NOT NULL,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE
);

INSERT INTO employers (company_name, email, password) 
VALUES ('Tech Corp', 'tech@example.com', 'hashed_password');

INSERT INTO jobs (employer_id, title, description, location) VALUES
(1, 'Software Engineer', 'Develop software applications', 'Addis Ababa'),
(1, 'Web Developer', 'Build web applications', 'Remote'),
(1, 'Data Analyst', 'Analyze data sets', 'Tokyo');