-- DROP DATABASE IF EXISTS clinic_db;
CREATE DATABASE clinic_db;
USE clinic_db;

-- 1. Unified Users Table (Auth Logic from Person A)
-- This stores everyone: Doctors and Patients
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('doctor', 'patient') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Patient Details (Profile Logic from Person A)
-- This links to the users table to store age, gender, etc.
CREATE TABLE patient_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    age INT,
    gender VARCHAR(10),
    contact_number VARCHAR(20),
    address TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Appointments (Merged Logic)
-- Using your column names so your JS doesn't break
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    time_slot VARCHAR(20) NOT NULL,
    symptoms TEXT,
    status VARCHAR(20) DEFAULT 'Scheduled',
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

-- 4. Medical Records (Your Core Responsibility)
CREATE TABLE medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    diagnosis TEXT NOT NULL,
    treatment TEXT NOT NULL,
    visit_date DATE NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. SEED DATA (Important for testing the patch)
-- Password for all is 'password123' (hashed using BCRYPT logic)
INSERT INTO users (name, email, password, role) VALUES 
('Dr. krishna', 'doctor@test.com', '$2y$10$8K9XvL7G5mGk1f3v9gHjUeR2uO3m.v5vGf1Y6vK6vK6vK6vK6vK6', 'doctor'),
('Dhaval', 'dhaval@test.com', '$2y$10$8K9XvL7G5mGk1f3v9gHjUeR2uO3m.v5vGf1Y6vK6vK6vK6vK6vK6', 'patient'),
('Rani', 'rani@test.com', '$2y$10$8K9XvL7G5mGk1f3v9gHjUeR2uO3m.v5vGf1Y6vK6vK6vK6vK6vK6', 'patient');

-- Link Dhaval and Rani to their patient details
INSERT INTO patient_details (user_id, age, gender, contact_number) VALUES 
(2, 21, 'Male', '9999888877'),
(3, 22, 'Female', '7777888899');






SELECT * FROM users WHERE role = 'patient';
SELECT email, LENGTH(password) as hash_length FROM users WHERE email = 'dhaval@test.com';



-- Check if appointments are linked to ID 1 (Dr. krishna)
SELECT * FROM appointments WHERE doctor_id = 1;