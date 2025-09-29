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
    axles INT NOT NULL,
    door_type VARCHAR(50),
    length INT,
    archived TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE refrigeration (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trl_id INT NULL,
    model VARCHAR(100),
    serial VARCHAR(100),
    refrigerant VARCHAR(50),
    archived TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
