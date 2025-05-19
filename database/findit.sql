-- USERS TABLE
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    contact_no VARCHAR(30),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(20) DEFAULT 'user'
);

-- LOST ITEMS TABLE
CREATE TABLE lost_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    location VARCHAR(255),
    date DATE,
    image1 VARCHAR(255),
    image2 VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'unclaimed',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- FOUND ITEMS TABLE
CREATE TABLE found_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    location VARCHAR(255),
    date DATE,
    image1 VARCHAR(255),
    image2 VARCHAR(255),
    found_area VARCHAR(255),
    found_city VARCHAR(100),
    found_state VARCHAR(100),
    found_date DATE,
    kept_address VARCHAR(255),
    kept_city VARCHAR(100),
    kept_state VARCHAR(100),
    kept_contact VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'unclaimed',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- CLAIMS TABLE (optional, for dashboard claims section)
CREATE TABLE claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    item_id INT NOT NULL,
    item_type ENUM('lost','found') NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'New',
    claim_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- EXCHANGE ITEMS TABLE
CREATE TABLE IF NOT EXISTS exchange_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    exchange_for VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Add a 'role' column to your users table if it doesn't exist
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user';

-- Create an admin user (replace email and password as needed)
INSERT INTO users (name, email, contact_no, password, role)
VALUES (
    'Admin',
    'admin@example.com',
    '9999999999',
    -- Use PHP's password_hash('yourpassword', PASSWORD_BCRYPT) to generate the hash
    'admin123', -- <-- replace with a real hash!
    'admin'
);
ALTER TABLE users ADD COLUMN reset_token VARCHAR(100), ADD COLUMN reset_expires DATETIME;
ALTER TABLE exchange_items ADD COLUMN image VARCHAR(255) DEFAULT NULL;

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exchange_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exchange_id) REFERENCES exchange_items(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);
ALTER TABLE messages ADD COLUMN parent_id INT DEFAULT 0;