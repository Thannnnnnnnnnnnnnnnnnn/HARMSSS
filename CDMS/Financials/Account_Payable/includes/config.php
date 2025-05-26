<?php
$host = "localhost";
$username = "3206_CENTRALIZED_DATABASE";
$password = "4562526";    
// $host = "localhost:3307";  
// $username = "root";   
// $password = "";       

// Connect to Accounts Payable Database
$accounts_payable_db = "fin_accounts_payable";
$conn = new mysqli($host, $username, $password, $accounts_payable_db);

// Connect to Budget Management Database
$budget_db = "fin_budget_management";  
$conn_budget = new mysqli($host, $username, $password, $budget_db);  

// Connect to Disbursement Database
$disbursement_db = "fin_disbursement";  
$conn_disbursement = new mysqli($host, $username, $password, $disbursement_db);  

// Connect to General Ledger Database
$general_ledger_db = "fin_general_ledger";  
$conn_general_ledger = new mysqli($host, $username, $password, $general_ledger_db);  
   
// Check connection 
if ($conn->connect_error || $conn_budget->connect_error || $conn_disbursement->connect_error || $conn_general_ledger->connect_error) {     
    die("Connection failed: " . 
        ($conn->connect_error ? "Accounts Payable: " . $conn->connect_error . " | " : "") . 
        ($conn_budget->connect_error ? "Budget Management: " . $conn_budget->connect_error . " | " : "") . 
        ($conn_disbursement->connect_error ? "Disbursement: " . $conn_disbursement->connect_error . " | " : "") . 
        ($conn_general_ledger->connect_error ? "General Ledger: " . $conn_general_ledger->connect_error : "")
    ); 
}
?>