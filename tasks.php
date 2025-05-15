<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'config.php';

$email = $_SESSION['email'];

$userQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userQuery->bind_param("s", $email);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit();
}
$user = $userResult->fetch_assoc();
$user_id = $user['id'];

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$taskId = null;

if (preg_match('/tasks\.php\/(\d+)/', $uri, $matches)) {
    $taskId = (int)$matches[1];
}

switch ($method) {
    case 'GET':
        $stmt = $conn->prepare("SELECT id, title, description FROM tasks WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        echo json_encode($tasks);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['title'], $data['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $data['title'], $data['description']);
        $stmt->execute();
        echo json_encode(['status' => 'Task added']);
        break;

    case 'PUT':
        if ($taskId === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Task ID missing']);
            exit();
        }
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['title'], $data['description'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            exit();
        }
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $data['title'], $data['description'], $taskId, $user_id);
        $stmt->execute();
        echo json_encode(['status' => 'Task updated']);
        break;

    case 'DELETE':
        if ($taskId === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Task ID missing']);
            exit();
        }
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $taskId, $user_id);
        $stmt->execute();
        echo json_encode(['status' => 'Task deleted']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
