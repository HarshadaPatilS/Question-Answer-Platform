<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Q&A Platform'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/qa-platform/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/qa-platform/index.php">
                <i class="bi bi-chat-square-text"></i> Q&A Platform
            </a>
            <form class="d-flex me-auto ms-3" method="GET" action="/qa-platform/search.php" style="width: 400px;">
    <div class="input-group">
        <input class="form-control" type="search" name="q" placeholder="Search questions..." aria-label="Search">
        <button class="btn btn-outline-light" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/qa-platform/index.php">Home</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/qa-platform/questions/ask_question.php">Ask Question</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/qa-platform/user/my_questions.php">My Questions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/qa-platform/user/profile.php">
                                <i class="bi bi-person"></i> <?php echo $_SESSION['username']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/qa-platform/auth/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/qa-platform/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/qa-platform/auth/signup.php">Sign Up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


