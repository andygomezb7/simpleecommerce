<?php

require_once("global.php");
$globalC = new GlobalClass();


// Task 1: Count employees earning more than $300,000
$earningThreshold = 300000;
$highEarningCount = $globalC->countHighEarningEmployees($earningThreshold);
echo "Number of employees earning more than $earningThreshold: $highEarningCount";


$myData = [
    "name" => "Andy Gomez",
    "salary" => "60000",
    "age" => "24"
];

// Task 2: Create a record with your name
echo "Employee created successfully!";
$createdMyRecord = $globalC->createEmployee($myData);

// Task 3: Find your user id by your name
$yourName = "Your Name"; // Replace with your actual name
$yourUserId = $globalC->getUserIdByName($yourName);
echo "User ID for '$yourName': $yourUserId";