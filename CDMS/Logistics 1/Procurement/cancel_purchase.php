<?php
session_start();
include("../../connection.php");

$db_name = "logs1_procurement";
$conn = $connections[$db_name];

// Show confirmation prompt (GET)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['purchase_id'])) {
    $purchase_id = $_GET['purchase_id'];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Confirm Cancellation</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                title: 'Cancel this request?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'cancel_purchase.php'; // Same file

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'purchase_id';
                    input.value = <?php echo json_encode($purchase_id); ?>;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    window.location.href = 'purchase_request.php';
                }
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Cancel logic (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $purchase_id = $_POST['purchase_id'] ?? '';

    if ($purchase_id) {
        // Validate status
        $stmt_check = $conn->prepare("SELECT status FROM purchase_request WHERE purchase_id = ?");
        $stmt_check->bind_param("s", $purchase_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $current = $result->fetch_assoc();
        $stmt_check->close();

        if (!$current || in_array($current['status'], ['Clearance approved', 'Cancelled'])) {
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Cancel',
                    text: 'Request cannot be cancelled. Current status: "<?php echo $current['status']; ?>"'
                }).then(() => {
                    window.location.href = 'purchase_request.php';
                });
            </script>
            </body>
            </html>
            <?php
            exit;
        }

        // Proceed with cancel
        $stmt = $conn->prepare("UPDATE purchase_request SET status = 'Cancelled' WHERE purchase_id = ?");
        $stmt->bind_param("s", $purchase_id);
        if ($stmt->execute()) {
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Cancelled!',
                    text: 'The purchase request was successfully cancelled.'
                }).then(() => {
                    window.location.href = 'purchase_request.php';
                });
            </script>
            </body>
            </html>
            <?php
            exit;
        } else {
            die("Error cancelling request: " . $stmt->error);
        }
    } else {
        die("Invalid request.");
    }
} else {
    header("Location: purchase_request.php");
    exit;
}
?>
