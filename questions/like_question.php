<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_id = (int)$_POST['question_id'];
    $user_id = $_SESSION['user_id'];

    $database = new Database();
    $db = $database->getConnection();

    // Check if already liked
    $checkQuery = "SELECT id FROM question_likes WHERE user_id = :user_id AND question_id = :question_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':user_id', $user_id);
    $checkStmt->bindParam(':question_id', $question_id);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Already liked']);
        exit();
    }

    try {
        $db->beginTransaction();

        // Insert like
        $likeQuery = "INSERT INTO question_likes (user_id, question_id) VALUES (:user_id, :question_id)";
        $likeStmt = $db->prepare($likeQuery);
        $likeStmt->bindParam(':user_id', $user_id);
        $likeStmt->bindParam(':question_id', $question_id);
        $likeStmt->execute();

        // Update like count
        $updateQuery = "UPDATE questions SET likes = likes + 1 WHERE id = :question_id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':question_id', $question_id);
        $updateStmt->execute();

        // Get new like count
        $countQuery = "SELECT likes FROM questions WHERE id = :question_id";
        $countStmt = $db->prepare($countQuery);
        $countStmt->bindParam(':question_id', $question_id);
        $countStmt->execute();
        $result = $countStmt->fetch(PDO::FETCH_ASSOC);

        $db->commit();

        echo json_encode([
            'success' => true, 
            'likes' => $result['likes'],
            'message' => 'Question liked successfully'
        ]);
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>