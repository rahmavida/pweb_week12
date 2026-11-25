-- BIMBINGKU - skema database (modul pendaftaran siswa + upload foto + kartu member)
CREATE DATABASE IF NOT EXISTS bimbingku
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bimbingku;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS audit_log, rate_limit_hits, member_cards, students, classes, branches, users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'siswa') NOT NULL DEFAULT 'siswa',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    must_change_password TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE branches (
    branch_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    branch_name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    PRIMARY KEY (branch_id)
) ENGINE=InnoDB;

CREATE TABLE classes (
    class_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    class_name VARCHAR(100) NOT NULL,
    branch_id INT UNSIGNED NOT NULL,
    schedule_day VARCHAR(30) NOT NULL,
    schedule_time VARCHAR(20) NOT NULL,
    quota INT UNSIGNED NOT NULL DEFAULT 20,
    fee INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (class_id),
    CONSTRAINT fk_class_branch FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
) ENGINE=InnoDB;

CREATE TABLE students (
    student_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    reg_number VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    class_id INT UNSIGNED NOT NULL,
    branch_id INT UNSIGNED NOT NULL,
    photo VARCHAR(255) NOT NULL,
    payment_proof VARCHAR(255) NULL,
    registration_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Menunggu', 'Diterima', 'Ditolak') NOT NULL DEFAULT 'Menunggu',
    admin_note TEXT NULL,
    PRIMARY KEY (student_id),
    CONSTRAINT fk_student_user   FOREIGN KEY (user_id)   REFERENCES users(id),
    CONSTRAINT fk_student_class  FOREIGN KEY (class_id)  REFERENCES classes(class_id),
    CONSTRAINT fk_student_branch FOREIGN KEY (branch_id) REFERENCES branches(branch_id)
) ENGINE=InnoDB;

CREATE TABLE member_cards (
    card_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    student_id INT UNSIGNED NOT NULL UNIQUE,
    card_number VARCHAR(50) NOT NULL UNIQUE,
    issue_date DATE NOT NULL,
    PRIMARY KEY (card_id),
    CONSTRAINT fk_card_student FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE rate_limit_hits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    hit_key VARCHAR(190) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX (hit_key, created_at)
) ENGINE=InnoDB;

CREATE TABLE audit_log (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    detail TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX (created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Data awal
INSERT INTO branches (branch_name, address, phone, email) VALUES
('Cabang Surabaya', 'Jl. Keputih Tegal No. 10, Surabaya', '031555001', 'surabaya@bimbingku.test'),
('Cabang Malang',   'Jl. Soekarno Hatta No. 25, Malang',  '034155002', 'malang@bimbingku.test'),
('Cabang Jember',   'Jl. Kalimantan No. 7, Jember',       '033155003', 'jember@bimbingku.test');

INSERT INTO classes (class_name, branch_id, schedule_day, schedule_time, quota, fee) VALUES
('Matematika Dasar', 1, 'Senin & Rabu',  '15.00-16.30', 20, 250000),
('Bahasa Inggris',   1, 'Selasa & Kamis','15.00-16.30', 20, 250000),
('Pemrograman Web',  1, 'Sabtu',         '09.00-11.00', 15, 350000),
('Matematika Dasar', 2, 'Senin & Rabu',  '16.00-17.30', 20, 225000),
('Bahasa Inggris',   2, 'Selasa & Kamis','16.00-17.30', 20, 225000),
('Pemrograman Web',  3, 'Minggu',        '09.00-11.00', 15, 300000);

-- Akun admin awal: admin@bimbingku.test / password (wajib diganti saat login pertama)
INSERT INTO users (nama, email, password, role, must_change_password) VALUES
('Administrator', 'admin@bimbingku.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'admin', 1);
