<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

$game_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$my_id = $_SESSION['user_id'];

// Fetch game data and winner name if finished
$sql = "SELECT g.*, w.username as winner_name FROM games g LEFT JOIN users w ON g.winner_id = w.id WHERE g.game_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Game not found!");
}

$game = $result->fetch_assoc();
$board = $game['board_state']; // The 42-character string
?>

<!DOCTYPE html>
<html>

<head>
    <title>Connect 4 - Game #<?php echo $game_id; ?></title>
    <style>
        .cell {
            width: 50px;
            height: 50px;
            text-align: center;
            border: 1px solid black;
        }

        .p1 {
            background-color: red;
            border-radius: 50%;
        }

        .p2 {
            background-color: yellow;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <h1>Connect 4</h1>
    <p><a href="lobby.php">Back to Lobby</a></p>

    <table border="0" style="background-color: blue; padding: 10px;">
        <tr>
            <?php if ($game['status'] == 'active'): ?>
                <?php for ($c = 0; $c < 7; $c++): ?>
                    <th>
                        <form action="make_move.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
                            <input type="hidden" name="col" value="<?php echo $c; ?>">
                            <button type="submit"
                                style="background: none; border: none; color: white; cursor: pointer; font-size: inherit; font-weight: bold; text-decoration: underline;">↓</button>
                        </form>
                    </th>
                <?php endfor; ?>
            <?php else: ?>
                <?php for ($c = 0; $c < 7; $c++): ?>
                    <th>&nbsp;</th>
                <?php endfor; ?>
            <?php endif; ?>
        </tr>

        <?php
        // Render the 6 rows
        for ($r = 0; $r < 6; $r++) {
            echo "<tr>";
            for ($c = 0; $c < 7; $c++) {
                $index = ($r * 7) + $c;
                $val = ($board !== '' && strlen($board) > $index) ? $board[$index] : '0';
                $class = ($val == '1') ? 'p1' : (($val == '2') ? 'p2' : '');
                echo "<td class='cell'><div class='$class' style='width:40px; height:40px; margin:auto;'></div></td>";
            }
            echo "</tr>";
        }
        ?>
    </table>

    <br>

    <?php if ($game['status'] == 'active'): ?>
        <form action="forfeit.php" method="POST">
            <input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
            <input type="submit" value="Forfeit Game" onclick="return confirm('Are you sure you want to quit?');">
        </form>

        <p><?php echo ($game['current_turn_id'] == $my_id) ? "<strong>It's your turn!</strong>" : "Waiting for opponent..."; ?>
        </p>
    <?php else: ?>
        <h2>Game Over!</h2>
        <p>
            <?php
            if ($game['winner_id']) {
                echo "Winner: <strong>" . htmlspecialchars($game['winner_name']) . "</strong>";
            } else {
                echo "<strong>It's a draw!</strong>";
            }
            ?>
        </p>
    <?php endif; ?>
</body>

</html>