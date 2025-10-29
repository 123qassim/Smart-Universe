<?php
// This is generate_receipt.php
include('includes/config.php');

// --- 1. Check Login & Permissions ---
if (!isLoggedIn()) {
    die("Access Denied. Please log in.");
}
if (!isset($_GET['id'])) {
    die("Invalid request. No payment ID specified.");
}

$user_id = $_SESSION['user_id'];
$payment_id = $_GET['id'];

// --- 2. Fetch Payment Data (AND verify user ownership) ---
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.full_name, u.email, c.title AS course_title
        FROM payments p
        JOIN users u ON p.user_id = u.user_id
        LEFT JOIN courses c ON p.course_id = c.course_id
        WHERE p.payment_id = ? AND p.user_id = ? AND p.status = 'completed'
    ");
    $stmt->execute([$payment_id, $user_id]);
    $payment = $stmt->fetch();

    if (!$payment) {
        die("Receipt not found, or you do not have permission to view it.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}


// --- 3. Generate PDF ---
require('fpdf/fpdf.php'); // Adjust path if needed

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Logo
        // $this->Image('path/to/logo.png',10,6,30);
        $this->SetFont('Arial','B',18);
        $this->Cell(0, 10, 'Smart Uni-Verse', 0, 1, 'C');
        $this->SetFont('Arial','',12);
        $this->Cell(0, 10, 'Payment Receipt', 0, 1, 'C');
        $this->Ln(10);
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// --- Receipt Details ---
$pdf->SetFont('Arial','B',12);
$pdf->Cell(40, 10, 'Transaction ID:');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0, 10, $payment['transaction_id'] ? $payment['transaction_id'] : 'N/A-' . $payment['payment_id']);
$pdf->Ln();

$pdf->SetFont('Arial','B',12);
$pdf->Cell(40, 10, 'Date:');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0, 10, date('M d, Y', strtotime($payment['created_at'])));
$pdf->Ln();

// --- Billed To ---
$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0, 10, 'Billed To:', 0, 1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0, 8, $payment['full_name'], 0, 1);
$pdf->Cell(0, 8, $payment['email'], 0, 1);

// --- Payment Details Table ---
$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(130, 10, 'Description', 1, 0, 'C');
$pdf->Cell(60, 10, 'Amount', 1, 1, 'C');

$pdf->SetFont('Arial','',12);
$description = $payment['course_title'] ? 'Course Enrollment: ' . $payment['course_title'] : 'Monthly Subscription';
$pdf->Cell(130, 10, $description, 1);
$pdf->Cell(60, 10, '$' . number_format($payment['amount'], 2), 1, 1, 'R');

// --- Total ---
$pdf->SetFont('Arial','B',14);
$pdf->Cell(130, 12, 'Total Paid', 1, 0, 'R');
$pdf->Cell(60, 12, '$' . number_format($payment['amount'], 2), 1, 1, 'R');


// --- Output ---
$pdf->Output('D', 'Receipt_SmartUniVerse_' . $payment['payment_id'] . '.pdf');
exit;

?>