<?php
include_once __DIR__ . '/../../Database/connection.php'; 
class Data{
     private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect("fin_budget_management"); 
    } 

  public function View(){
   $data = $this->conn->query("SELECT * FROM budgets");
    return $data->fetch_all(MYSQLI_ASSOC);
   }
 public function Create($budgetname , $totalAmt , $start , $end){
   $stmt = $this->conn->prepare("INSERT INTO budgets(BudgetName,TotalAmount,StartDate,EndDate) VALUES (?,?,?,?) ");
   $stmt->bind_param('siss',$budgetname , $totalAmt , $start , $end);
   return $stmt->execute();
   }
   public function getById($id){
        $stmt = $this->conn->prepare("SELECT *FROM budgets Where BudgetID= ?");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();

    } 
public function Update($budgetid, $budgetname, $totalAmt, $start, $end) {
    $stmt = $this->conn->prepare("UPDATE budgets SET BudgetName = ?, TotalAmount = ?, StartDate = ?, EndDate = ? WHERE BudgetID = ?");
      $stmt->bind_param('sissi', $budgetname, $totalAmt, $start, $end, $budgetid);
    return  $stmt->execute();
}
    public function Delete($id){
       $stmt = $this->conn->prepare("DELETE FROM budgets Where BudgetID=?");
       $stmt->bind_param('i',$id);
       return $stmt->execute();
    }



}

   ?>