Project: Connect 4 Web Application

--- OVERVIEW ---
This is a turn-based implementation of Connect 4. Players can register, log in, 
and challenge other users from a central lobby. The lobby tracks pending 
challenges, active games (with turn indicators), and match history.

The game board is represented as a 42-character string in the database, 
mapping to a 7-column by 6-row grid. Turns are managed via a current_turn_id
system to ensure only one player can move at a time.

--- WEB APP URL ---
https://cs.gettysburg.edu/~sackjo02/clobbyGame/lobby.php

## 2. Instructions for Playing
- **Lobby:** Log in and challenge a player from the "All Players" list or accept an incoming challenge.
- **Making Moves:** Once a game is active, click the arrow (↓) above any of the 7 columns to drop your disc.
- **Gravity:** Discs will automatically fall to the lowest available row in the selected column.
- **Turn Management:** The game enforces turn-based play. You can only move when the status shows "Your Turn."
- **Winning:** The game automatically detects four-in-a-row (horizontal, vertical, or diagonal). When a win is detected, the game ends, and the result is posted to the lobby history.
- **Forfeiting:** If you wish to end a game early, click the "Forfeit Game" button. The win will be awarded to your opponent.

## 3. Database Schema

```sql
-- Users Table (Authentication)
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(64) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (username)
);

-- Challenges Table (Matchmaking)
CREATE TABLE challenges (
    id INT NOT NULL AUTO_INCREMENT,
    challenger_id INT NOT NULL,
    challenged_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    PRIMARY KEY (id),
    FOREIGN KEY (challenger_id) REFERENCES users(id),
    FOREIGN KEY (challenged_id) REFERENCES users(id)
);

-- Games Table (State & Logic)
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
chmod 755 lobbyGame

- Set permission for the files inside the folder
chmod 644 *.php

