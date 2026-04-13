Project: Connect 4 Web Application (Phase 1: Lobby)

--- OVERVIEW ---
This is a turn-based implementation of Connect 4. Players can register, log in, 
and challenge other users from a central lobby. The lobby tracks pending 
challenges, active games (with turn indicators), and match history.

The game board is represented as a 42-character string in the database, 
mapping to a 7-column by 6-row grid. Turns are managed via a current_turn_id
system to ensure only one player can move at a time.

--- WEB APP URL ---
https://cs.gettysburg.edu/~sackjo02/lobbyGame/lobby.php

--- DATABASE SCHEMA ---

-- Existing Users Table
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(64) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (username)
);

-- Challenges Table
CREATE TABLE challenges (
    id INT NOT NULL AUTO_INCREMENT,
    challenger_id INT NOT NULL,
    challenged_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    PRIMARY KEY (id),
    FOREIGN KEY (challenger_id) REFERENCES users(id),
    FOREIGN KEY (challenged_id) REFERENCES users(id)
);

-- Games Table
CREATE TABLE games (
    game_id INT NOT NULL AUTO_INCREMENT,
    player1_id INT NOT NULL,
    player2_id INT NOT NULL,
    current_turn_id INT NOT NULL,
    board_state VARCHAR(42) DEFAULT '000000000000000000000000000000000000000000', 
    status ENUM('active', 'completed', 'forfeited') DEFAULT 'active',
    winner_id INT DEFAULT NULL,
    PRIMARY KEY (game_id),
    FOREIGN KEY (player1_id) REFERENCES users(id),
    FOREIGN KEY (player2_id) REFERENCES users(id)
);

--- INITIAL DATA & PERMISSIONS ---
GRANT SELECT, INSERT, UPDATE, DELETE ON s26_sackjo02.* TO sackjo02_web;

--- FILES INCLUDED ---
- lobby.php: Main navigation, challenge management, and game lists.
- game.php: Visual draft of the 7x6 Connect 4 board grid.
- accept_challenge.php: Logic to initialize a new game match.
- db_connect.php: Shared database connection configuration.
- register.php / login.php / logout.php: Authentication system.
- decline_challenge.php: Logic to decline a challenge.


- Set permission for the folder
chmod 755 webApp

- Set permission for the files inside the folder
chmod 644 *.php

