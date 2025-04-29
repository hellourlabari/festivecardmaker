<?php
header('Content-Type: application/json');
require_once 'config.php';

// Handle CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle different requests
switch ($method) {
    case 'GET':
        if ($action === 'get_all') {
            // Get all templates
            $sql = "SELECT * FROM templates ORDER BY created_at DESC";
            $result = $conn->query($sql);
            
            $templates = array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $templates[] = $row;
                }
            }
            
            echo json_encode(['success' => true, 'data' => $templates]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'create') {
            // Create new template
            $title = $conn->real_escape_string($data['title']);
            $image = $conn->real_escape_string($data['image']);
            $description = $conn->real_escape_string($data['description']);
            
            $sql = "INSERT INTO templates (title, image_url, description) VALUES ('$title', '$image', '$description')";
            
            if ($conn->query($sql) === TRUE) {
                $data['id'] = $conn->insert_id;
                echo json_encode(['success' => true, 'message' => 'Template created successfully', 'data' => $data]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'update' && isset($data['id'])) {
            // Update template
            $id = $conn->real_escape_string($data['id']);
            $title = $conn->real_escape_string($data['title']);
            $image = $conn->real_escape_string($data['image']);
            $description = $conn->real_escape_string($data['description']);
            
            $sql = "UPDATE templates SET title='$title', image_url='$image', description='$description' WHERE id=$id";
            
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Template updated successfully', 'data' => $data]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
        }
        break;

    case 'DELETE':
        if ($action === 'delete' && isset($_GET['id'])) {
            // Delete template
            $id = $conn->real_escape_string($_GET['id']);
            
            $sql = "DELETE FROM templates WHERE id=$id";
            
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Template deleted successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

$conn->close();
?> 