<?php
include_once __DIR__ . '/../../Database/connection.php'; 
class Data{
     private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect("fin_budget_management"); 
    } 
   public function Adjust(){
     $data = $this->conn->query("SELECT * FROM budgetallocations");
     return $data->fetch_all(MYSQLI_ASSOC);
   }
  public function View(){
    $data = $this->conn->query("SELECT * FROM budgetadjustments");
    return $data->fetch_all(MYSQLI_ASSOC);
  }
  public function Create($budgetID,$adjustID,$budget_name,$totalamt,$department,$adjustreason,$adjustAmt){
    $stmt = $this->conn->prepare("INSERT INTO budgetadjustments(BudgetID, AllocationID ,BudgetName, BudgetAllocated, DepartmentName,AdjustmentReason ,AdjustmentAmount) VALUES (?, ?, ?, ?, ?,?,?)");
    $stmt->bind_param('iisissi', $budgetID,$adjustID,$budget_name,$totalamt,$department,$adjustreason,$adjustAmt);
    return $stmt->execute();
}


       public function getById($id){
        $stmt = $this->conn->prepare("SELECT *FROM budgetadjustments Where AdjustmentID= ?");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();

    } 
    public function Update($adjReason,$adjustAmt,$Editid) {
    $stmt = $this->conn->prepare("UPDATE budgetadjustments SET AdjustmentReason = ?, AdjustmentAmount = ? WHERE AdjustmentID = ?");
      $stmt->bind_param('ssi',  $adjReason,$adjustAmt,$Editid);
    return  $stmt->execute();
}
    public function UpdatedAmount( $adjustAmt, $adjustID){
     $stmt = $this->conn->prepare("UPDATE budgetallocations SET AllocatedAmount = ? WHERE AllocationID = ?");
     $stmt->bind_param('ii',  $adjustAmt, $adjustID);
     return $stmt->execute();
    
    }
   public function CreateAndUpdateAmount($budgetID, $adjustID, $budget_name, $totalamt, $department, $adjustreason, $adjustAmt) {
    $this->conn->begin_transaction();

    try {
        $stmt = $this->conn->prepare("INSERT INTO budgetadjustments (BudgetID, AllocationID, BudgetName, BudgetAllocated, DepartmentName, AdjustmentReason, AdjustmentAmount) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iisissi', $budgetID, $adjustID, $budget_name, $totalamt, $department, $adjustreason, $adjustAmt);
        $stmt->execute();
        $adjustmentID = $this->conn->insert_id;
        $stmt = $this->conn->prepare("UPDATE budgetallocations SET AllocatedAmount = ? WHERE AllocationID = ?");
        $stmt->bind_param('ii', $adjustAmt, $adjustID);
        $stmt->execute();
        $this->conn->commit();

        return $adjustmentID;

    } catch (Exception $e) {
        $this->conn->rollback();
        throw $e;
    }
}






    public function Delete($DeleteID){
     $stmt = $this->conn->prepare("DELETE FROM budgetadjustments WHERE adjustmentID =?");
     $stmt->bind_param('i', $DeleteID);
     return $stmt->execute();

    }
public function getLastInsertedID() {
    return $this->conn->insert_id;
}











}
?>