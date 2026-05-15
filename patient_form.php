<?php
// Form 1: Patient Record Lookup
$active_page = 'patient';
$page_title = 'Patient Record';

include 'db_connect.php';
include 'header.php';

$db = new Database();
$conn = $db->connect();

$patientList = sqlsrv_query($conn, "SELECT patient_no, patient_name FROM patient ORDER BY patient_name");

$selected = isset($_GET['patient_no']) ? (int) $_GET['patient_no'] : 0;
?>

<div class="main-content">
    <h1 class="page-title">Patient Record</h1>
    <p class="page-subtitle">Select a patient to view their full medical record</p>

    <!-- select patients from the list instead of using the number :) -->
    <div class="card">
        <h3>Select Patient</h3>
        <form method="GET" class="filter-form">
            <div class="form-group">
                <label>Patient</label>
                <select name="patient_no" onchange="this.form.submit()">
                    <option value="">-- Choose a Patient --</option>
                    <?php while ($p = sqlsrv_fetch_array($patientList, SQLSRV_FETCH_ASSOC)): ?>
                        <option value="<?php echo $p['patient_no']; ?>" <?php echo $selected == $p['patient_no'] ? 'selected' : ''; ?>>
                            <?php echo $p['patient_no'] . ' — ' . $p['patient_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if ($selected > 0):
        //get patient info + bed + ward + doctor
        $infoSql = "SELECT p.patient_no, p.patient_name, p.date_of_birth,
                           w.ward_name, w.spec_name AS ward_specialty,
                           b.bed_no, b.date_admitted,
                           d.doctor_id, d.name AS doctor_name, d.position,
                           cons.name AS consultant_name
                    FROM patient p
                    LEFT JOIN bed b ON p.patient_no = b.patient_no
                    LEFT JOIN ward w ON b.ward_name = w.ward_name
                    JOIN doctor d ON p.doctor_id = d.doctor_id
                    LEFT JOIN doctor cons ON d.consultant_id = cons.doctor_id
                    WHERE p.patient_no = ?";
        $infoResult = sqlsrv_query($conn, $infoSql, [$selected]);
        $info = sqlsrv_fetch_array($infoResult, SQLSRV_FETCH_ASSOC);

        if ($info): ?>
            <div class="card">
                <h3>Patient Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Patient No</label>
                        <span><?php echo $info['patient_no']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Name</label>
                        <span><?php echo $info['patient_name']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Date of Birth</label>
                        <span><?php echo $info['date_of_birth'] instanceof DateTime ? $info['date_of_birth']->format('Y-m-d') : $info['date_of_birth']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Ward</label>
                        <span><?php echo $info['ward_name'] ?? '—'; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Doctor Name</label>
                        <span><?php echo $info['doctor_name']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Doctor No</label>
                        <span><?php echo $info['doctor_id']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Consultant</label>
                        <span><?php echo $info['consultant_name'] ?? '—'; ?></span>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Complaints &amp; Treatments</h3>
                <?php
                $recordSql = "SELECT c.description AS 'Complaint',
                                 t.description AS 'Treatment',
                                 d.name AS 'Treating Doctor',
                                 m.date_started AS 'Started',
                                 m.date_ended AS 'Ended'
                          FROM medicalRecord m
                          JOIN complaint c ON m.complaint_id = c.complaint_id
                          JOIN treatment t ON m.treatment_id = t.treatment_id
                          JOIN doctor d ON m.doctor_id = d.doctor_id
                          WHERE m.patient_no = ?";
                $recordResult = sqlsrv_query($conn, $recordSql, [$selected]);
                renderTable($recordResult);
                ?>
            </div>
        <?php else: ?>
            <p class="no-data">Patient not found.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>

</html>