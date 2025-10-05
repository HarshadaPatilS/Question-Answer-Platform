<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

// Get platform statistics
$stats = [];

// Total users
$userQuery = "SELECT COUNT(*) as count FROM users";
$userStmt = $db->prepare($userQuery);
$userStmt->execute();
$stats['total_users'] = $userStmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total questions
$questionQuery = "SELECT COUNT(*) as count FROM questions";
$questionStmt = $db->prepare($questionQuery);
$questionStmt->execute();
$stats['total_questions'] = $questionStmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total answers
$answerQuery = "SELECT COUNT(*) as count FROM answers";
$answerStmt = $db->prepare($answerQuery);
$answerStmt->execute();
$stats['total_answers'] = $answerStmt->fetch(PDO::FETCH_ASSOC)['count'];

// Questions answered percentage
$answeredQuery = "SELECT COUNT(DISTINCT question_id) as count FROM answers";
$answeredStmt = $db->prepare($answeredQuery);
$answeredStmt->execute();
$answered_count = $answeredStmt->fetch(PDO::FETCH_ASSOC)['count'];
$stats['answered_percentage'] = $stats['total_questions'] > 0 
    ? round(($answered_count / $stats['total_questions']) * 100, 2) 
    : 0;

echo json_encode($stats);
?>