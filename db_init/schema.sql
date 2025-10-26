-- Full schema for robust primary/secondary_units maintenance system
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    privilege ENUM('user','admin') DEFAULT 'user',
    email VARCHAR(100),
    nickname VARCHAR(100),
    api_key VARCHAR(255) UNIQUE,
    theme VARCHAR(20) DEFAULT 'theme_1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_id INT,
    equipment_level INT DEFAULT 1,
    archived TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_level INT DEFAULT 1,
    label VARCHAR(100) NOT NULL,
    type ENUM('string','text','number','date','multi_choice') NOT NULL,
    options VARCHAR(255) DEFAULT NULL,
    position INT NOT NULL DEFAULT 0
);

CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    question_id INT NOT NULL,
    value TEXT,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
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

CREATE TABLE IF NOT EXISTS admin_message (
    id INT PRIMARY KEY AUTO_INCREMENT,
    message TEXT,
    active TINYINT(1) DEFAULT 0,
    UNIQUE KEY unique_active (active),
    performed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin_message (message, active) VALUES (
    'Welcome to the Maintenance Tracker. This is in its early stage and just putting the nuts and bolts together and will go this as I use it more. To Change this message go to the admin settings under site config.',
    1
);

-- Insert default config row if not exists
INSERT INTO admin_config (config_name, config_value)
    VALUES ('default_theme', 'theme_1'),
           ('columns_to_show', '3'),
           ('primary_unit', 'Primary'),
           ('secondary_unit', 'Secondary'),
           ('version', '1.0.1');

-- Insert default admin user (password: changeme)
INSERT INTO users (username, password, privilege, email, nickname) VALUES (
  'admin',
  '$2y$10$ZbPOgCEetIZCcTO2PmeZYO6K9igD23CXK4YeXunxJGtPpwWWllFO2', -- Hash for 'changeme'
  'admin',
  'admin@example.com',
  'Administrator'
);