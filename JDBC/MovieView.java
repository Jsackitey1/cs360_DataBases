import java.sql.*;
import java.util.Scanner;
import java.util.ArrayList;
import java.util.List;

public class MovieView {

    // Variables - ensure these match your specific credentials if different from the example
    private static final String USERNAME = "sackjo02_web";
    private static final String PASSWORD = ""; // do not add a password!
    private static final String SERVER_URL = "jdbc:mysql://cray.cc.gettysburg.edu/s26_sackjo02";

    private Connection connection;
    private Scanner input;

    public MovieView() {
    	
        input = new Scanner(System.in);
        
        try {
            // Establish connection to the Sakila database
            connection = DriverManager.getConnection(SERVER_URL, USERNAME, PASSWORD);
            System.out.println("Connected to Sakila database successfully.");
        } catch (SQLException sqle) {
            System.err.printf("Unable to connect to database. Message: %s\n", sqle.getMessage());
            connection = null;
        }
    }

    public void run() {
        if (connection == null) return;

        try {
            // 1. Prompt for first letter
            System.out.print("Enter the first letter of a film name: ");
            String letter = input.nextLine().trim();
            if (letter.isEmpty()) {
                System.out.println("No letter entered. Exiting.");
                return;
            }

            // 2. Display films starting with that letter
            // Using prepared statement for security
            String filmQuery = "SELECT film_id, title FROM film WHERE title LIKE ? ORDER BY title";
            PreparedStatement filmStmt = connection.prepareStatement(filmQuery);
            filmStmt.setString(1, letter.charAt(0) + "%");
            
            ResultSet rs = filmStmt.executeQuery();
            List<Integer> filmIds = new ArrayList<>();
            int count = 1;

            System.out.println("\nFilms starting with '" + letter.charAt(0) + "':");
            while (rs.next()) {
                int id = rs.getInt("film_id");
                String title = rs.getString("title");
                filmIds.add(id);
                System.out.printf("[%d] %s\n", count++, title);
            }

            if (filmIds.isEmpty()) {
                System.out.println("No films found starting with that letter.");
                return;
            }

            // 3. User selects a film
            System.out.print("\nSelect a film number to see the actors: ");
            int selection = Integer.parseInt(input.nextLine());
            
            if (selection < 1 || selection > filmIds.size()) {
                System.out.println("Invalid selection.");
                return;
            }
            int selectedFilmId = filmIds.get(selection - 1);

            // 4. Print all actors in that film
            // Join film_actor with actor table
            String actorQuery = "SELECT a.first_name, a.last_name " +
                               "FROM actor a " +
                               "JOIN film_actor fa ON a.actor_id = fa.actor_id " +
                               "WHERE fa.film_id = ?";
            
            PreparedStatement actorStmt = connection.prepareStatement(actorQuery);
            actorStmt.setInt(1, selectedFilmId);
            ResultSet rsActors = actorStmt.executeQuery();

            System.out.println("\nActors in the selected film:");
            boolean foundActor = false;
            while (rsActors.next()) {
                foundActor = true;
                System.out.printf("- %s %s\n", rsActors.getString("first_name"), rsActors.getString("last_name"));
            }
            
            if (!foundActor) {
                System.out.println("No actors listed for this film.");
            }

            filmStmt.close();
            actorStmt.close();

        } catch (SQLException e) {
            System.err.println("Database error: " + e.getMessage());
        } catch (NumberFormatException e) {
            System.err.println("Invalid input. Please enter a number.");
        } finally {
            close();
        }
    }

    public void close() {
        try {
            if (connection != null) connection.close();
            if (input != null) input.close();
        } catch (SQLException sqle) {
            System.err.println(sqle.getMessage());
        }
    }

    public static void main(String[] args) {
        MovieView app = new MovieView();
        app.run();
    }
}