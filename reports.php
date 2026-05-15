<?php

$active_page = 'reports';

include 'db_connect.php';
include 'header.php';

$db = new Database();
$conn = $db->connect();

$report = isset($_GET['report']) ? (int) $_GET['report'] : 0;

$titles = [
    1 => 'Consultants & the Doctors in Their Team',
    2 => 'Wards with Sisters, Care Units & Staff Nurses',
    3 => 'Patients — Complaints, Treatments & Dates',
    4 => 'Junior Housemen, Patients & Care-Unit Nurses',
    5 => 'Consultants with Unique Specialties',
    6 => 'Complaints, Treatments & Doctor Experience',
    7 => 'Patients with More Than One Complaint',
    8 => 'Patients Grouped by Treatment Within Complaint',
    9 => 'Performance History for a Doctor',
    10 => 'Full Medical Details for a Patient',
    11 => 'Treatments for a Complaint Between Dates',
    12 => 'Staff Positions & Count',
];

$page_title = $report > 0 ? 'Report ' . $report : 'Reports';
?>

<div class="main-content">

    <?php if ($report === 0): ?>
            <h1 class="page-title">Reports</h1>
            <p class="page-subtitle">Select a report to view</p>
            <div class="report-grid">
                <?php foreach ($titles as $num => $title): ?>
                        <a href="reports.php?report=<?php echo $num; ?>" class="report-card">
                            <div class="rnum">
                                <?php echo $num; ?>
                            </div>
                            <div class="rtitle">
                                <?php echo $title; ?>
                            </div>
                        </a>
                <?php endforeach; ?>
            </div>

    <?php else: ?>
            <a href="reports.php" class="back-link">← Back to All Reports</a>
            <h1 class="page-title">Report
                <?php echo $report; ?>
            </h1>
            <p class="page-subtitle">
                <?php echo $titles[$report] ?? 'Unknown Report'; ?>
            </p>

            <div class="card">
                <?php
                // Query 1

                if ($report == 1):
                    $sql = "SELECT senior.name AS 'Consultant Name',
                       junior.name AS 'Team Member',
                       junior.position AS 'Member Rank'
                FROM doctor senior
                JOIN doctor junior ON senior.doctor_id = junior.consultant_id";
                    $result = sqlsrv_query($conn, $sql);
                    renderTable($result);


                // Query 2

                elseif ($report == 2):
                    $sql = "SELECT w.ward_name AS 'Ward Name',
                       nd.name AS 'Day Sister',
                       nn.name AS 'Night Sister',
                       cu.care_unit_no AS 'Care Unit',
                       ns.name AS 'Staff Nurse In Charge'
                    FROM ward w
                    LEFT JOIN nurse nd ON w.day_sister_no = nd.staff_no
                    LEFT JOIN nurse nn ON w.night_sister_no = nn.staff_no
                    JOIN careunit cu ON w.ward_name = cu.ward_name
                    LEFT JOIN nurse ns ON cu.incharge_staff_no = ns.staff_no";
                    $result = sqlsrv_query($conn, $sql);
                    renderTable($result);

                // Query 3

                elseif ($report == 3):
                    $sql = "SELECT p.patient_name AS 'Patient Name',
                       c.description AS 'Complaint',
                       t.description AS 'Treatment Given',
                       m.date_started AS 'Treatment Started',
                       m.date_ended AS 'Treatment Ended'
                FROM patient p
                JOIN medicalRecord m ON m.patient_no = p.patient_no
                JOIN complaint c ON m.complaint_id = c.complaint_id
                JOIN treatment t ON m.treatment_id = t.treatment_id";
                    $result = sqlsrv_query($conn, $sql);
                    renderTable($result);


                // Query 4

                elseif ($report == 4):
                    $sql = "SELECT d.name AS 'Junior Houseman',
                       p.patient_name AS 'Patient Name',
                       cu.care_unit_no AS 'Care Unit',
                       n.name AS 'Staff Nurse In Charge'
                    FROM doctor d
                    JOIN patient p ON d.doctor_id = p.doctor_id
                    JOIN careunit cu ON p.care_unit_no = cu.care_unit_no
                    JOIN nurse n ON cu.incharge_staff_no = n.staff_no
                    WHERE d.position = 'junior houseman(jh)'";
                    $result = sqlsrv_query($conn, $sql);
                    renderTable($result);

                // Query 5

                elseif ($report == 5):
                    $sql = "SELECT DISTINCT d.name AS 'Consultant Name',
                       c.spec_name AS 'Unique Specialty'
                    FROM doctor d
                    JOIN consultant c ON d.doctor_id = c.doctor_id";
                    $result = sqlsrv_query($conn, $sql);
                    renderTable($result);


                // Query 6

                elseif ($report == 6):
                    $sql = "SELECT c.description AS 'Patient Complaint',
                       t.description AS 'Treatment Prescribed',
                       d.name AS 'Prescribing Doctor',
                       pe.position AS 'Past Job Title',
                       pe.establishment AS 'Past Hospital',
                       pe.from_date AS 'Worked From',
                       pe.to_date AS 'Worked To'
                    FROM medicalRecord m
                    JOIN complaint c ON m.complaint_id = c.complaint_id
                    JOIN treatment t ON m.treatment_id = t.treatment_id
                    JOIN doctor d ON m.doctor_id = d.doctor_id
                    LEFT JOIN preexperience pe ON d.doctor_id = pe.doctor_id";
                    $result = sqlsrv_query($conn, $sql);
                    renderTable($result);

                // Query 7

                elseif ($report == 7):
                    $sql = "SELECT p.patient_name AS 'Patient Name',
                       c.description AS 'Complaint',
                       t.description AS 'Treatment'
                    FROM patient p
                    JOIN medicalRecord m ON p.patient_no = m.patient_no
                    JOIN complaint c ON m.complaint_id = c.complaint_id
                    JOIN treatment t ON m.treatment_id = t.treatment_id
                    WHERE p.patient_no IN (
                    SELECT patient_no FROM medicalRecord
                    GROUP BY patient_no
                    HAVING COUNT(DISTINCT complaint_id) > 1 )";
                    $result = sqlsrv_query($conn, $sql);
                    renderTable($result);

                    
                // Query 8 

                elseif ($report == 8):
                    $sql = "SELECT c.description AS 'Complaint',
                       t.description AS 'Treatment',
                       p.patient_name AS 'Patient Name'
                    FROM medicalRecord m
                    JOIN complaint c ON m.complaint_id = c.complaint_id
                    JOIN treatment t ON m.treatment_id = t.treatment_id
                    JOIN patient p ON m.patient_no = p.patient_no
                    ORDER BY c.description, t.description, p.patient_name";
                    $result = sqlsrv_query($conn, $sql);
                    renderTable($result);


                // Query 9

                elseif ($report == 9):
                    $docList = sqlsrv_query($conn, "SELECT doctor_id, name FROM doctor ORDER BY name");
                    $selDoc = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;
                    ?>
                        <form method="GET" class="filter-form">
                            <input type="hidden" name="report" value="9">
                            <div class="form-group">
                                <label>Select Doctor</label>
                                <select name="doctor_id" onchange="this.form.submit()">
                                    <option value="">-- Choose a Doctor --</option>
                                    <?php while ($d = sqlsrv_fetch_array($docList, SQLSRV_FETCH_ASSOC)): ?>
                                            <option value="<?php echo $d['doctor_id']; ?>" <?php echo $selDoc == $d['doctor_id'] ? 'selected' : ''; ?>>
                                                <?php echo $d['name']; ?>
                                            </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </form>
                        <?php
                        if ($selDoc > 0):
                            $sql = "SELECT d.name AS 'Doctor Name',
                            ph.date_recorded AS 'Review Date',
                            ph.grade AS 'Performance Grade'
                        FROM doctor d
                        JOIN performancehistory ph ON d.doctor_id = ph.doctor_id
                        WHERE d.doctor_id = ?";
                            $result = sqlsrv_query($conn, $sql, [$selDoc]);
                            renderTable($result);
                        endif;

                // Query 10

                elseif ($report == 10):
                    $patList = sqlsrv_query($conn, "SELECT patient_no, patient_name FROM patient ORDER BY patient_name");
                    $selPat = isset($_GET['patient_no']) ? (int) $_GET['patient_no'] : 0;
                    ?>
                    <form method="GET" class="filter-form">
                        <input type="hidden" name="report" value="10">
                        <div class="form-group">
                            <label>Select Patient</label>
                            <select name="patient_no" onchange="this.form.submit()">
                                <option value="">-- Choose a Patient --</option>
                                <?php while ($p = sqlsrv_fetch_array($patList, SQLSRV_FETCH_ASSOC)): ?>
                                    <option value="<?php echo $p['patient_no']; ?>" <?php echo $selPat == $p['patient_no'] ? 'selected' : ''; ?>>
                                        <?php echo $p['patient_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                    <?php
                    if ($selPat > 0):
                            $sql = "SELECT p.patient_name AS 'Patient',
                            p.date_of_birth AS 'DOB',
                            w.ward_name AS 'Ward',
                            b.bed_no AS 'Bed',
                            b.date_admitted AS 'Admitted',
                            d.name AS 'Doctor Assigned',
                            c.description AS 'Complaint',
                            t.description AS 'Treatment',
                            m.date_started AS 'Treatment Started',
                            m.date_ended AS 'Treatment Ended'
                        FROM patient p
                        LEFT JOIN bed b ON p.patient_no = b.patient_no
                        LEFT JOIN ward w ON b.ward_name = w.ward_name
                        JOIN doctor d ON p.doctor_id = d.doctor_id
                        JOIN medicalRecord m ON p.patient_no = m.patient_no
                        LEFT JOIN complaint c ON m.complaint_id = c.complaint_id
                        LEFT JOIN treatment t ON m.treatment_id = t.treatment_id
                        WHERE p.patient_no = ?";
                    $result = sqlsrv_query($conn, $sql, [$selPat]);
                    renderTable($result);
                    endif;

                // Query 11

                elseif ($report == 11):
                    $compList = sqlsrv_query($conn, "SELECT complaint_id, description FROM complaint ORDER BY description");
                    $selComp = isset($_GET['complaint_id']) ? (int) $_GET['complaint_id'] : 0;
                    $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
                    $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
                    ?>
                    <form method="GET" class="filter-form">
                        <input type="hidden" name="report" value="11">
                        <div class="form-group">
                            <label>Complaint</label>
                            <select name="complaint_id">
                                <option value="">-- Choose --</option>
                                <?php while ($c = sqlsrv_fetch_array($compList, SQLSRV_FETCH_ASSOC)): ?>
                                    <option value="<?php echo $c['complaint_id']; ?>" <?php echo $selComp == $c['complaint_id'] ? 'selected' : ''; ?>>
                                        <?php echo $c['description']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="date_from" value="<?php echo $dateFrom; ?>">
                        </div>
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="date_to" value="<?php echo $dateTo; ?>">
                        </div>
                        <button type="submit" class="btn">Search</button>
                    </form>
                    <?php
                    if ($selComp > 0 && $dateFrom && $dateTo):
                        $sql = "SELECT t.description AS 'Treatment',
                                c.description AS 'Complaint',
                                m.date_started AS 'Date Given'
                        FROM medicalRecord m
                        JOIN treatment t ON m.treatment_id = t.treatment_id
                        JOIN complaint c ON m.complaint_id = c.complaint_id
                        WHERE m.complaint_id = ?
                        AND m.date_started BETWEEN ? AND ?
                        ORDER BY t.description";
                        $result = sqlsrv_query($conn, $sql, [$selComp, $dateFrom, $dateTo]);
                        renderTable($result);
                    endif;

                    
                    // Query 12 

                    elseif ($report == 12):
                        $sql = "SELECT hospital_staff.position AS 'Position',
                        COUNT(*) AS 'Total Staff'
                        FROM (
                            SELECT position FROM doctor
                            UNION ALL
                            SELECT 'day sister' FROM daysister
                            UNION ALL
                            SELECT 'night sister' FROM nightsister
                            UNION ALL
                            SELECT 'staff nurse' FROM staffnurse
                            UNION ALL
                            SELECT 'non-registered nurse' FROM nonregsister
                        ) AS hospital_staff
                        GROUP BY hospital_staff.position
                        ORDER BY COUNT(*) DESC, hospital_staff.position";
                        $result = sqlsrv_query($conn, $sql);
                        renderTable($result);

                    else:
                        echo '<p class="no-data">Invalid report number.</p>';
                    endif;
                    ?>
            </div>
        <?php endif; ?>

</div>
</body>
</html>