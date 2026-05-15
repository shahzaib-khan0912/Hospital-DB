<!-- header.php is the seperate file for the side bar and the main header -->
<?php if (!isset($active_page))
    $active_page = ''; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' — ' : ''; ?>Ivor Paine Hospital</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <div class="icon">🏥</div>
            <h2>Ivor Paine<br>Memorial Hospital</h2>
        </div>

        <div class="section-title">Main</div>
        <a href="main.php" class="<?php echo $active_page == 'dashboard' ? 'active' : ''; ?>">
            📊 Dashboard
        </a>

        <div class="section-title">Forms</div>
        <a href="patient_form.php" class="<?php echo $active_page == 'patient' ? 'active' : ''; ?>">
            📋 Patient Record
        </a>
        <a href="ward_form.php" class="<?php echo $active_page == 'ward' ? 'active' : ''; ?>">
            📋 Ward Record
        </a>
        <a href="team_form.php" class="<?php echo $active_page == 'team' ? 'active' : ''; ?>">
            📋 Consultant Team
        </a>

        <div class="section-title">Reports</div>
        <a href="reports.php?report=1"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 1) ? 'active' : ''; ?>">
            <span class="nav-num">1</span> Consultant Teams
        </a>
        <a href="reports.php?report=2"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 2) ? 'active' : ''; ?>">
            <span class="nav-num">2</span> Ward Details
        </a>
        <a href="reports.php?report=3"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 3) ? 'active' : ''; ?>">
            <span class="nav-num">3</span> Patient Treatments
        </a>
        <a href="reports.php?report=4"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 4) ? 'active' : ''; ?>">
            <span class="nav-num">4</span> Junior Housemen
        </a>
        <a href="reports.php?report=5"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 5) ? 'active' : ''; ?>">
            <span class="nav-num">5</span> Specialties
        </a>
        <a href="reports.php?report=6"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 6) ? 'active' : ''; ?>">
            <span class="nav-num">6</span> Doctor Experience
        </a>
        <a href="reports.php?report=7"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 7) ? 'active' : ''; ?>">
            <span class="nav-num">7</span> Multi-Complaints
        </a>
        <a href="reports.php?report=8"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 8) ? 'active' : ''; ?>">
            <span class="nav-num">8</span> Treatment Groups
        </a>
        <a href="reports.php?report=9"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 9) ? 'active' : ''; ?>">
            <span class="nav-num">9</span> Performance History
        </a>
        <a href="reports.php?report=10"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 10) ? 'active' : ''; ?>">
            <span class="nav-num">10</span> Patient Details
        </a>
        <a href="reports.php?report=11"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 11) ? 'active' : ''; ?>">
            <span class="nav-num">11</span> Treatments by Date
        </a>
        <a href="reports.php?report=12"
            class="<?php echo ($active_page == 'reports' && isset($_GET['report']) && $_GET['report'] == 12) ? 'active' : ''; ?>">
            <span class="nav-num">12</span> Staff Positions
        </a>
    </nav>