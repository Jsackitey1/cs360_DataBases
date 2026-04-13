<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

$game_id = $_GET['id'];
$my_id = $_SESSION['user_id'];

// Fetch game data
$sql = "SELECT * FROM games WHERE game_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

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
            <?php for ($c = 0; $c < 7; $c++): ?>
                <th><a href="make_move.php?game_id=<?php echo $game_id; ?>&col=<?php echo $c; ?>"
                        style="color: white;">↓</a></th>
            <?php endfor; ?>
        </tr>

        <?php
        // Render the 6 rows
        for ($r = 0; $r < 6; $r++) {
            echo "<tr>";
            for ($c = 0; $c < 7; $c++) {
                $index = ($r * 7) + $c;
                $val = $board[$index];
                $class = ($val == '1') ? 'p1' : (($val == '2') ? 'p2' : '');
                echo "<td class='cell'><div class='$class' style='width:40px; height:40px; margin:auto;'></div></td>";
            }
            echo "</tr>";
        }
        ?>
    </table>

    <p><?php echo ($game['current_turn_id'] == $my_id) ? "<strong>It's your turn!</strong>" : "Waiting for opponent..."; ?>
    </p>
</body>

</html>