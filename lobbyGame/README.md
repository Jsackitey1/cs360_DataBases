https://cs.gettysburg.edu/~sackjo02/webApp/register.php

- SQL command used to create the users table

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(64) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (username)
);

-- 1. Challenges: Tracks invites between players

CREATE TABLE challenges (
    id INT NOT NULL AUTO_INCREMENT,
    challenger_id INT NOT NULL,
    challenged_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    PRIMARY KEY (id),
    FOREIGN KEY (challenger_id) REFERENCES users(id),
    FOREIGN KEY (challenged_id) REFERENCES users(id)
);

-- 2. Games: Tracks active matches

CREATE TABLE games (
    game_id INT NOT NULL AUTO_INCREMENT,
    player1_id INT NOT NULL,
    player2_id INT NOT NULL,
    current_turn_id INT NOT NULL,
    board_state VARCHAR(42) DEFAULT '000000000000000000000000000000000000000000', 
    status ENUM('active', 'completed', 'forfeited') DEFAULT 'active',
    winner_id INT DEFAULT NULL,
    PRIMARY KEY (game_id)
);

- Note: The board_state is stored as a 42-character string (7 columns × 6 rows). 0 is empty, 1 is Player 1, 2 is Player 2.

- grant permission to sackjo02_web to access the users table

grant select, insert on users to sackjo02_web;

- Set permission for the folder
chmod 755 webApp

- Set permission for the files inside the folder
chmod 644 *.php

