import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Scanner;

//This program requires have /usr/share/java/mysql-connector-java-9.4.0.jar in your classpath.
//In eclipse, right click the project and select "Biuld Path" -> "Add External Archived..."
//Click "Other Locations" on the left and navigate to /usr/share/java
//Select mysql-connector-java-9.4.0.jar

//It only runs on the cs network.
public class JDBCTest2 {

	//Variables
	// Use your own username but keep the _web and s26
	private static final String USERNAME = "sackjo02_web";
	private static final String PASSWORD = ""; //do not add a password!
	private static final String SERVER_URL = "jdbc:mysql://cray.cc.gettysburg.edu/s26_sackjo02";

	private Connection connection;

	//constructor
	public JDBCTest2(){

		//connect to the database
		try {

			//create a connection
			connection = DriverManager.getConnection (SERVER_URL, USERNAME, PASSWORD);

		}
		catch(SQLException sqle){
			System.err.printf("Unable to connect to database. Message: %s\n", sqle.getMessage());
			connection = null;
		}
		catch(Exception e){
			System.err.printf("Unable to load mysql driver. Check your CLASSPATH. Message: %s\n",
					e.getMessage());
			connection = null;
		}
	}

	public void close(){

		try {
			if(connection != null)
				connection.close();
		}

		catch(SQLException sqle){
			System.err.println(sqle.getMessage());
		}

		finally {
			//used to indicate there is no connection
			connection = null;
		}

	}

	public void queryTest(String query){
		if(connection == null)
			throw new IllegalStateException("No Database connection.");

		try {
			//query code here
			Statement stmt = connection.createStatement();

			ResultSet result = stmt.executeQuery(query);
			// get meta: column info etc
			ResultSetMetaData meta = result.getMetaData();
			int columns = meta.getColumnCount();

			//iterate through results (each row)
			while(result.next()) {
				//for each columnResultSet
				for(int i =  1; i <= columns; i++) {
					System.out.println(meta.getColumnLabel(i) + ": " +
							result.getString(i));
				}
				System.out.println("----------------------------------");
			}

			//done, close the statement
			stmt.close();
		}
		catch(SQLException sqle){
			System.err.println("Exception: " + sqle.getMessage());
		}
	}

	public void modifyTest(String modify){
		if(connection == null)
			throw new IllegalStateException("No Database connection.");

		try {
			//create a statement
			Statement stmt = connection.createStatement();
			//modification code here

			//int rows = stmt.executeUpdate(query);

			stmt.close();
		}
		catch(SQLException sqle){
			System.err.println(sqle.getMessage());
		}
	}


	public void preparedModificationTest(){
		if(connection == null)
			throw new IllegalStateException("No Database connection.");

		try {
			//prepared statement update here
			String query = "INSERT INTO BILLS VALUES (?, ?, ?)";

			PreparedStatement pstmt = connection.prepareStatement(query);

			java.sql.Date today = new java.sql.Date(System.currentTimeMillis());

			pstmt.setDate(1, today);
			pstmt.setString(2, "Bob");
			pstmt.setDouble(3, 20.5);

			int rows = pstmt.executeUpdate();
			System.out.printf("%d rows inserted.\n", rows);

			//pay someone else (change payee and amount
			pstmt.setString(2, "Mary");
			pstmt.setDouble(3, 30.25);
			rows = pstmt.executeUpdate();
			System.out.printf("%d rows inserted.\n", rows);

			//pay someone else (change payee
			pstmt.setString(2, "Clif");
			rows = pstmt.executeUpdate();
			System.out.printf("%d rows inserted.\n", rows);


			pstmt.close();
		}
		catch(SQLException sqle){
			System.err.println(sqle.getMessage());
		}
	}

	public void preparedStatementTest(String name){
		if(connection == null)
			throw new IllegalStateException("No Database connection.");

		try {
			//prepared statement update here
			//fill in the blanks at the ?
			String query = "select fname, lname from EMPLOYEE join DEPARTMENT on (dno=dnumber) where dname like ?";
			PreparedStatement pstmt = connection.prepareStatement(query);

			//set data for a ? (start at 1)
			pstmt.setString(1, name);

			ResultSet result = pstmt.executeQuery();

			//output results
			// get meta: column info etc
			ResultSetMetaData meta = result.getMetaData();
			int columns = meta.getColumnCount();

			//iterate through results (each row)
			while(result.next()) {
				//for each column
				for(int i =  1; i <= columns; i++) {
					System.out.println(meta.getColumnLabel(i) + ": " +
							result.getString(i));
				}
				System.out.println("----------------------------------");
			}
			pstmt.close();
		}
		catch(SQLException sqle){
			System.err.println(sqle.getMessage());
		}
	}

	/**
	 * @param args
	 */
	public static void main(String[] args) {

		JDBCTest2 test = new JDBCTest2();
		//test.queryTest("SELECT FNAME, LNAME FROM EMPLOYEE");

		Scanner input = new Scanner(System.in);

		System.out.println("Enter a department name: ");
		String line = input.nextLine();

		String query = String.format(
				"select fname, lname from EMPLOYEE join D-- \n"
						+ "		\n"
						+ "		//match everyone who makes more than 30000\n"
						+ "		// ' and salary > 30000 -EPARTMENT on (dno=dnumber) where dname like '%%%s%%'", line);

		//1. prompt for a department name
		//2. create a query that displays all employees in that department

		//System.out.println(query);

		//test.queryTest(query);
		test.preparedStatementTest(line);
		//SQL injections:
		// % match everyone

		//match everyone in dept 4
		// ' and dnumber=4 -- 

		//match everyone who makes more than 30000
		// ' and salary > 30000 -- 

		test.preparedModificationTest();

		test.close();
	}

}

