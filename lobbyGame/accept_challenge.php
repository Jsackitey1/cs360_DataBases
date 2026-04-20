<?php
session_start();
include('db_connect.php');

// 1. Security Check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $challenge_id = $_GET['id'];
    $my_id = $_SESSION['user_id'];

    // 2. Fetch the challenge details to ensure it belongs to the logged-in user and is still pending
    $query = "SELECT challenger_id, challenged_id FROM challenges WHERE id = ? AND challenged_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $challenge_id, $my_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $challenge = $result->fetch_assoc();
        $challenger_id = $challenge['challenger_id'];

        $conn->begin_transaction();

        try {
            $update_sql = "UPDATE challenges SET status = 'accepted' WHERE id = ?";
            $upd_stmt = $conn->prepare($update_sql);
            $upd_stmt->bind_param("i", $challenge_id);
            $upd_stmt->execute();

            // Create the new game
            $initial_board = str_repeat("0", 42);
            $game_sql = "INSERT INTO games (player1_id, player2_id, current_turn_id, board_state, status) 
                         VALUES (?, ?, ?, ?, 'active')";

            $game_stmt = $conn->prepare($game_sql);
            $game_stmt->bind_param("iiis", $challenger_id, $my_id, $challenger_id, $initial_board);
            $game_stmt->execute();

            $new_game_id = $conn->insert_id;

            // Commit the changes
            $conn->commit();

            header("Location: game.php?id=" . $new_game_id);
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            die("Error creating game: " . $e->getMessage());
        }
    } else {
        die("Invalid challenge ID.");
    }
} else {
    header("Location: lobby.php");
    exit;
}
?>