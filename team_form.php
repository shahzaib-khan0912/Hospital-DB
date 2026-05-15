<?php
// Form 3: Consultant Team Record 
$active_page = 'team';
$page_title = 'Consultant Team';

include 'db_connect.php';
include 'header.php';

$db = new Database();
$conn = $db->connect();

$consList = sqlsrv_query(
    $conn,
    "SELECT d.doctor_id, d.name, c.spec_name
     FROM doctor d JOIN consultant c ON d.doctor_id = c.doctor_id
     ORDER BY d.name"
);

$selected = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;
?>

<div class="main-content">
    <h1 class="page-title">Consultant Team Record</h1>
    <p class="page-subtitle">Select a consultant to view their team, performance records and experience</p>

    <div class="card">
        <h3>Select Consultant</h3>
        <form method="GET" class="filter-form">
            <div class="form-group">
                <label>Consultant</label>
                <select name="doctor_id" onchange="this.form.submit()">
                    <option value="">-- Choose a Consultant --</option>
                    <?php while ($c = sqlsrv_fetch_array($consList, SQLSRV_FETCH_ASSOC)): ?>
                        <option value="<?php echo $c['doctor_id']; ?>" <?php echo $selected == $c['doctor_id'] ? 'selected' : ''; ?>>
                            <?php echo $c['name'] . ' (' . $c['spec_name'] . ')'; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if ($selected > 0):
        //Consultant info
        $infoSql = "SELECT d.name, d.doctor_id, c.spec_name, c.date_joined
                    FROM doctor d JOIN consultant c ON d.doctor_id = c.doctor_id
                    WHERE d.doctor_id = ?";
        $infoResult = sqlsrv_query($conn, $infoSql, [$selected]);
        $info = sqlsrv_fetch_array($infoResult, SQLSRV_FETCH_ASSOC);

        if ($info): ?>
            <div class="card">
                <h3>Consultant Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Name</label>
                        <span><?php echo $info['name']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Doctor ID</label>
                        <span><?php echo $info['doctor_id']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Specialty</label>
                        <span><?php echo $info['spec_name']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Date Joined</label>
                        <span><?php echo $info['date_joined'] instanceof DateTime ? $info['date_joined']->format('Y-m-d') : $info['date_joined']; ?></span>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Team Members</h3>
                <?php
                $teamSql = "SELECT d.doctor_id AS 'ID',
                               d.name AS 'Name',
                               d.position AS 'Position',
                               c.date_joined AS 'Date Joined Team'
                        FROM doctor d
                        LEFT JOIN consultant c ON d.consultant_id = c.doctor_id
                        WHERE d.consultant_id = ?
                        ORDER BY d.name";
                $teamResult = sqlsrv_query($conn, $teamSql, [$selected]);
                renderTable($teamResult);
                ?>
            </div>
            <div class="card">
                <h3>Team Performance History</h3>
                <?php
                $perfSql = "SELECT d.name AS 'Doctor',
                               ph.date_recorded AS 'Review Date',
                               ph.grade AS 'Grade'
                        FROM performancehistory ph
                        JOIN doctor d ON ph.doctor_id = d.doctor_id
                        WHERE d.consultant_id = ?
                        ORDER BY d.name, ph.date_recorded";
                $perfResult = sqlsrv_query($conn, $perfSql, [$selected]);
                renderTable($perfResult);
                ?>
            </div>

            <div class="card">
                <h3>Team Previous Experience</h3>
                <?php
                $expSql = "SELECT d.name AS 'Doctor',
                              pe.position AS 'Past Position',
                              pe.establishment AS 'Hospital/Establishment',
                              pe.from_date AS 'From',
                              pe.to_date AS 'To'
                       FROM preexperience pe
                       JOIN doctor d ON pe.doctor_id = d.doctor_id
                       WHERE d.consultant_id = ?
                       ORDER BY d.name, pe.from_date";
                $expResult = sqlsrv_query($conn, $expSql, [$selected]);
                renderTable($expResult);
                ?>
            </div>
        <?php else: ?>
            <p class="no-data">Consultant not found.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>

</html>