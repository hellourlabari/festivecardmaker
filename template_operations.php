<?php
header('Content-Type: application/json');
require_once 'db_config.php';

// Create templates table if it doesn't exist
try {
    $sql = "CREATE TABLE IF NOT EXISTS templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        image_url TEXT NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
} catch(PDOException $e) {
    die(json_encode(['error' => 'Table creation failed: ' . $e->getMessage()]));
}

$action = $_POST['action'] ?? '';

switch($action) {
    case 'add':
        try {
            $stmt = $pdo->prepare("INSERT INTO templates (title, image_url, description) VALUES (?, ?, ?)");
            $stmt->execute([
                $_POST['title'],
                $_POST['image'],
                $_POST['description']
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to add template: ' . $e->getMessage()]);
        }
        break;

    case 'update':
        try {
            $stmt = $pdo->prepare("UPDATE templates SET title = ?, image_url = ?, description = ? WHERE id = ?");
            $stmt->execute([
                $_POST['title'],
                $_POST['image'],
                $_POST['description'],
                $_POST['id']
            ]);
            echo json_encode(['success' => true]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to update template: ' . $e->getMessage()]);
        }
        break;

    case 'delete':
        try {
            $stmt = $pdo->prepare("DELETE FROM templates WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            echo json_encode(['success' => true]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to delete template: ' . $e->getMessage()]);
        }
        break;

    case 'get':
        try {
            $stmt = $pdo->query("SELECT * FROM templates ORDER BY created_at DESC");
            $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'templates' => $templates]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to fetch templates: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?> 