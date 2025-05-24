<?php
include_once __DIR__ . '/../../Database/connection.php'; 
class Data{
     private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect("fin_budget_management"); 
    } 
   public function Budget(){
   $data = $this->conn->query("SELECT * FROM budgets");
    return $data->fetch_all(MYSQLI_ASSOC);
   }
  public function View(){
   $data = $this->conn->query("SELECT * FROM budgetallocations");
    return $data->fetch_all(MYSQLI_ASSOC);
   }
public function Create($select_budget, $budgetname, $totalamt, $departName, $allocAmt){
    $stmt = $this->conn->prepare("INSERT INTO budgetallocations(BudgetID, BudgetName, TotalAmount, DepartmentName, AllocatedAmount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssiss', $select_budget, $budgetname, $totalamt, $departName, $allocAmt);
    return $stmt->execute();
}

   public function getById($id){
        $stmt = $this->conn->prepare("SELECT *FROM budgetallocations Where AllocationID= ?");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();

    } 
public function Update($budgetName, $deptName,$Editid) {
    $stmt = $this->conn->prepare("UPDATE budgetallocations SET BudgetName = ?, DepartmentName = ? WHERE AllocationID = ?");
      $stmt->bind_param('ssi',  $budgetName, $deptName,$Editid);
    return  $stmt->execute();
}
    public function Delete($DeleteID){
       $stmt = $this->conn->prepare("DELETE FROM budgetallocations Where AllocationID=?");
       $stmt->bind_param('i',$DeleteID);
       return $stmt->execute();
    }
   
public function getLastInsertedID() {
    return $this->conn->insert_id;
}


}

   ?>