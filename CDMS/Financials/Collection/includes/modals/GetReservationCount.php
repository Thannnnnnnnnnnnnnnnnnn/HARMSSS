<?php
include('../config/controller.php');
header('Content-Type: application/json');
$data = new Data();
$today = new DateTime();
$todayDate = $today->format('Y-m-d');
$collections = $data->ViewCollectionPayments();

$total = 0;
$unseen = 0;
foreach ($collections as $c) {
    $startDate = new DateTime($c['StartDate']);
    $startDateOnly = $startDate->format('Y-m-d');
    if ($c['Status'] === 'Reservation' && $startDate > $today && $startDateOnly !== $todayDate) {
        $total++;
        if ($c['IsViewed'] == 0) {
            $unseen++;
        }
    }
}
echo json_encode(['status' => 'success', 'total' => $total, 'unseen' => $unseen]);
exit;
?>