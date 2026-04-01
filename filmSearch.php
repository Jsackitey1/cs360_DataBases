//Joseph Sackitey

<!DOCTYPE html>
<html>

<head>
    <title>Film Search</title>
</head>

<body>
    <h2>Search for a Film</h2>
    <form action="filmSearch.php" method="GET">
        Title: <input type="text" name="title">
        Rating:
        <select name="rating">
            <option value="">Any</option>
            <option value="G">G</option>
            <option value="PG">PG</option>
            <option value="PG-13">PG-13</option>
            <option value="R">R</option>
            <option value="NC-17">NC-17</option>
        </select>
        <input type="submit" value="Search">
    </form>

    <?php
    // Only run the search if at least one field is submitted
    if (isset($_GET['title']) || isset($_GET['rating'])) {
        // Database Credentials
        $db_hostname = 'cray';
        $db_database = 'sakila';
        $db_username = 'sackjo02_web';
        $db_password = '';

        $conn = new mysqli(
            $db_hostname,
            $db_username,
            $db_password
        );

        // Select the database
        $conn->select_db($db_database) or
            die("Unable to select database.");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // 3. Prepare the Search Parameters
        $title = "%" . $_GET['title'] . "%";
        $rating = $_GET['rating'];

        // 4. Build the SQL with Prepared Statement Placeholders (?)
        // We use "LIKE" for the title and "=" for the rating
        $sql = "SELECT title, description, release_year, rating FROM film WHERE title LIKE ? AND rating LIKE ?";

        // If rating is empty, we match anything using the '%' wildcard
        if (empty($rating)) {
            $rating = "%";
        }

        // 5. Prepare and Execute
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $title, $rating);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h3>Search Results:</h3>";
                echo "<table border='1'><tr><th>Title</th><th>Rating</th><th>Year</th><th>Description</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["title"] . "</td>";
                    echo "<td>" . $row["rating"] . "</td>";
                    echo "<td>" . $row["release_year"] . "</td>";
                    echo "<td>" . $row["description"] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No films found matching those criteria.</p>";
            }
            $stmt->close();
        }
        $conn->close();
    }
    ?>
</body>

</html>