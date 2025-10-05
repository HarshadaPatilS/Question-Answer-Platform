<?php
require_once '../includes/functions.php';

// Simple admin check (you should implement proper role-based access)
if (!isLoggedIn() || $_SESSION['username'] !== 'admin') {
    header("Location: /qa-platform/index.php");
    exit();
}

$page_title = "Admin Dashboard";

$database = new Database();
$db = $database->getConnection();

// Get statistics
$statsQuery = "SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM questions) as total_questions,
    (SELECT COUNT(*) FROM answers) as total_answers,
    (SELECT COUNT(*) FROM question_likes) as total_question_likes,
    (SELECT COUNT(*) FROM answer_likes) as total_answer_likes";
$statsStmt = $db->prepare($statsQuery);
$statsStmt->execute();
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Get recent activity
$activityQuery = "SELECT 'question' as type, q.id, q.title as content, u.username, q.created_at 
                  FROM questions q 
                  JOIN users u ON q.user_id = u.id 
                  UNION ALL 
                  SELECT 'answer' as type, a.id, SUBSTRING(a.answer_text, 1, 50) as content, u.username, a.created_at 
                  FROM answers a 
                  JOIN users u ON a.user_id = u.id 
                  ORDER BY created_at DESC 
                  LIMIT 10";
$activityStmt = $db->prepare($activityQuery);
$activityStmt->execute();
$activities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Admin Dashboard</h2>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2><?php echo $stats['total_users']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Questions</h5>
                    <h2><?php echo $stats['total_questions']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Answers</h5>
                    <h2><?php echo $stats['total_answers']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Total Likes</h5>
                    <h2><?php echo $stats['total_question_likes'] + $stats['total_answer_likes']; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Activity</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Content</th>
                            <th>User</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-<?php echo $activity['type'] == 'question' ? 'primary' : 'success'; ?>">
                                        <?php echo ucfirst($activity['type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($activity['content']); ?></td>
                                <td><?php echo htmlspecialchars($activity['username']); ?></td>
                                <td><?php echo timeAgo($activity['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
