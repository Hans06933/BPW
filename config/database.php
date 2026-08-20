<?php
// config/database.php

class Database {
    private $host = 'localhost';
    private $db_name = 'db_bpw';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->conn;
        } catch(PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
}

// Fungsi helper global
function db_query($sql, $params = []) {
    $db = (new Database())->getConnection();
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
    $db = (new Database())->getConnection();
    $fields = array_keys($data);
    $placeholders = ':' . implode(', :', $fields);
    $sql = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES (" . $placeholders . ")";
    $stmt = $db->prepare($sql);
    foreach ($data as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->execute();
    return $db->lastInsertId();
}

function db_update($table, $data, $where, $whereValue) {
    $db = (new Database())->getConnection();
    $set = [];
    foreach ($data as $key => $value) {
        $set[] = "$key = :$key";
    }
    $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE $where = :where_value";
    $stmt = $db->prepare($sql);
    foreach ($data as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':where_value', $whereValue);
    return $stmt->execute();
}

function db_delete($table, $where, $whereValue) {
    $db = (new Database())->getConnection();
    $sql = "DELETE FROM $table WHERE $where = :where_value";
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

function upload_file($file, $folder, $allowed = ['jpg','jpeg','png','gif','webp']) {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $target = "../uploads/$folder/" . $filename;
    if (!is_dir("../uploads/$folder")) mkdir("../uploads/$folder", 0777, true);
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return "uploads/$folder/" . $filename;
    }
    return null;
}