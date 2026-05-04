<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    die("Unauthorized access.");
}

$appointment_id = $_GET['id'];

try {
    // Fetch detailed appointment info
    $sql = "SELECT a.*, d.name as doctor_name, p.name as patient_name 
            FROM appointments a
            JOIN users d ON a.doctor_id = d.id
            JOIN users p ON a.patient_id = p.id
            WHERE a.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$appointment_id]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$app) {
        die("Appointment not found.");
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Appointment Receipt - CareClinic</title>
    <style>
        body { font-family: sans-serif; padding: 40px; color: #333; }
        .receipt-box { border: 2px solid #eee; padding: 30px; max-width: 600px; margin: auto; }
        .header { text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .details { margin-top: 30px; line-height: 2; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="receipt-box">
        <div class="header">
            <h1>CareClinic</h1>
            <p>Appointment Confirmation Receipt</p>
        </div>
        
        <div class="details">
            <p><strong>Appointment ID:</strong> #<?php echo $app['id']; ?></p>
            <p><strong>Patient Name:</strong> <?php echo $app['patient_name']; ?></p>
            <p><strong>Consulting Doctor:</strong> Dr. <?php echo $app['doctor_name']; ?></p>
            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($app['appointment_date'])); ?></p>
            <p><strong>Time Slot:</strong> <?php echo $app['time_slot']; ?></p>
            <p><strong>Status:</strong> <?php echo $app['status']; ?></p>
            <p><strong>Symptoms:</strong> <?php echo $app['symptoms'] ? $app['symptoms'] : 'N/A'; ?></p>
        </div>

        <div class="footer">
            <p>This is a computer-generated receipt.</p>
            <button onclick="window.print()" class="no-print" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Print / Save as PDF</button>
        </div>
    </div>

    <script>
        // Automatically trigger print dialog on load
        window.onload = function() {
            // Uncomment the line below if you want it to pop up instantly
            // window.print();
        }
    </script>
</body>
</html>
<?php
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>