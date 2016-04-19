<?php
/**
 * Created by PhpStorm.
 * User: jorgeromero
 * Date: 4/16/16
 * Time: 3:09 PM
 */

$database = new SQLite3('byte.db');


/*$sql = 'CREATE TABLE Users (
userid INTEGER  PRIMARY  KEY,
fname TEXT,
lname TEXT,
age INTEGER,
height INTEGER,
bmi FLOAT,
bmr FLOAT
)';

$database-> exec($sql);*/

$sql2 = 'INSERT INTO Users (fname, lname, age, height, bmi, bmr)'.
    'VALUES("jorge", "romero", "21", "68", "23.0", "1720"); ' ;

$database ->exec($sql2);

$sql = "SELECT * FROM Users ORDER BY lname, fname";

$result = $database ->query($sql);

while ($row = $result-> fetchArray()){
    echo $row['userid']. " ". $row['fname'] . " " . $row['lname'];
    echo "this worked";
}



