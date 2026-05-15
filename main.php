<?php
// | DB project
// | Milestone 3


// Muhammad Shahzaib Khan | 24I-0741 | CS-C
// Hassaan Mehmood | 24I-0829 | CS-C
// Hashir Noor | 24I-0554 | CS-C
// Arslan Hadayat | 24F-0724 | CS-C

?>


<?php
//  Main page
$active_page = 'dashboard';
$page_title = 'Dashboard';

include 'db_connect.php';
include 'header.php';

$db = new Database();
$conn = $db->connect();

function getCount($conn, $table)
{
    $result = sqlsrv_query($conn, "SELECT COUNT(*) AS total FROM $table");
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    return $row['total'];
}

$totalPatients = getCount($conn, 'patient');
$totalDoctors = getCount($conn, 'doctor');
$totalNurses = getCount($conn, 'nurse');
$totalBeds = getCount($conn, 'bed');
?>

<div class="main-content">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Ivor Paine Memorial Hospital — Database Management System</p>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon">🧑‍🤝‍🧑</div>
            <div class="stat-info">
                <h3><?php echo $totalPatients; ?></h3>
                <p>Total Patients</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👨‍⚕️</div>
            <div class="stat-info">
                <h3><?php echo $totalDoctors; ?></h3>
                <p>Total Doctors</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👩‍⚕️</div>
            <div class="stat-info">
                <h3><?php echo $totalNurses; ?></h3>
                <p>Total Nurses</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🛏️</div>
            <div class="stat-info">
                <h3><?php echo $totalBeds; ?></h3>
                <p>Total Beds</p>
            </div>
        </div>
    </div>

    <!-- direct links to the forms and the reports (queries)-->
    <h2 class="page-title" style="font-size:18px; margin-bottom:14px;">📋 Forms</h2>
    <div class="link-grid">
        <a href="patient_form.php" class="link-card">
            <div class="card-icon">🔍</div>
            <div class="card-text">
                <h4>Patient Record</h4>
                <p>Lookup full patient medical details</p>
            </div>
        </a>
        <a href="ward_form.php" class="link-card">
            <div class="card-icon">🏠</div>
            <div class="card-text">
                <h4>Ward Record</h4>
                <p>View ward beds and admissions</p>
            </div>
        </a>
        <a href="team_form.php" class="link-card">
            <div class="card-icon">👥</div>
            <div class="card-text">
                <h4>Consultant Team</h4>
                <p>View consultant teams and performance</p>
            </div>
        </a>
    </div>

    <h2 class="page-title" style="font-size:18px; margin-bottom:14px;">📈 Reports</h2>
    <div class="link-grid">
        <?php
        $reports = [
            1 => 'Consultants & Their Teams',
            2 => 'Ward Details',
            3 => 'Patient Treatments',
            4 => 'Junior Housemen',
            5 => 'Consultant Specialties',
            6 => 'Doctor Experience',
            7 => 'Multi-Complaint Patients',
            8 => 'Treatment Groups',
            9 => 'Performance History',
            10 => 'Full Patient Details',
            11 => 'Treatments by Date',
            12 => 'Staff Positions',
        ];
        foreach ($reports as $num => $title): ?>
            <a href="reports.php?report=<?php echo $num; ?>" class="link-card">
                <div class="card-icon" style="font-size:14px; font-weight:700;"><?php echo $num; ?></div>
                <div class="card-text">
                    <h4>Report <?php echo $num; ?></h4>
                    <p><?php echo $title; ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

</body>

</html>