<?php
require __DIR__ . '/../core/database.php';
use core\Database;

function db(): Database
{
    $path = __DIR__ . '/../config/app.php';
    $config = include($path);
    $db = new Database($config['database']);

    return $db;
}
function createMember($postData) {
    $first_name = trim($postData['first_name'] ?? '');
    $last_name = trim($postData['last_name'] ?? '');
    $specialty = $postData['fkspecialty'] ?? null;
    $bio = trim($postData['bio'] ?? '');
    $email = trim($postData['email'] ?? '');
    $password = trim($postData['password'] ?? '');
    $is_active = isset($postData['is_active']) ? 1 : 0;
    $is_admin = isset($postData['is_admin']) ? 1 : 0;

    if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($password)) {
        $sql = 'INSERT INTO members (first_name, last_name, fkspecialty, bio, email, password_hash, is_active, is_admin) VALUES (:first_name, :last_name, :fkspecialty, :bio, :email, :password_hash, :is_active, :is_admin)';
        $result = db()->execute($sql, [
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':fkspecialty' => $specialty ? (int)$specialty : null,
            ':bio' => $bio,
            ':email' => $email,
            ':password_hash' => $password,
            ':is_active' => $is_active,
            ':is_admin' => $is_admin
        ]);

        if ($result === true) {
            return ['success' => 'Member created successfully!'];
        } else {
            return ['error' => 'Database operation failed unexpectedly.'];
        }
    } else {
        return ['error' => 'Please fill in all required fields.'];
    }
}

function editMember($postData) {
    $kmember = $postData['kmember'] ?? '';
    $first_name = trim($postData['first_name'] ?? '');
    $last_name = trim($postData['last_name'] ?? '');
    $specialty = $postData['fkspecialty'] ?? null;
    $bio = trim($postData['bio'] ?? '');
    $email = trim($postData['email'] ?? '');
    $is_active = isset($postData['is_active']) ? 1 : 0;
    $is_admin = isset($postData['is_admin']) ? 1 : 0;

    if (!empty($kmember) && !empty($first_name) && !empty($last_name) && !empty($email)) {
        $sql = 'UPDATE members SET first_name = :first_name, last_name = :last_name, fkspecialty = :fkspecialty, bio = :bio, email = :email, is_active = :is_active, is_admin = :is_admin WHERE kmember = :kmember';
        $result = db()->execute($sql, [
            ':kmember' => (int)$kmember,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':fkspecialty' => $specialty ? (int)$specialty : null,
            ':bio' => $bio,
            ':email' => $email,
            ':is_active' => $is_active,
            ':is_admin' => $is_admin
        ]);

        if ($result === true) {
            return ['success' => 'Member updated successfully!'];
        } else {
            return ['error' => 'Database operation failed unexpectedly.'];
        }
    } else {
        return ['error' => 'Please fill in all required fields.'];
    }
}

function deleteMember($postData) {
    $kmember = $postData['kmember'] ?? '';
    if (!empty($kmember)) {
        $sql = 'DELETE FROM members WHERE kmember = :kmember';
        $result = db()->execute($sql, [':kmember' => (int)$kmember]);

        if ($result === true) {
            return ['success' => 'Member deleted successfully!'];
        } else {
            return ['error' => 'Database operation failed unexpectedly.'];
        }
    } else {
        return ['error' => 'Invalid member ID.'];
    }
}

function toggleActiveMember($postData) {
    $kmember = $postData['kmember'] ?? '';
    if (!empty($kmember)) {
        $current = db()->query('SELECT is_active FROM members WHERE kmember = ?', [(int)$kmember])->findColumn();
        $new_active = $current ? 0 : 1;
        $sql = 'UPDATE members SET is_active = :is_active WHERE kmember = :kmember';
        $result = db()->execute($sql, [
            ':is_active' => $new_active,
            ':kmember' => (int)$kmember
        ]);

        if ($result === true) {
            return ['success' => 'Member status updated successfully!'];
        } else {
            return ['error' => 'Database operation failed unexpectedly.'];
        }
    } else {
        return ['error' => 'Invalid member ID.'];
    }
}