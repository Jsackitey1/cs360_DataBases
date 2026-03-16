-- Joseph Sackitey
-- Hw3
-- Create PUBLISHER table first because BOOK references it

CREATE TABLE PUBLISHER (
    Name VARCHAR(255) PRIMARY KEY,
    Address VARCHAR(255),
    Phone VARCHAR(20)
);

-- Create BOOK table
CREATE TABLE BOOK (
    Book_id INT PRIMARY KEY,
    Title VARCHAR(255) NOT NULL,
    Publisher_name VARCHAR(255),
    FOREIGN KEY (Publisher_name) REFERENCES PUBLISHER(Name)
        ON UPDATE CASCADE ON DELETE SET NULL
);

-- Create BOOK_AUTHORS table
CREATE TABLE BOOK_AUTHORS (
    Book_id INT,
    Author_name VARCHAR(255),
    PRIMARY KEY (Book_id, Author_name),
    FOREIGN KEY (Book_id) REFERENCES BOOK(Book_id)
        ON DELETE CASCADE
);

-- Insert Publishers
INSERT INTO PUBLISHER (Name, Address, Phone) VALUES 
('Pearson', '221 River St, Hoboken, NJ', '201-236-7000'),
('O-Reilly Media', '1005 Gravenstein Hwy N, Sebastopol, CA', '707-827-7000'),
('MIT Press', '77 Massachusetts Ave, Cambridge, MA', '617-253-5646');

-- Insert Books
INSERT INTO BOOK (Book_id, Title, Publisher_name) VALUES 
(1, 'Fundamentals of Database Systems', 'Pearson'),
(2, 'Database System Concepts', 'Pearson'),
(3, 'SQL Cookbook', 'O-Reilly Media'),
(4, 'Introduction to Algorithms', 'MIT Press'),
(5, 'Designing Data-Intensive Applications', 'O-Reilly Media');

-- Insert Authors
INSERT INTO BOOK_AUTHORS (Book_id, Author_name) VALUES 
(1, 'Ramez Elmasri'),
(1, 'Shamkant Navathe'),
(2, 'Abraham Silberschatz'),
(3, 'Anthony Molinaro'),
(4, 'Thomas H. Cormen'),
(5, 'Martin Kleppmann');