<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

$question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$question_id) {
    header("Location: /qa-platform/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Update view count
$updateView = "UPDATE questions SET views = views + 1 WHERE id = :id";
$stmt = $db->prepare($updateView);
$stmt->bindParam(':id', $question_id);
$stmt->execute();

// Get question details
$query = "SELECT q.*, u.username, u.id as user_id_author 
          FROM questions q 
          JOIN users u ON q.user_id = u.id 
          WHERE q.id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $question_id);
$stmt->execute();
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$question) {
    header("Location: /qa-platform/index.php");
    exit();
}

// Get answers
$answerQuery = "SELECT a.*, u.username 
                FROM answers a 
                JOIN users u ON a.user_id = u.id 
                WHERE a.question_id = :question_id 
                ORDER BY a.likes DESC, a.created_at ASC";
$answerStmt = $db->prepare($answerQuery);
$answerStmt->bindParam(':question_id', $question_id);
$answerStmt->execute();
$answers = $answerStmt->fetchAll(PDO::FETCH_ASSOC);

// Get related questions
$relatedQuestions = getRelatedQuestions($question_id, $question['category']);

$page_title = $question['title'];
include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-9">
            <!-- Question Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="vote-section text-center me-4">
                            <?php if (isLoggedIn()): ?>
                                <button class="btn btn-sm btn-outline-primary like-btn" 
                                        data-type="question" 
                                        data-id="<?php echo $question['id']; ?>"
                                        <?php echo hasUserLikedQuestion($_SESSION['user_id'], $question['id']) ? 'disabled' : ''; ?>>
                                    <i class="bi bi-hand-thumbs-up"></i>
                                </button>
                            <?php endif; ?>
                            <div class="vote-count my-2 fs-4" id="question-likes-<?php echo $question['id']; ?>">
                                <?php echo $question['likes']; ?>
                            </div>
                            <small class="text-muted">likes</small>
                        </div>
                        
                        <div class="flex-grow-1">
                            <h2><?php echo htmlspecialchars($question['title']); ?></h2>
                            
                            <div class="mb-3">
                                <?php if ($question['category']): ?>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($question['category']); ?></span>
                                <?php endif; ?>
                                <span class="badge bg-info"><?php echo $question['views']; ?> views</span>
                            </div>
                            
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($question['description'])); ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    Asked by <strong><?php echo htmlspecialchars($question['username']); ?></strong>
                                    <?php echo timeAgo($question['created_at']); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Answers Section -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><?php echo count($answers); ?> Answer<?php echo count($answers) != 1 ? 's' : ''; ?></h5>
                </div>
                <div class="card-body">
                    <?php if (count($answers) > 0): ?>
                        <?php foreach ($answers as $answer): ?>
                            <div class="answer-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="vote-section text-center me-3">
                                        <?php if (isLoggedIn()): ?>
                                            <button class="btn btn-sm btn-outline-success like-btn" 
                                                    data-type="answer" 
                                                    data-id="<?php echo $answer['id']; ?>"
                                                    <?php echo hasUserLikedAnswer($_SESSION['user_id'], $answer['id']) ? 'disabled' : ''; ?>>
                                                <i class="bi bi-hand-thumbs-up"></i>
                                            </button>
                                        <?php endif; ?>
                                        <div class="vote-count my-2" id="answer-likes-<?php echo $answer['id']; ?>">
                                            <?php echo $answer['likes']; ?>
                                        </div>
                                        <small class="text-muted">likes</small>
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <p><?php echo nl2br(htmlspecialchars($answer['answer_text'])); ?></p>
                                        <small class="text-muted">
                                            Answered by <strong><?php echo htmlspecialchars($answer['username']); ?></strong>
                                            <?php echo timeAgo($answer['created_at']); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No answers yet. Be the first to answer!</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Answer Form -->
            <?php if (isLoggedIn()): ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Your Answer</h5>
                    </div>
                    <div class="card-body">
                        <form id="answerForm" method="POST" action="../answers/post_answer.php">
                            <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                            <div class="mb-3">
                                <textarea name="answer_text" class="form-control" rows="5" 
                                          placeholder="Write your answer here..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Post Answer
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Please <a href="/qa-platform/auth/login.php">login</a> to answer this question.
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-3">
            <?php if (count($relatedQuestions) > 0): ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-link-45deg"></i> Related Questions</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($relatedQuestions as $related): ?>
                            <a href="view_question.php?id=<?php echo $related['id']; ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="fw-bold small"><?php echo htmlspecialchars($related['title']); ?></div>
                                <small class="text-muted">
                                    <?php echo $related['answer_count']; ?> answers
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>