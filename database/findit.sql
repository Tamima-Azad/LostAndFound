-- USERS TABLE
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    contact_no VARCHAR(30),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- CLAIMS TABLE (optional, for dashboard claims section)
CREATE TABLE claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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