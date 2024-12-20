CREATE DATABASE fyp;

-- Switch to the new database
USE fyp;

-- Create the user table
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    otp_secret VARCHAR(100),
    profile_pic VARCHAR(100)
);

CREATE TABLE item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    item_description TEXT NOT NULL,
    item_type VARCHAR(50) NOT NULL, -- e.g., weapon, armor, potion, etc.
    item_value INT NOT NULL, -- e.g., price or value of the item
    item_effect VARCHAR(100) NOT NULL,
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

CREATE TABLE character_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    health INT DEFAULT 100,
    attack_power INT DEFAULT 10,
    defense INT DEFAULT 5,
    level INT DEFAULT 1,
    experience INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

-- Add items for Level 1
INSERT INTO item (item_name, item_description, item_type, item_value, item_effect)
VALUES 
    ('Wooden Sword', 'A basic wooden sword.', 'weapon', 50, '5'), 
    ('Iron Sword', 'An iron sword with better damage.', 'weapon', 100, '10'),
    ('Leather Armor', 'Basic leather armor.', 'armor', 60, '3'), 
    ('Iron Armor', 'Sturdier iron armor.', 'armor', 120, '7'),
    ('Health Potion', 'Restores 20 HP.', 'potion', 30, '20'),
    ('Attack Potion', 'Increases attack by 5.', 'potion', 40, '5'),
    ('Defense Potion', 'Increases defense by 5.', 'potion', 40, '5');

-- Add items for Level 2
INSERT INTO item (item_name, item_description, item_type, item_value, item_effect)
VALUES 
    ('Steel Sword', 'A steel sword with increased damage.', 'weapon', 80, '15'), 
    ('Golden Sword', 'A golden sword with high damage.', 'weapon', 160, '20'),
    ('Chain Mail', 'Chain mail armor for better protection.', 'armor', 80, '5'), 
    ('Steel Armor', 'Steel armor for strong defense.', 'armor', 160, '10'),
    ('Greater Health Potion', 'Restores 40 HP.', 'potion', 60, '40'),
    ('Greater Attack Potion', 'Increases attack by 10.', 'potion', 70, '10'),
    ('Greater Defense Potion', 'Increases defense by 10.', 'potion', 70, '10');

-- Add items for Level 3
INSERT INTO item (item_name, item_description, item_type, item_value, item_effect)
VALUES 
    ('Dragon Sword', 'A sword imbued with dragon magic.', 'weapon', 120, '25'), 
    ('Magic Sword', 'A sword with magical properties.', 'weapon', 240, '30'),
    ('Dragon Mail', 'Mail forged by dragon fire.', 'armor', 120, '10'), 
    ('Magic Armor', 'Armor with magical enhancements.', 'armor', 240, '15'),
    ('Superior Health Potion', 'Restores 60 HP.', 'potion', 90, '60'),
    ('Superior Attack Potion', 'Increases attack by 15.', 'potion', 100, '15'),
    ('Superior Defense Potion', 'Increases defense by 15.', 'potion', 100, '15');

-- Add items for Level 4
INSERT INTO item (item_name, item_description, item_type, item_value, item_effect)
VALUES 
    ('Excalibur', 'The legendary sword of kings.', 'weapon', 150, '40'), 
    ('Holy Sword', 'A sword with divine power.', 'weapon', 300, '50'),
    ('Holy Armor', 'Armor blessed by the gods.', 'armor', 150, '20'), 
    ('Dragon Scale Armor', 'Armor made from dragon scales.', 'armor', 300, '25'),
    ('Ultimate Health Potion', 'Restores 100 HP.', 'potion', 120, '100'),
    ('Ultimate Attack Potion', 'Increases attack by 25.', 'potion', 150, '25'),
    ('Ultimate Defense Potion', 'Increases defense by 25.', 'potion', 150, '25');
    
select * from inventory;

UPDATE user
SET email = "testing@gmail.com"
WHERE id=2;

DELETE FROM inventory WHERE id = 19;
SELECT i.id, i.item_name, i.item_description, i.item_type, i.item_value, i.item_effect, inv.quantity
    FROM inventory inv
    JOIN item i ON inv.item_id = i.id
    WHERE inv.user_id = 1 AND inv.quantity > 0
    ORDER BY i.item_name;
UPDATE user
SET profile_pic = "profilePicture/profilePic"
WHERE id = 1;

DROP DATABASE fyp;
INSERT INTO inventory (user_id, item_id, quantity)
VALUES 
    ('1', '1', 1),
    ('1', '2', 1);
