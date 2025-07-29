<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Inizializza il database se necessario
initializeDatabase();

// Gestione invio nuovo post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') !== 'reply') {
    $author = trim($_POST['author'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($author) && !empty($content)) {
        addPost($author, $content);
        header('Location: index.php');
        exit;
    }
}

// Gestione invio risposta
if ($_POST['action'] ?? '' === 'reply') {
    $author = trim($_POST['author'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $parent_id = (int)($_POST['parent_id'] ?? 0);
    
    if (!empty($author) && !empty($content) && $parent_id > 0) {
        $result = addReply($author, $content, $parent_id);
        header('Location: index.php');
        exit;
    }
}

// Recupera gli ultimi 10 post con le loro risposte
$posts = getLatestPosts(10);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Forum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="forum-header">
            <h1><i class="fas fa-comments"></i> Mini Forum Professionale</h1>
            <p class="subtitle">Piattaforma di discussione per dimostrazione aziendale</p>
        </header>

        <!-- Form nuovo post -->
        <div class="post-form-container">
            <h2><i class="fas fa-plus-circle"></i> Nuovo Post</h2>
            <form method="POST" class="post-form">
                <input type="hidden" name="action" value="new_post">
                <div class="form-group">
                    <label for="author">Nome Autore:</label>
                    <input type="text" id="author" name="author" required maxlength="50" placeholder="Il tuo nome">
                </div>
                <div class="form-group">
                    <label for="content">Contenuto:</label>
                    <textarea id="content" name="content" required maxlength="1000" placeholder="Scrivi il tuo messaggio..."></textarea>
                    <div class="char-counter">0/1000 caratteri</div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Pubblica Post
                </button>
            </form>
        </div>

        <!-- Lista post -->
        <div class="posts-container">
            <h2><i class="fas fa-list"></i> Ultimi Post (<?= count($posts) ?>)</h2>
            
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <i class="fas fa-comment-slash"></i>
                    <p>Nessun post ancora pubblicato. Sii il primo a scrivere!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post" data-post-id="<?= $post['id'] ?>">
                        <div class="post-header">
                            <div class="post-author">
                                <i class="fas fa-user-circle"></i>
                                <strong><?= htmlspecialchars($post['author']) ?></strong>
                            </div>
                            <div class="post-date">
                                <i class="fas fa-clock"></i>
                                <?= formatDate($post['created_at']) ?>
                            </div>
                        </div>
                        
                        <div class="post-content">
                            <?= nl2br(htmlspecialchars($post['content'])) ?>
                        </div>
                        
                        <div class="post-actions">
                            <button class="btn btn-reply" onclick="toggleReplyForm(<?= $post['id'] ?>)">
                                <i class="fas fa-reply"></i> Rispondi
                            </button>
                            <span class="replies-count">
                                <i class="fas fa-comments"></i>
                                <?= count($post['replies']) ?> risposte
                            </span>
                        </div>
                        
                        <!-- Form risposta -->
                        <div class="reply-form" id="reply-form-<?= $post['id'] ?>" style="display: none;">
                            <h4><i class="fas fa-reply"></i> Rispondi a questo post:</h4>
                            <form method="POST" class="reply-form-content">
                                <input type="hidden" name="action" value="reply">
                                <input type="hidden" name="parent_id" value="<?= $post['id'] ?>">
                                <div class="form-group">
                                    <input type="text" name="author" required maxlength="50" placeholder="Il tuo nome">
                                </div>
                                <div class="form-group">
                                    <textarea name="content" required maxlength="500" placeholder="Scrivi la tua risposta..."></textarea>
                                </div>
                                <div class="reply-actions">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane"></i> Invia Risposta
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleReplyForm(<?= $post['id'] ?>)">
                                        <i class="fas fa-times"></i> Annulla
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Risposte -->
                        <?php if (!empty($post['replies'])): ?>
                            <div class="replies-container">
                                <h4><i class="fas fa-comments"></i> Risposte:</h4>
                                <?php foreach ($post['replies'] as $reply): ?>
                                    <div class="reply">
                                        <div class="reply-header">
                                            <div class="reply-author">
                                                <i class="fas fa-user"></i>
                                                <strong><?= htmlspecialchars($reply['author']) ?></strong>
                                            </div>
                                            <div class="reply-date">
                                                <i class="fas fa-clock"></i>
                                                <?= formatDate($reply['created_at']) ?>
                                            </div>
                                        </div>
                                        <div class="reply-content">
                                            <?= nl2br(htmlspecialchars($reply['content'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <footer class="forum-footer">
            <p>&copy; 2024 Mini Forum Professionale - Sviluppato con PHP e MySQL</p>
        </footer>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>