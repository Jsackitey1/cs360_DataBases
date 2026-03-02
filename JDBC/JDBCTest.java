import java.sql.*;

//This program requires have /usr/share/java/mysql-connector-java-9.4.0.jar in your classpath.
//In eclipse, right click the project and select "Biuld Path" -> "Add External Archived..."
//Click "Other Locations" on the left and navigate to /usr/share/java
//Select mysql-connector-java-9.4.0.jar

//It only runs on the cs network.
public class JDBCTest {

	//Variables
	// Use your own username but keep the _web and s26
	private static final String USERNAME = "sackjo02_web";
	private static final String PASSWORD = ""; //do not add a password!
	private static final String SERVER_URL = "jdbc:mysql://cray.cc.gettysburg.edu/s26_sackjo02";

	private Connection connection;

	//constructor
	public JDBCTest(){

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
				//for each column
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

			stmt.close();
		}
		catch(SQLException sqle){
			System.err.println(sqle.getMessage());
		}
	}


	public void preparedModificationTest(String name, int amount){
		if(connection == null)
			throw new IllegalStateException("No Database connection.");

		//		try {
		//			//prepared statement update here
		//		}
		//		catch(SQLException sqle){
		//			System.err.println(sqle.getMessage());
		//		}
	}
	/**
	 * @param args
	 */
	public static void main(String[] args) {

		JDBCTest test = new JDBCTest();
		test.queryTest("SELECT FNAME, LNAME FROM EMPLOYEE");

		test.close();

	}

}

