-- Create the database
CREATE DATABASE fyp;

-- Switch to the new database
USE fyp;

-- Create the user table
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    item_description TEXT,
    item_type VARCHAR(50), -- e.g., weapon, armor, potion, etc.
    item_value INT, -- e.g., price or value of the item
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT DEFAULT 1,
    acquired_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (item_id) REFERENCES item(id)
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT, -- User who sends the item
    receiver_id INT, -- User who receives the item
    item_id INT NOT NULL,
    quantity INT DEFAULT 1,
    transaction_type VARCHAR(50), -- e.g., "trade", "purchase", "gift"
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES user(id),
    FOREIGN KEY (receiver_id) REFERENCES user(id),
    FOREIGN KEY (item_id) REFERENCES item(id)
);

CREATE TABLE otp_verification (
    email VARCHAR(100) PRIMARY KEY,
    otp VARCHAR(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

