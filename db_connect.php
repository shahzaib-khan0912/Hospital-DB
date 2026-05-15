<?php

class Database
{
    private $serverName = "localhost\\SQLEXPRESS";
    private $connectionOptions = array(
        "Database" => "ivor_paine_hospital",
        "Uid" => "",
        "PWD" => "",
        "TrustServerCertificate" => true
    );

    public function connect()
    {
        $conn = sqlsrv_connect($this->serverName, $this->connectionOptions);
        if ($conn === false) {
            die("<div class='error-box'><h3>Database Connection Failed</h3><pre>"
                . print_r(sqlsrv_errors(), true) . "</pre></div>");
        }
        return $conn;
    }
}
// this function renders the table for all the reports 
function renderTable($result)
{
    if (!$result) {
        echo '<p class="no-data">Query execution failed.</p>';
        return;
    }

    $meta = sqlsrv_field_metadata($result);
    if (!$meta) {
        echo '<p class="no-data">No metadata available.</p>';
        return;
    }

    $rows = [];
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $rows[] = $row;
    }

    if (count($rows) === 0) {
        echo '<p class="no-data">No records found.</p>';
        return;
    }

    echo '<div class="table-container">';
    echo '<table>';

    echo '<thead><tr>';
    foreach ($meta as $field) {
        echo '<th>' . htmlspecialchars($field['Name']) . '</th>';
    }
    echo '</tr></thead>';

    echo '<tbody>';
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($row as $value) {
            if ($value instanceof DateTime) {
                echo '<td>' . $value->format('Y-m-d') . '</td>';
            } else {
                echo '<td>' . htmlspecialchars($value ?? '—') . '</td>';
            }
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}
?>