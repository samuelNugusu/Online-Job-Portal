<?php
// Database configuration
$host = 'localhost';
$dbname = 'job_portal';
$username = 'root';     // Your MySQL username (default for XAMPP)
$password = '';         // Your MySQL password (default empty for XAMPP)

try {
    // Connect to MySQL and create the database if it doesn't exist
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");

    // Connect to the job_portal database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create tables if they don’t exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS employers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS job_seekers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS jobs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employer_id INT NOT NULL,
            title VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            location VARCHAR(100) NOT NULL,
            posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            job_id INT NOT NULL,
            seeker_id INT NOT NULL,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
            FOREIGN KEY (seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS future_jobs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            image_url VARCHAR(255),
            category VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS future_job_interests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            job_title VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>