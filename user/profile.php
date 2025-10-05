<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header("Location: /qa-platform/auth/login.php");
    exit();
}

$user = getUserById($_SESSION['user_id']);
$page_title = "Profile - " . $user['username'];

$database = new Database();
$db = $database->getConnection();

// Get user statistics
$statsQuery = "SELECT 
    (SELECT COUNT(*) FROM questions WHERE user_id = :user_id) as question_count,
    (SELECT COUNT(*) FROM answers WHERE user_id = :user_id) as answer_count,
    (SELECT SUM(likes) FROM questions WHERE user_id = :user_id) as total_question_likes,
    (SELECT SUM(likes) FROM answers WHERE user_id = :user_id) as total_answer_likes";
$statsStmt = $db->prepare($statsQuery);
$statsStmt->bindParam(':user_id', $_SESSION['user_id']);
$statsStmt->execute();
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-circle mb-3">
                        <i class="bi bi-person-circle" style="font-size: 100px; color: #0d6efd;"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    <small class="text-muted">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></small>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Statistics</h6>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Questions Asked</span>
                        <strong><?php echo $stats['question_count']; ?></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Answers Posted</span>
                        <strong><?php echo $stats['answer_count']; ?></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Question Likes</span>
                        <strong><?php echo $stats['total_question_likes'] ?? 0; ?></strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Answer Likes</span>
                        <strong><?php echo $stats['total_answer_likes'] ?? 0; ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get recent questions
                    $recentQuery = "SELECT q.*, COUNT(a.id) as answer_count 
                                   FROM questions q 
                                   LEFT JOIN answers a ON q.id = a.question_id 
                                   WHERE q.user_id = :user_id 
                                   GROUP BY q.id 
                                   ORDER BY q.created_at DESC 
                                   LIMIT 5";
                    $recentStmt = $db->prepare($recentQuery);
                    $recentStmt->bindParam(':user_id', $_SESSION['user_id']);
                    $recentStmt->execute();
                    $recentQuestions = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if (count($recentQuestions) > 0): ?>
                        <h6 class="mb-3">Your Recent Questions</h6>
                        <?php foreach ($recentQuestions as $question): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <h6>
                                    <a href="/qa-platform/questions/view_question.php?id=<?php echo $question['id']; ?>">
                                        <?php echo htmlspecialchars($question['title']); ?>
                                    </a>
                                </h6>
                                <div>
                                    <span class="badge bg-info"><?php echo $question['answer_count']; ?> answers</span>
                                    <span class="badge bg-secondary"><?php echo $question['likes']; ?> likes</span>
                                    <small class="text-muted ms-2"><?php echo timeAgo($question['created_at']); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="my_questions.php" class="btn btn-primary btn-sm">View All Questions</a>
                    <?php else: ?>
                        <p class="text-muted">You haven't asked any questions yet.</p>
                        <a href="/qa-platform/questions/ask_question.php" class="btn btn-primary">Ask Your First Question</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>