<?php
// Form 2: Ward Record Lookup
$active_page = 'ward';
$page_title = 'Ward Record';

include 'db_connect.php';
include 'header.php';

$db = new Database();
$conn = $db->connect();

$wardList = sqlsrv_query($conn, "SELECT ward_name FROM ward ORDER BY ward_name");

$selected = isset($_GET['ward_name']) ? $_GET['ward_name'] : '';
?>

<div class="main-content">
    <h1 class="page-title">Ward Record</h1>
    <p class="page-subtitle">Select a ward to view its details, staff and bed occupancy</p>

    <!-- selector for ward as well -->
    <div class="card">
        <h3>Select Ward</h3>
        <form method="GET" class="filter-form">
            <div class="form-group">
                <label>Ward Name</label>
                <select name="ward_name" onchange="this.form.submit()">
                    <option value="">-- Choose a Ward --</option>
                    <?php while ($w = sqlsrv_fetch_array($wardList, SQLSRV_FETCH_ASSOC)): ?>
                        <option value="<?php echo $w['ward_name']; ?>" <?php echo $selected == $w['ward_name'] ? 'selected' : ''; ?>>
                            <?php echo $w['ward_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if ($selected !== ''):
        // ward info with sisters
        $infoSql = "SELECT w.ward_name, w.spec_name,
                           nd.name AS day_sister, nn.name AS night_sister
                    FROM ward w
                    LEFT JOIN nurse nd ON w.day_sister_no = nd.staff_no
                    LEFT JOIN nurse nn ON w.night_sister_no = nn.staff_no
                    WHERE w.ward_name = ?";
        $infoResult = sqlsrv_query($conn, $infoSql, [$selected]);
        $info = sqlsrv_fetch_array($infoResult, SQLSRV_FETCH_ASSOC);

        if ($info): ?>
            <div class="card">
                <h3>Ward Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Ward Name</label>
                        <span><?php echo $info['ward_name']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Specialty</label>
                        <span><?php echo $info['spec_name']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Day Sister</label>
                        <span><?php echo $info['day_sister'] ?? '—'; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Night Sister</label>
                        <span><?php echo $info['night_sister'] ?? '—'; ?></span>
                    </div>
                </div>
            </div>
            <div class="card">
                <h3>Care Units</h3>
                <?php
                $cuSql = "SELECT cu.care_unit_no AS 'Care Unit',
                             n.name AS 'Staff Nurse In Charge'
                      FROM careunit cu
                      LEFT JOIN nurse n ON cu.incharge_staff_no = n.staff_no
                      WHERE cu.ward_name = ?";
                $cuResult = sqlsrv_query($conn, $cuSql, [$selected]);
                renderTable($cuResult);
                ?>
            </div>
            <div class="card">
                <h3>Bed Occupancy</h3>
                <?php
                $bedSql = "SELECT b.bed_no AS 'Bed No',
                              p.patient_no AS 'Patient No',
                              p.patient_name AS 'Patient Name',
                              cu.care_unit_no AS 'Care Unit',
                              cons.name AS 'Consultant',
                              b.date_admitted AS 'Date Admitted'
                       FROM bed b
                       LEFT JOIN patient p ON b.patient_no = p.patient_no
                       LEFT JOIN careunit cu ON p.care_unit_no = cu.care_unit_no
                       LEFT JOIN doctor d ON p.doctor_id = d.doctor_id
                       LEFT JOIN doctor cons ON d.consultant_id = cons.doctor_id
                       WHERE b.ward_name = ?
                       ORDER BY b.bed_no";
                $bedResult = sqlsrv_query($conn, $bedSql, [$selected]);
                renderTable($bedResult);
                ?>
            </div>
        <?php else: ?>
            <p class="no-data">Ward not found.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>

</html>