# Web Authentication Application

A simple PHP web application featuring user registration, secure login, and session management. It manages user credentials in a MySQL backend and provides a personalized dashboard upon successful authentication.

## Link
https://cs.gettysburg.edu/~sackjo02/webApp/register.php

- SQL command used to create the users table
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(64) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (username)
);

- grant permission to sackjo02_web to access the users table

grant select, insert on users to sackjo02_web;

- Set permission for the folder
chmod 755 webApp

- Set permission for the files inside the folder
chmod 644 *.php

