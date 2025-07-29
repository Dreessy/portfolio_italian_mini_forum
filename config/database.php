<?php
/**
 * Configurazione Database - Mini Forum Professionale
 * Sistema automatico di creazione database e tabelle
 */

// Configurazione connessione MySQL
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sito_3');
define('DB_CHARSET', 'utf8mb4');

/**
 * Connessione al database con creazione automatica
 */
function getConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            error_log("Tentativo di connessione al database...");
            
            // Prima connessione senza specificare il database per crearlo se non esiste
            $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $tempPdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            error_log("Connessione al server MySQL riuscita");
            
            // Crea il database se non esiste
            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            error_log("Database " . DB_NAME . " creato o già esistente");
            
            // Connessione al database specifico
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            error_log("Connessione al database " . DB_NAME . " riuscita");
            
        } catch (PDOException $e) {
            error_log("Errore connessione database: " . $e->getMessage());
            die("Errore di connessione al database: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Inizializza il database creando le tabelle necessarie
 */
function initializeDatabase() {
    $pdo = getConnection();
    
    try {
        // Tabella posts (post principali)
        $createPostsTable = "
            CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                author VARCHAR(50) NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                is_active BOOLEAN DEFAULT TRUE,
                INDEX idx_created_at (created_at),
                INDEX idx_author (author)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        // Tabella replies (risposte ai post)
        $createRepliesTable = "
            CREATE TABLE IF NOT EXISTS replies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                author VARCHAR(50) NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                is_active BOOLEAN DEFAULT TRUE,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                INDEX idx_post_id (post_id),
                INDEX idx_created_at (created_at),
                INDEX idx_author (author)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        // Tabella statistics (statistiche del forum)
        $createStatsTable = "
            CREATE TABLE IF NOT EXISTS forum_stats (
                id INT AUTO_INCREMENT PRIMARY KEY,
                total_posts INT DEFAULT 0,
                total_replies INT DEFAULT 0,
                last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        // Esegui la creazione delle tabelle
        $pdo->exec($createPostsTable);
        $pdo->exec($createRepliesTable);
        $pdo->exec($createStatsTable);
        
        // Inizializza le statistiche se non esistono
        $checkStats = $pdo->query("SELECT COUNT(*) as count FROM forum_stats");
        if ($checkStats->fetch()['count'] == 0) {
            $pdo->exec("INSERT INTO forum_stats (total_posts, total_replies) VALUES (0, 0)");
        }
        
        // Inserisci dati di esempio se le tabelle sono vuote
        $checkPosts = $pdo->query("SELECT COUNT(*) as count FROM posts");
        if ($checkPosts->fetch()['count'] == 0) {
            insertSampleData($pdo);
        }
        
    } catch (PDOException $e) {
        die("Errore durante l'inizializzazione del database: " . $e->getMessage());
    }
}

/**
 * Inserisce dati di esempio per dimostrare il funzionamento
 */
function insertSampleData($pdo) {
    try {
        // Post di benvenuto
        $welcomePost = "
            INSERT INTO posts (author, content) VALUES 
            ('Amministratore', 'Benvenuti nel Mini Forum Professionale!\n\nQuesto è un sistema di forum moderno sviluppato in PHP con MySQL. Caratteristiche principali:\n\n• Interfaccia moderna e responsive\n• Sistema di post e risposte\n• Visualizzazione degli ultimi 10 post\n• Gestione automatica del database\n• Codice pulito e professionale\n\nSentitevi liberi di testare tutte le funzionalità!'),
            ('Sviluppatore', 'Caratteristiche tecniche del progetto:\n\n✅ PHP 8+ compatibile\n✅ MySQL con PDO\n✅ Design responsive\n✅ Sicurezza XSS protection\n✅ Validazione input\n✅ Gestione errori\n✅ Codice documentato\n\nPerfetto per dimostrazioni aziendali!'),
            ('Beta Tester', 'Ho testato il forum e funziona perfettamente! L\'interfaccia è molto intuitiva e il codice è ben strutturato. Ottimo lavoro!')
        ";
        
        $pdo->exec($welcomePost);
        
        // Aggiungi alcune risposte di esempio
        $sampleReplies = "
            INSERT INTO replies (post_id, author, content) VALUES 
            (1, 'Utente Demo', 'Grazie per il benvenuto! Il forum sembra molto professionale.'),
            (1, 'Visitatore', 'Interfaccia molto pulita e moderna. Complimenti!'),
            (2, 'Code Reviewer', 'Il codice è ben organizzato e segue le best practices. Eccellente!'),
            (3, 'Project Manager', 'Perfetto per presentazioni aziendali. Molto impressionante!')
        ";
        
        $pdo->exec($sampleReplies);
        
        // Aggiorna le statistiche
        $pdo->exec("UPDATE forum_stats SET total_posts = 3, total_replies = 4");
        
    } catch (PDOException $e) {
        // Non bloccare l'applicazione se i dati di esempio falliscono
        error_log("Errore inserimento dati di esempio: " . $e->getMessage());
    }
}

/**
 * Ottiene le statistiche del forum
 */
function getForumStats() {
    $pdo = getConnection();
    
    try {
        $stmt = $pdo->query("
            SELECT 
                (SELECT COUNT(*) FROM posts WHERE is_active = 1) as total_posts,
                (SELECT COUNT(*) FROM replies WHERE is_active = 1) as total_replies,
                (SELECT MAX(created_at) FROM posts) as last_post_date
        ");
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        return ['total_posts' => 0, 'total_replies' => 0, 'last_post_date' => null];
    }
}

/**
 * Verifica la connessione al database
 */
function testConnection() {
    try {
        $pdo = getConnection();
        $stmt = $pdo->query("SELECT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>