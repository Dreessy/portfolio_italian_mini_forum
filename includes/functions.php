<?php
/**
 * Funzioni principali - Mini Forum Professionale
 * Gestione post, risposte e utilità varie
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Aggiunge un nuovo post al forum
 */
function addPost($author, $content) {
    $pdo = getConnection();
    
    try {
        // Sanitizza e valida i dati
        $author = trim(strip_tags($author));
        $content = trim(strip_tags($content, '<br><p><strong><em>'));
        
        if (empty($author) || empty($content)) {
            throw new InvalidArgumentException("Autore e contenuto sono obbligatori");
        }
        
        if (strlen($author) > 50) {
            throw new InvalidArgumentException("Il nome autore non può superare i 50 caratteri");
        }
        
        if (strlen($content) > 1000) {
            throw new InvalidArgumentException("Il contenuto non può superare i 1000 caratteri");
        }
        
        // Inserisci il post
        $stmt = $pdo->prepare("
            INSERT INTO posts (author, content, created_at) 
            VALUES (:author, :content, NOW())
        ");
        
        $result = $stmt->execute([
            ':author' => $author,
            ':content' => $content
        ]);
        
        if ($result) {
            // Aggiorna le statistiche
            updateForumStats('post_added');
            return $pdo->lastInsertId();
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Errore aggiunta post: " . $e->getMessage());
        return false;
    } catch (InvalidArgumentException $e) {
        error_log("Errore validazione post: " . $e->getMessage());
        return false;
    }
}

/**
 * Aggiunge una risposta a un post
 */
function addReply($author, $content, $postId) {
    $pdo = getConnection();
    
    try {
        // Sanitizza e valida i dati
        $author = trim(strip_tags($author));
        $content = trim(strip_tags($content, '<br><p><strong><em>'));
        $postId = (int)$postId;
        
        if (empty($author) || empty($content) || $postId <= 0) {
            throw new InvalidArgumentException("Tutti i campi sono obbligatori");
        }
        
        if (strlen($author) > 50) {
            throw new InvalidArgumentException("Il nome autore non può superare i 50 caratteri");
        }
        
        if (strlen($content) > 500) {
            throw new InvalidArgumentException("Il contenuto della risposta non può superare i 500 caratteri");
        }
        
        // Verifica che il post esista
        $checkPost = $pdo->prepare("SELECT id FROM posts WHERE id = :post_id AND is_active = 1");
        $checkPost->execute([':post_id' => $postId]);
        
        $postExists = $checkPost->fetch();
        
        if (!$postExists) {
            throw new InvalidArgumentException("Post non trovato");
        }
        
        // Inserisci la risposta
        $stmt = $pdo->prepare("
            INSERT INTO replies (post_id, author, content, created_at) 
            VALUES (:post_id, :author, :content, NOW())
        ");
        
        $result = $stmt->execute([
            ':post_id' => $postId,
            ':author' => $author,
            ':content' => $content
        ]);
        
        if ($result) {
            // Aggiorna le statistiche
            updateForumStats('reply_added');
            $insertId = $pdo->lastInsertId();
            return $insertId;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Errore aggiunta risposta: " . $e->getMessage());
        return false;
    } catch (InvalidArgumentException $e) {
        error_log("Errore validazione risposta: " . $e->getMessage());
        return false;
    }
}

/**
 * Recupera gli ultimi post con le relative risposte
 */
function getLatestPosts($limit = 10) {
    $pdo = getConnection();
    
    try {
        $limit = max(1, min(50, (int)$limit)); // Limita tra 1 e 50
        
        // Query per recuperare i post
        $stmt = $pdo->prepare("
            SELECT id, author, content, created_at 
            FROM posts 
            WHERE is_active = 1 
            ORDER BY created_at DESC 
            LIMIT :limit
        ");
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $posts = $stmt->fetchAll();
        
        // Per ogni post, recupera le risposte
        foreach ($posts as &$post) {
            $post['replies'] = getRepliesForPost($post['id']);
        }
        
        return $posts;
        
    } catch (PDOException $e) {
        error_log("Errore recupero post: " . $e->getMessage());
        return [];
    }
}

/**
 * Recupera le risposte per un post specifico
 */
function getRepliesForPost($postId) {
    $pdo = getConnection();
    
    try {
        $stmt = $pdo->prepare("
            SELECT id, author, content, created_at 
            FROM replies 
            WHERE post_id = :post_id AND is_active = 1 
            ORDER BY created_at ASC
        ");
        
        $stmt->execute([':post_id' => $postId]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Errore recupero risposte: " . $e->getMessage());
        return [];
    }
}

/**
 * Formatta una data in modo user-friendly
 */
function formatDate($datetime) {
    if (empty($datetime)) {
        return 'Data non disponibile';
    }
    
    try {
        $date = new DateTime($datetime);
        $now = new DateTime();
        $diff = $now->diff($date);
        
        // Se è oggi
        if ($diff->days == 0) {
            if ($diff->h == 0) {
                if ($diff->i == 0) {
                    return 'Proprio ora';
                } else {
                    return $diff->i . ' minuto' . ($diff->i > 1 ? 'i' : '') . ' fa';
                }
            } else {
                return $diff->h . ' ora' . ($diff->h > 1 ? 'e' : '') . ' fa';
            }
        }
        // Se è ieri
        elseif ($diff->days == 1) {
            return 'Ieri alle ' . $date->format('H:i');
        }
        // Se è questa settimana
        elseif ($diff->days < 7) {
            $giorni = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];
            return $giorni[$date->format('w')] . ' alle ' . $date->format('H:i');
        }
        // Altrimenti data completa
        else {
            return $date->format('d/m/Y H:i');
        }
        
    } catch (Exception $e) {
        return date('d/m/Y H:i', strtotime($datetime));
    }
}

/**
 * Aggiorna le statistiche del forum
 */
function updateForumStats($action) {
    $pdo = getConnection();
    
    try {
        switch ($action) {
            case 'post_added':
                $pdo->exec("UPDATE forum_stats SET total_posts = total_posts + 1, last_activity = NOW()");
                break;
                
            case 'reply_added':
                $pdo->exec("UPDATE forum_stats SET total_replies = total_replies + 1, last_activity = NOW()");
                break;
                
            case 'recalculate':
                $stmt = $pdo->query("
                    SELECT 
                        (SELECT COUNT(*) FROM posts WHERE is_active = 1) as posts,
                        (SELECT COUNT(*) FROM replies WHERE is_active = 1) as replies
                ");
                $stats = $stmt->fetch();
                
                $pdo->prepare("
                    UPDATE forum_stats 
                    SET total_posts = :posts, total_replies = :replies, last_activity = NOW()
                ")->execute([
                    ':posts' => $stats['posts'],
                    ':replies' => $stats['replies']
                ]);
                break;
        }
    } catch (PDOException $e) {
        error_log("Errore aggiornamento statistiche: " . $e->getMessage());
    }
}

/**
 * Pulisce e valida l'input utente
 */
function sanitizeInput($input, $maxLength = null) {
    $input = trim(strip_tags($input));
    
    if ($maxLength && strlen($input) > $maxLength) {
        $input = substr($input, 0, $maxLength);
    }
    
    return $input;
}

/**
 * Verifica se una stringa contiene contenuto spam o inappropriato
 */
function isSpam($content) {
    $spamWords = ['spam', 'viagra', 'casino', 'lottery', 'winner'];
    $content = strtolower($content);
    
    foreach ($spamWords as $word) {
        if (strpos($content, $word) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Genera un token CSRF per la sicurezza dei form
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica un token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Ottiene informazioni sul sistema per debug
 */
function getSystemInfo() {
    return [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'database_connection' => testConnection() ? 'OK' : 'ERROR',
        'forum_stats' => getForumStats()
    ];
}
?>