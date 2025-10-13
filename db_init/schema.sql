-- Full schema for robust primary/secondary_units maintenance system
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    privilege ENUM('user','admin') DEFAULT 'user',
    email VARCHAR(100),
    nickname VARCHAR(100),
    theme VARCHAR(20) DEFAULT 'theme_1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE primary_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pmy_id INT,
    archived TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE secondary_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pmy_id INT NULL,
    archived TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE primary_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    type ENUM('string','text','number','date','multi_choice') NOT NULL,
    options VARCHAR(255) DEFAULT NULL,
    position INT NOT NULL DEFAULT 0
);

CREATE TABLE primary_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    primary_id INT NOT NULL,
    question_id INT NOT NULL,
    value TEXT,
    FOREIGN KEY (primary_id) REFERENCES primary_units(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES primary_questions(id) ON DELETE CASCADE
);

CREATE TABLE secondary_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    type ENUM('string','text','number','date','multi_choice') NOT NULL,
    options VARCHAR(255) DEFAULT NULL,
    position INT NOT NULL DEFAULT 0
);

CREATE TABLE secondary_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    secondary_id INT NOT NULL,
    question_id INT NOT NULL,
    value TEXT,
    FOREIGN KEY (secondary_id) REFERENCES secondary_units(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES secondary_questions(id) ON DELETE CASCADE
);

CREATE TABLE photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_type ENUM('primary_units','secondary_units'),
    unit_id INT NOT NULL,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    filename VARCHAR(255)
);

CREATE TABLE maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    secondary_id INT NULL,
    pmy_id INT NULL,
    type_of_service VARCHAR(100),
    description TEXT,
    costs_of_parts DECIMAL(10,2),
    performed_at DATE,
    performed_by VARCHAR(100),
    photos JSON NULL
);

CREATE TABLE IF NOT EXISTS admin_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    config_name VARCHAR(32) NOT NULL UNIQUE,
    config_value VARCHAR(32) NOT NULL
);

-- Insert default config row if not exists
INSERT INTO admin_config (config_name, config_value)
    VALUES ('default_theme', 'theme_1'),
           ('columns_to_show', '3'),
           ('primary_unit', 'Primary'),
           ('secondary_unit', 'Secondary');

-- Insert default admin user (password: changeme)
INSERT INTO users (username, password, privilege, email, nickname) VALUES (
  'admin',
  '$2y$10$ZbPOgCEetIZCcTO2PmeZYO6K9igD23CXK4YeXunxJGtPpwWWllFO2', -- Hash for 'changeme'
  'admin',
  'admin@example.com',
  'Administrator'
);