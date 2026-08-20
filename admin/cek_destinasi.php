<?php

require_once __DIR__ . '/../config/database.php';

try {

    $db = (new Database())->getConnection();

    echo "<h2>TEST DATABASE BPW</h2>";

    // Database yang sedang digunakan
    $database = $db->query("SELECT DATABASE()")->fetchColumn();

    echo "<p><b>Database aktif:</b> " . htmlspecialchars($database) . "</p>";

    // Host/server
    $server = $db->query("SELECT @@hostname")->fetchColumn();

    echo "<p><b>MySQL Host:</b> " . htmlspecialchars($server) . "</p>";

    // Port
    $port = $db->query("SELECT @@port")->fetchColumn();

    echo "<p><b>MySQL Port:</b> " . htmlspecialchars($port) . "</p>";

    echo "<hr>";

    // Cek tabel
    $cekTabel = $db->query("
        SHOW TABLES LIKE 'destinasi'
    ");

    $tabel = $cekTabel->fetchColumn();

    if (!$tabel) {

        echo "<p style='color:red'>
            <b>TABEL destinasi TIDAK DITEMUKAN</b>
        </p>";

        exit;
    }

    echo "<p style='color:green'>
        <b>Tabel destinasi ditemukan.</b>
    </p>";

    // Hitung jumlah data
    $jumlah = $db->query("
        SELECT COUNT(*) 
        FROM destinasi
    ")->fetchColumn();

    echo "<p>
        <b>Jumlah data destinasi:</b>
        " . $jumlah . "
    </p>";

    echo "<hr>";

    // Tampilkan semua data
    $stmt = $db->query("
        SELECT *
        FROM destinasi
        ORDER BY id DESC
    ");

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$data) {

        echo "<p style='color:red'>
            Tabel destinasi ditemukan tetapi ISINYA KOSONG.
        </p>";

    } else {

        echo "<h3>DATA DESTINASI</h3>";

        echo "<table border='1' cellpadding='8' cellspacing='0'>";

        echo "<tr>";

        foreach (array_keys($data[0]) as $kolom) {
            echo "<th>" . htmlspecialchars($kolom) . "</th>";
        }

        echo "</tr>";

        foreach ($data as $row) {

            echo "<tr>";

            foreach ($row as $value) {

                echo "<td>" .
                    htmlspecialchars((string)$value) .
                    "</td>";
            }

            echo "</tr>";
        }

        echo "</table>";
    }

} catch (PDOException $e) {

    echo "<h2 style='color:red'>DATABASE ERROR</h2>";

    echo "<pre>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";
}