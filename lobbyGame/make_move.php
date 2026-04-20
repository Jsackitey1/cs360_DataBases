<?php
session_start();
include('db_connect.php');

if (!isset($_POST['game_id']) || !isset($_POST['col'])) {
    // Fallback to GET just in case someone refreshes, redirect to lobby
    if (isset($_GET['game_id'])) {
        header("Location: game.php?id=" . intval($_GET['game_id']));
        exit;
    }
    header("Location: lobby.php");
    exit;
}

$game_id = intval($_POST['game_id']);
$col = intval($_POST['col']);
$my_id = $_SESSION['user_id'];

// 1. Fetch game state and verify it's your turn
$res = $conn->query("SELECT * FROM games WHERE game_id = $game_id");
$game = $res->fetch_assoc();

if ($game['current_turn_id'] != $my_id || $game['status'] != 'active') {
    // Not your turn or game already over
    header("Location: game.php?id=$game_id");
    exit;
}

$board = $game['board_state'];
$player_num = ($my_id == $game['player1_id']) ? '1' : '2';

// 2. Find the lowest empty row (Gravity)
$target_row = -1;
for ($r = 5; $r >= 0; $r--) {
    if ($board[($r * 7) + $col] == '0') {
        $target_row = $r;
        break;
    }
}

if ($target_row == -1) {
    header("Location: game.php?id=$game_id");
    exit;
}

// 3. Update board string
$board[($target_row * 7) + $col] = $player_num;

// 4. Check Win Conditions BEFORE updating database turn to next player
function checkWin($board, $p)
{
    // 1. Horizontal Check
    for ($r = 0; $r < 6; $r++) {
        for ($c = 0; $c < 4; $c++) {
            if (
                $board[$r * 7 + $c] == $p && $board[$r * 7 + $c + 1] == $p &&
                $board[$r * 7 + $c + 2] == $p && $board[$r * 7 + $c + 3] == $p
            )
                return true;
        }
    }
    // 2. Vertical Check
    for ($r = 0; $r < 3; $r++) {
        for ($c = 0; $c < 7; $c++) {
            if (
                $board[$r * 7 + $c] == $p && $board[($r + 1) * 7 + $c] == $p &&
                $board[($r + 2) * 7 + $c] == $p && $board[($r + 3) * 7 + $c] == $p
            )
                return true;
        }
    }
    // 3. Diagonal (Down-Right)
    for ($r = 0; $r < 3; $r++) {
        for ($c = 0; $c < 4; $c++) {
            if (
                $board[$r * 7 + $c] == $p && $board[($r + 1) * 7 + $c + 1] == $p &&
                $board[($r + 2) * 7 + $c + 2] == $p && $board[($r + 3) * 7 + $c + 3] == $p
            )
                return true;
        }
    }
    // 4. Diagonal (Up-Right)
    for ($r = 3; $r < 6; $r++) {
        for ($c = 0; $c < 4; $c++) {
            if (
                $board[$r * 7 + $c] == $p && $board[($r - 1) * 7 + $c + 1] == $p &&
                $board[($r - 2) * 7 + $c + 2] == $p && $board[($r - 3) * 7 + $c + 3] == $p
            )
                return true;
        }
    }
    return false;
}

if (checkWin($board, $player_num)) {
    // If it's a win, mark game as completed and delete moves data
    $empty_board = '';
    $update = $conn->prepare("UPDATE games SET board_state = ?, status = 'completed', winner_id = ? WHERE game_id = ?");
    $update->bind_param("sii", $empty_board, $my_id, $game_id);
    $update->execute();
} elseif (strpos($board, '0') === false) {
    // If board is completely full and no win, it's a draw. Delete moves data.
    $empty_board = '';
    $null_winner = NULL;
    $update = $conn->prepare("UPDATE games SET board_state = ?, status = 'completed', winner_id = ? WHERE game_id = ?");
    $update->bind_param("sii", $empty_board, $null_winner, $game_id);
    $update->execute();
} else {
    // If no win and not full, switch turn and update board
    $next_turn = ($my_id == $game['player1_id']) ? $game['player2_id'] : $game['player1_id'];
    $update = $conn->prepare("UPDATE games SET board_state = ?, current_turn_id = ? WHERE game_id = ?");
    $update->bind_param("sii", $board, $next_turn, $game_id);
    $update->execute();
}

header("Location: game.php?id=$game_id");
exit;
?>