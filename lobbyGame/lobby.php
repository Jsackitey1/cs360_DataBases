<?php
session_start();
include('db_connect.php');

// 1. Security Check: Redirect to login if not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$my_id = $_SESSION['user_id'];
$my_name = $_SESSION['username'];

// 2. Fetch Active Games (NEW)
$games_sql = "SELECT g.game_id, u1.username AS p1_name, u2.username AS p2_name, g.current_turn_id 
              FROM games g
              JOIN users u1 ON g.player1_id = u1.id
              JOIN users u2 ON g.player2_id = u2.id
              WHERE (g.player1_id = ? OR g.player2_id = ?) AND g.status = 'active'";
$g_stmt = $conn->prepare($games_sql);
$g_stmt->bind_param("ii", $my_id, $my_id);
$g_stmt->execute();
$active_games = $g_stmt->get_result();

// Fetch Results from Previous Games
$history_sql = "SELECT g.game_id, u1.username AS p1_name, u2.username AS p2_name, u3.username AS winner_name, g.status
                FROM games g
                JOIN users u1 ON g.player1_id = u1.id
                JOIN users u2 ON g.player2_id = u2.id
                LEFT JOIN users u3 ON g.winner_id = u3.id
                WHERE (g.player1_id = ? OR g.player2_id = ?) AND g.status != 'active'
                ORDER BY g.game_id DESC";
$h_stmt = $conn->prepare($history_sql);
$h_stmt->bind_param("ii", $my_id, $my_id);
$h_stmt->execute();
$game_history = $h_stmt->get_result();

// 3. Handle "Sending a Challenge"
if (isset($_GET['challenge'])) {
    $target_id = $_GET['challenge'];
    $sql = "INSERT INTO challenges (challenger_id, challenged_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $my_id, $target_id);
    $stmt->execute();
    header("Location: lobby.php?msg=Challenge Sent!");
    exit;
}

// 4. Fetch all other users
$users_result = $conn->query("SELECT id, username FROM users WHERE id != $my_id");

// 5. Fetch incoming challenges
$challenges_sql = "SELECT c.id, u.username FROM challenges c 
                   JOIN users u ON c.challenger_id = u.id 
                   WHERE c.challenged_id = ? AND c.status = 'pending'";
$c_stmt = $conn->prepare($challenges_sql);
$c_stmt->bind_param("i", $my_id);
$c_stmt->execute();
$incoming_challenges = $c_stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Connect 4 Lobby</title>
</head>

<body>
    <h1>Welcome, <?php echo htmlspecialchars($my_name); ?>!</h1>
    <a href="logout.php">Logout</a>
    <hr>

    <h3>Incoming Challenges</h3>
    <?php if ($incoming_challenges->num_rows > 0): ?>
        <ul>
            <?php while ($row = $incoming_challenges->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($row['username']); ?></strong> challenged you!
                    <a href="accept_challenge.php?id=<?php echo $row['id']; ?>">[Accept]</a>
                    <a href="decline_challenge.php?id=<?php echo $row['id']; ?>">[Decline]</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No new challenges.</p>
    <?php endif; ?>

    <hr>

    <h3>Your Active Games</h3>
    <?php if ($active_games->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Opponent</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($game = $active_games->fetch_assoc()): ?>
                <?php
                $opponent = ($game['p1_name'] == $my_name) ? $game['p2_name'] : $game['p1_name'];
                $is_my_turn = ($game['current_turn_id'] == $my_id);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($opponent); ?></td>
                    <td style="color: <?php echo $is_my_turn ? 'green' : 'black'; ?>">
                        <?php echo $is_my_turn ? "<strong>Your Turn</strong>" : "Waiting..."; ?>
                    </td>
                    <td><a href="game.php?id=<?php echo $game['game_id']; ?>">View Game</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>You have no active games.</p>
    <?php endif; ?>

    <hr>

    <h3>All Players (Challenge Someone!)</h3>
    <table border="1">
        <tr>
            <th>Username</th>
            <th>Action</th>
        </tr>
        <?php while ($user = $users_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><a href="lobby.php?challenge=<?php echo $user['id']; ?>">Challenge</a></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <hr>
    <h3>Results from Previous Games</h3>
    <?php if ($game_history->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Match</th>
                <th>Result</th>
                <th>Status</th>
            </tr>
            <?php while ($history = $game_history->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($history['p1_name'] . " vs " . $history['p2_name']); ?></td>
                    <td>
                        <?php
                        if ($history['winner_name']) {
                            echo "Winner: <strong>" . htmlspecialchars($history['winner_name']) . "</strong>";
                        } else {
                            echo "Draw/No Winner";
                        }
                        ?>
                    </td>
                    <td><?php echo ucfirst($history['status']); ?></td>
                </tr>
                <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No completed games yet.</p>
    <?php endif; ?>
</body>

</html>