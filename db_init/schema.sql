-- Full schema for robust trailer/refrigeration maintenance system
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    privilege ENUM('user','admin') DEFAULT 'user',
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE trailers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trl_id INT,
    archived TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE refrigeration (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trl_id INT NULL,
    archived TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE trailer_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    type ENUM('string','text','number','date','multi_choice') NOT NULL,
    options VARCHAR(255) DEFAULT NULL,
    position INT NOT NULL DEFAULT 0
);

CREATE TABLE trailer_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trailer_id INT NOT NULL,
    question_id INT NOT NULL,
    value TEXT,
    FOREIGN KEY (trailer_id) REFERENCES trailers(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES trailer_questions(id) ON DELETE CASCADE
);

CREATE TABLE refrigeration_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    type ENUM('string','text','number','date','multi_choice') NOT NULL,
    options VARCHAR(255) DEFAULT NULL,
    position INT NOT NULL DEFAULT 0
);

CREATE TABLE refrigeration_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    refrigeration_id INT NOT NULL,
    question_id INT NOT NULL,
    value TEXT,
    FOREIGN KEY (refrigeration_id) REFERENCES refrigeration(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES refrigeration_questions(id) ON DELETE CASCADE
);

CREATE TABLE photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_type ENUM('trailer','refrigeration'),
    unit_id INT NOT NULL,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    filename VARCHAR(255)
);

CREATE TABLE maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    refrigeration_id INT NULL,
    trl_id INT NULL,
    type_of_service VARCHAR(100),
    description TEXT,
    costs_of_parts DECIMAL(10,2),
    performed_at DATE,
    performed_by VARCHAR(100),
    photos JSON NULL
);

-- Insert default admin user (password: changeme)
INSERT INTO users (username, password, privilege, email) VALUES (
  'admin',
  '$2y$10$ZbPOgCEetIZCcTO2PmeZYO6K9igD23CXK4YeXunxJGtPpwWWllFO2', -- Hash for 'changeme'
  'admin',
  'admin@example.com'
);