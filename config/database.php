<?php
/**
 * ============================================================
 * CONFIG - DATABASE & HELPER GLOBAL
 * Bayu Prima Wisata
 * ============================================================
 */

class Database {
    private $host     = 'localhost';
    private $db_name  = 'db_bpw';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->conn;
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
}


/* ============================================================
 * HELPER DATABASE DASAR
 * ============================================================ */

function db_query($sql, $params = []) {
    $db   = (new Database())->getConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_get($sql, $params = []) {
    return db_query($sql, $params)->fetch();
}

function db_get_all($sql, $params = []) {
    return db_query($sql, $params)->fetchAll();
}

function db_insert($table, $data) {
    $db           = (new Database())->getConnection();
    $fields       = array_keys($data);
    $placeholders = ':' . implode(', :', $fields);
    $sql          = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES (" . $placeholders . ")";
    $stmt         = $db->prepare($sql);

    foreach ($data as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->execute();

    return $db->lastInsertId();
}

function db_update($table, $data, $where, $whereValue) {
    $db  = (new Database())->getConnection();
    $set = [];

    foreach ($data as $key => $value) {
        $set[] = "$key = :$key";
    }

    $sql  = "UPDATE $table SET " . implode(', ', $set) . " WHERE $where = :where_value";
    $stmt = $db->prepare($sql);

    foreach ($data as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':where_value', $whereValue);

    return $stmt->execute();
}

function db_delete($table, $where, $whereValue) {
    $db   = (new Database())->getConnection();
    $sql  = "DELETE FROM $table WHERE $where = :where_value";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':where_value', $whereValue);
    return $stmt->execute();
}

function db_count($table, $where = '', $params = []) {
    $sql = "SELECT COUNT(*) as total FROM $table";
    if ($where) $sql .= " WHERE $where";
    $result = db_get($sql, $params);
    return $result['total'];
}


/* ============================================================
 * HELPER UPLOAD FILE
 * ============================================================ */

function upload_file($file, $folder, $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;

    $filename = time() . '_' . uniqid() . '.' . $ext;
    $target   = "../uploads/$folder/" . $filename;

    if (!is_dir("../uploads/$folder")) {
        mkdir("../uploads/$folder", 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return "uploads/$folder/" . $filename;
    }

    return null;
}


/* ============================================================
 * HELPER PESAN MASUK (NOTIFIKASI ADMIN)
 * ============================================================ */

function kirim_pesan_admin($tipe, $judul, $pesan, $link = null, $pengirim = null, $email = null, $no_hp = null) {
    try {
        db_insert('pesan_masuk', [
            'tipe'       => $tipe,
            'judul'      => $judul,
            'pesan'      => $pesan,
            'link'       => $link,
            'pengirim'   => $pengirim,
            'email'      => $email,
            'no_hp'      => $no_hp,
            'is_read'    => 0,
            'is_starred' => 0,
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function hitung_pesan_belum_dibaca() {
    try {
        $row = db_get("SELECT COUNT(*) AS total FROM pesan_masuk WHERE is_read = 0");
        return (int) ($row['total'] ?? 0);
    } catch (PDOException $e) {
        return 0;
    }
}

function statistik_pesan_masuk() {
    $default = [
        'total'      => 0,
        'unread'     => 0,
        'starred'    => 0,
        'testimoni'  => 0,
        'blog'       => 0,
        'kontak'     => 0,
        'newsletter' => 0,
        'review'     => 0,
    ];

    try {
        $row = db_get("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread,
                SUM(CASE WHEN is_starred = 1 THEN 1 ELSE 0 END) AS starred,
                SUM(CASE WHEN tipe = 'testimoni' THEN 1 ELSE 0 END) AS testimoni,
                SUM(CASE WHEN tipe = 'blog' THEN 1 ELSE 0 END) AS blog,
                SUM(CASE WHEN tipe = 'kontak' THEN 1 ELSE 0 END) AS kontak,
                SUM(CASE WHEN tipe = 'newsletter' THEN 1 ELSE 0 END) AS newsletter,
                SUM(CASE WHEN tipe = 'review' THEN 1 ELSE 0 END) AS review
            FROM pesan_masuk
        ");

        if ($row) {
            foreach ($default as $k => $v) {
                $default[$k] = (int) ($row[$k] ?? 0);
            }
        }
    } catch (PDOException $e) {
        // biarkan default
    }

    return $default;
}