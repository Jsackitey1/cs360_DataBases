# MeteoriteReader

A Java program that reads meteorite landing data from a CSV file and allows users to query records based on numeric attribute ranges.

## Overview

This program reads the `Meteorite_Landings.csv` dataset (45,425 records) into memory and prompts the user to:
1. Select a numeric attribute (`mass`, `year`, `reclat`, `reclong`)
2. Enter a minimum and maximum value
3. Receive a count of records within that range (inclusive)

---

## Data Structure Choices

### 1. `ArrayList<String[]>` — Record Storage

**Why ArrayList:**
- **Dynamic sizing**: File size unknown at compile time (45K+ rows)
- **O(n) iteration**: Efficient for scanning all records during range queries
- **Memory efficient**: Each row stored as a simple `String[]` from `split(",")`
- **No custom class needed**: We only access 4 of 8 columns; no behavior required

**Alternatives Considered:**
| Structure | Rejected Because |
|-----------|------------------|
| `LinkedList` | Slower iteration, unnecessary insertion flexibility |
| Custom `Meteorite` class | Added complexity without functional benefit |
| `TreeMap` on each attribute | Would require 4 separate sorted structures |

### 2. `HashMap<String, Integer>` — Attribute Mapping

**Why HashMap:**
- **O(1) lookup**: Instant translation from user input ("mass") → column index (3)
- **Clean validation**: `containsKey()` checks if attribute is valid
- **Extensible**: Easy to add new attributes

```java
ATTRIBUTE_COLUMNS.put("mass", 3);
ATTRIBUTE_COLUMNS.put("year", 5);
ATTRIBUTE_COLUMNS.put("reclat", 6);
ATTRIBUTE_COLUMNS.put("reclong", 7);
```

### 3. `double` for Range Values

All numeric comparisons use `double` because:
- `mass`, `reclat`, `reclong` contain decimal values
- `year` (integer) safely converts to double for uniform handling

---

## Program Structure

```
MeteoriteReader.java
│
├── main(String[] args)
│   └── Orchestrates: load data → get input → count → display
│
├── readData(String filename) → ArrayList<String[]>
│   ├── Opens BufferedReader for efficient file reading
│   ├── Skips header row
│   └── Splits each line by comma, adds to ArrayList
│
├── getValidAttribute(Scanner) → String
│   └── Loops until user enters valid attribute name
│
├── getDoubleInput(Scanner, String prompt) → double
│   └── Loops until user enters valid number
│
└── countInRange(ArrayList<String[]>, int col, double min, double max) → int
    ├── Iterates all records
    ├── Parses value at column index
    ├── Skips empty/invalid values
    └── Counts values where min ≤ value ≤ max
```

---

## CSV Format

| Column | Index | Type | Description |
|--------|-------|------|-------------|
| name | 0 | String | Meteorite name |
| id | 1 | int | Unique identifier |
| class | 2 | String | Classification |
| **mass** | 3 | double | Mass in grams |
| fall | 4 | String | "Found" or "Fell" |
| **year** | 5 | int | Year discovered |
| **reclat** | 6 | double | Latitude |
| **reclong** | 7 | double | Longitude |

*Queryable attributes in bold*

---

## Usage

### Compile
```bash
javac MeteoriteReader.java
```

### Run
```bash
java MeteoriteReader
```

### Example Session
```
Loaded 45425 meteorite records.
Enter attribute (mass, year, reclat, reclong): mass
Enter minimum value: 100
Enter maximum value: 500
Number of records with mass between 100.0 and 500.0 (inclusive): 8288
```

---

## Edge Cases Handled

| Case | Handling |
|------|----------|
| Empty/missing values | Skipped silently |
| Invalid attribute name | Re-prompts user |
| Non-numeric input | Re-prompts user |
| min > max | Displays error, exits |
| File not found | Displays error, exits |

---

## Time Complexity

| Operation | Complexity |
|-----------|------------|
| Load file | O(n) where n = number of records |
| Attribute lookup | O(1) via HashMap |
| Range query | O(n) linear scan |

---
