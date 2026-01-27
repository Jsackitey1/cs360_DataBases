
/**
 * @author josephsackitey
 * 
 * MeteoriteReader - Reads meteorite data from CSV and allows range queries
 * on mass, year, reclat, and reclong.
 * 
 * Data Structures Used:
 * - ArrayList<String[]>: Stores all CSV records for efficient iteration
 * - HashMap<String, Integer>: Maps attribute names to column indices for O(1)
 * lookup
 */

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Scanner;

public class MeteoriteReader {

	// Map attribute names to their column indices in the CSV
	private static final HashMap<String, Integer> ATTRIBUTE_COLUMNS = new HashMap<>();

	static {
		ATTRIBUTE_COLUMNS.put("mass", 3);
		ATTRIBUTE_COLUMNS.put("year", 5);
		ATTRIBUTE_COLUMNS.put("reclat", 6);
		ATTRIBUTE_COLUMNS.put("reclong", 7);
	}

	/**
	 * Main entry point - loads data and handles user interaction
	 */
	public static void main(String[] args) {
		String filename = "Meteorite_Landings.csv";

		// Read all data into memory
		ArrayList<String[]> records = readData(filename);

		if (records == null || records.isEmpty()) {
			System.out.println("Error: Could not read data or file is empty.");
			return;
		}

		System.out.println("Loaded " + records.size() + " meteorite records.");

		Scanner scanner = new Scanner(System.in);

		// Get attribute from user
		String attribute = getValidAttribute(scanner);

		// Get range from user
		double min = getDoubleInput(scanner, "Enter minimum value: ");
		double max = getDoubleInput(scanner, "Enter maximum value: ");

		// Validate range
		if (min > max) {
			System.out.println("Error: Minimum value cannot be greater than maximum value.");
			scanner.close();
			return;
		}

		// Get column index for the attribute
		int columnIndex = ATTRIBUTE_COLUMNS.get(attribute);

		// Count records in range
		int count = countInRange(records, columnIndex, min, max);

		System.out.println("Number of records with " + attribute + " between "
				+ min + " and " + max + " (inclusive): " + count);

		scanner.close();
	}

	/**
	 * Reads the CSV file and returns all data records (excluding header).
	 * 
	 * @param filename Path to the CSV file
	 * @return ArrayList of String arrays, each representing a row
	 */
	public static ArrayList<String[]> readData(String filename) {
		ArrayList<String[]> records = new ArrayList<>();

		try (BufferedReader br = new BufferedReader(new FileReader(filename))) {
			String line;
			boolean isHeader = true;

			while ((line = br.readLine()) != null) {
				// Skip the header row
				if (isHeader) {
					isHeader = false;
					continue;
				}

				// Split by comma and add to list
				String[] fields = line.split(",");
				records.add(fields);
			}

		} catch (IOException e) {
			System.out.println("Error reading file: " + e.getMessage());
			return null;
		}

		return records;
	}

	/**
	 * Prompts user for a valid attribute name until one is entered.
	 * 
	 * @param scanner Scanner for user input
	 * @return Valid attribute name (mass, year, reclat, or reclong)
	 */
	public static String getValidAttribute(Scanner scanner) {
		String attribute;

		while (true) {
			System.out.print("Enter an attribute (mass, year, reclat, reclong): ");
			attribute = scanner.nextLine().trim().toLowerCase();

			if (ATTRIBUTE_COLUMNS.containsKey(attribute)) {
				return attribute;
			}

			System.out.println("Invalid attribute. Please enter mass, year, reclat, or reclong.");
		}
	}

	/**
	 * Prompts user for a double value with validation.
	 * 
	 * @param scanner Scanner for user input
	 * @param prompt  Message to display
	 * @return Valid double value
	 */
	public static double getDoubleInput(Scanner scanner, String prompt) {
		while (true) {
			System.out.print(prompt);
			String input = scanner.nextLine().trim();

			try {
				return Double.parseDouble(input);
			} catch (NumberFormatException e) {
				System.out.println("Invalid number. Please enter a numeric value.");
			}
		}
	}

	/**
	 * Counts records where the specified attribute falls within the given range
	 * (inclusive).
	 * 
	 * @param records     List of all data records
	 * @param columnIndex Index of the attribute column
	 * @param min         Minimum value (inclusive)
	 * @param max         Maximum value (inclusive)
	 * @return Number of matching records
	 */
	public static int countInRange(ArrayList<String[]> records, int columnIndex,
			double min, double max) {
		int count = 0;

		for (String[] record : records) {
			// Check if column exists in this record
			if (columnIndex >= record.length) {
				continue;
			}

			String valueStr = record[columnIndex].trim();

			// Skip empty values
			if (valueStr.isEmpty()) {
				continue;
			}

			try {
				double value = Double.parseDouble(valueStr);

				// Check if value is within range (inclusive)
				if (value >= min && value <= max) {
					count++;
				}
			} catch (NumberFormatException e) {
				// Skip non-numeric values
				continue;
			}
		}

		return count;
	}
}
