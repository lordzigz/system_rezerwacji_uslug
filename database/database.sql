-- Baza danych dla projektu: Studio Włosów Siedlce
-- Autorzy: Wiktor Szymański
-- Data: 2026

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS studio_wlosow;
CREATE DATABASE studio_wlosow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studio_wlosow;

-- --------------------------------------------------------
-- Tabela: users
-- --------------------------------------------------------
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  surname VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  role ENUM('klient','pracownik','admin') NOT NULL DEFAULT 'klient',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabela: service_categories
-- --------------------------------------------------------
CREATE TABLE service_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabela: services
-- --------------------------------------------------------
CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  duration INT NOT NULL COMMENT 'czas trwania w minutach',
  price DECIMAL(10,2) NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_services_category
    FOREIGN KEY (category_id) REFERENCES service_categories(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabela: employee_services (relacja N:M)
-- --------------------------------------------------------
CREATE TABLE employee_services (
  employee_id INT NOT NULL,
  service_id INT NOT NULL,
  PRIMARY KEY (employee_id, service_id),
  CONSTRAINT fk_es_employee
    FOREIGN KEY (employee_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_es_service
    FOREIGN KEY (service_id) REFERENCES services(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabela: employee_availability
-- --------------------------------------------------------
CREATE TABLE employee_availability (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NOT NULL,
  day_of_week TINYINT NOT NULL COMMENT '1=poniedziałek, 7=niedziela',
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  CONSTRAINT fk_av_employee
    FOREIGN KEY (employee_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabela: reservations
-- --------------------------------------------------------
CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  employee_id INT NOT NULL,
  service_id INT NOT NULL,
  reservation_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  status ENUM('oczekujaca','potwierdzona','zrealizowana','anulowana')
    NOT NULL DEFAULT 'oczekujaca',
  comment TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_res_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_res_employee
    FOREIGN KEY (employee_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_res_service
    FOREIGN KEY (service_id) REFERENCES services(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_res_employee_date ON reservations (employee_id, reservation_date);
CREATE INDEX idx_res_user ON reservations (user_id);
CREATE INDEX idx_res_status ON reservations (status);

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Dane testowe
-- --------------------------------------------------------

INSERT INTO users (name, surname, email, password, phone, role) VALUES
('Adam',   'Administrator', 'admin@studiowlosew.pl', '$2y$10$abcdefghijklmnopqrstuvABCDEFGHIJKLMNOPQRSTUVWXYZ012345', '250000000', 'admin'),
('Anna',   'Kowalska',      'anna@studiowlosew.pl',  '$2y$10$abcdefghijklmnopqrstuvABCDEFGHIJKLMNOPQRSTUVWXYZ012345', '251111111', 'pracownik'),
('Marta',  'Wiśniewska',    'marta@studiowlosew.pl', '$2y$10$abcdefghijklmnopqrstuvABCDEFGHIJKLMNOPQRSTUVWXYZ012345', '252222222', 'pracownik'),
('Piotr',  'Zieliński',     'piotr@studiowlosew.pl', '$2y$10$abcdefghijklmnopqrstuvABCDEFGHIJKLMNOPQRSTUVWXYZ012345', '253333333', 'pracownik'),
('Kasia',  'Nowak',         'klient@example.com',    '$2y$10$abcdefghijklmnopqrstuvABCDEFGHIJKLMNOPQRSTUVWXYZ012345', '254444444', 'klient');

INSERT INTO service_categories (name, description) VALUES
('Strzyżenie',   'Strzyżenie damskie, męskie i dziecięce'),
('Koloryzacja',  'Farbowanie, baleyage, refleksy'),
('Pielęgnacja',  'Zabiegi regenerujące i pielęgnacyjne'),
('Stylizacja',   'Modelowanie i upięcia okolicznościowe');

INSERT INTO services (category_id, name, description, duration, price) VALUES
(1, 'Strzyżenie damskie',        'Strzyżenie z myciem i modelowaniem',        60, 120.00),
(1, 'Strzyżenie męskie',         'Strzyżenie męskie z myciem',                30,  60.00),
(1, 'Strzyżenie dziecięce',      'Strzyżenie dla dzieci do 12 lat',           30,  40.00),
(2, 'Koloryzacja całościowa',    'Farbowanie całych włosów',                 120, 250.00),
(2, 'Baleyage',                  'Rozjaśnianie pasemek techniką baleyage',   180, 400.00),
(2, 'Refleksy',                  'Pasemka refleksyjne',                       90, 200.00),
(3, 'Botox na włosy',            'Zabieg regenerujący włosy',                 90, 200.00),
(3, 'Keratynowe prostowanie',    'Prostowanie keratynowe',                   120, 300.00),
(4, 'Modelowanie',               'Modelowanie włosów',                        30,  50.00),
(4, 'Upięcie okolicznościowe',   'Upięcie na wesele lub inną okazję',         90, 180.00);

INSERT INTO employee_services (employee_id, service_id) VALUES
(2, 1), (2, 2), (2, 3), (2, 9), (2, 10),
(3, 4), (3, 5), (3, 6),
(4, 2), (4, 9);

INSERT INTO employee_availability (employee_id, day_of_week, start_time, end_time) VALUES
(2,1,'09:00','19:00'),(2,2,'09:00','19:00'),(2,3,'09:00','19:00'),
(2,4,'09:00','19:00'),(2,5,'09:00','19:00'),(2,6,'09:00','15:00'),
(3,1,'09:00','19:00'),(3,2,'09:00','19:00'),(3,3,'09:00','19:00'),
(3,4,'09:00','19:00'),(3,5,'09:00','19:00'),(3,6,'09:00','15:00'),
(4,1,'09:00','19:00'),(4,2,'09:00','19:00'),(4,3,'09:00','19:00'),
(4,4,'09:00','19:00'),(4,5,'09:00','19:00'),(4,6,'09:00','15:00');

INSERT INTO reservations
(user_id, employee_id, service_id, reservation_date, start_time, end_time, status, comment) VALUES
(5, 2, 1, '2026-11-05', '10:00', '11:00', 'potwierdzona', 'Proszę o krótsze włosy'),
(5, 3, 4, '2026-11-12', '12:00', '14:00', 'oczekujaca',   NULL);