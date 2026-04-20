<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['loggedin'])) {
    die("Not logged in");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
    $my_id = $_SESSION['user_id'];

    // Find who the opponent is
    $stmt = $conn->prepare("SELECT player1_id, player2_id, status FROM games WHERE game_id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        die("Game not found.");
    }

    $game = $res->fetch_assoc();

    if ($game['status'] != 'active') {
        header("Location: lobby.php?msg=Game already completed");
        exit;
    }

    if ($my_id != $game['player1_id'] && $my_id != $game['player2_id']) {
        die("You are not part of this game.");
    }

    $opponent_id = ($my_id == $game['player1_id']) ? $game['player2_id'] : $game['player1_id'];

    // Mark game as completed, award win to opponent, and delete moves data
    $empty_board = '';
    $update = $conn->prepare("UPDATE games SET board_state = ?, status = 'completed', winner_id = ? WHERE game_id = ?");
    $update->bind_param("sii", $empty_board, $opponent_id, $game_id);
    $update->execute();

    header("Location: lobby.php?msg=Game Forfeited");
}
?>