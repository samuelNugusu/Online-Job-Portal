-- Drop database if exists to start fresh
DROP DATABASE IF EXISTS job_portal;
CREATE DATABASE job_portal;
USE job_portal;

-- Create tables
CREATE TABLE employers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE job_seekers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
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

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    seeker_id INT NOT NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE
);

CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE future_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE future_job_interests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_title VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a sample employer
INSERT INTO employers (company_name, email, password) 
VALUES ('Tech Corp', 'tech@example.com', 'hashed_password');

-- Simplified job categories (reduced list to avoid issues)
SET @job_categories = 'Software Engineer,Web Developer,Data Analyst,Graphic Designer,Network Administrator,Cybersecurity Specialist,UI/UX Designer,Mobile App Developer,Product Manager,DevOps Engineer,Cloud Architect,Database Administrator,Machine Learning Engineer,Business Analyst,Project Manager,Frontend Developer,Backend Developer,Full Stack Developer,Quality Assurance Tester,IT Support Specialist,Digital Marketer,Sales Representative,Human Resources Manager,Financial Analyst,Marketing Manager';

-- Split job categories into an array for random selection
SET @job_categories_array = TRIM('"' FROM REPLACE(REPLACE(REPLACE(@job_categories, ',', '","'), ' ', ''), '"', '"'));

-- Insert 1000 jobs
INSERT INTO jobs (employer_id, title, description, location) 
SELECT 1, 
       SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(@job_categories_array, ',', @job_categories_array), ',', FLOOR(1 + RAND() * (LENGTH(@job_categories_array) - LENGTH(REPLACE(@job_categories_array, ',', '')) + 1))), ',', -1),
       CONCAT('Exciting opportunity to work as a ', SUBSTRING_INDEX(SUBSTRING_INDEX(CONCAT(@job_categories_array, ',', @job_categories_array), ',', FLOOR(1 + RAND() * (LENGTH(@job_categories_array) - LENGTH(REPLACE(@job_categories_array, ',', '')) + 1))), ',', -1), ' in a dynamic environment.'),
       CASE FLOOR(RAND() * 5)
           WHEN 0 THEN 'Addis Ababa'
           WHEN 1 THEN 'Dire Dawa'
           WHEN 2 THEN 'Tokyo'
           WHEN 3 THEN 'Berlin'
           WHEN 4 THEN 'Dubai'
       END
FROM information_schema.tables
LIMIT 1000;
SELECT COUNT(*) FROM job_portal.jobs;