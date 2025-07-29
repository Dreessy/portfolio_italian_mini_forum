/**
 * Mini Forum Professionale - JavaScript
 * Gestione interattività e validazione form
 */

// Inizializzazione quando il DOM è caricato
document.addEventListener('DOMContentLoaded', function() {
    initializeForum();
});

/**
 * Inizializza tutte le funzionalità del forum
 */
function initializeForum() {
    initializeFormValidation();
    initializeCharacterCounters();
    initializeReplyToggle();
    initializeScrollToTop();
    initializeFormSubmission();
    initializeAutoRefresh();
}

/**
 * Inizializza la validazione dei form
 */
function initializeFormValidation() {
    // Validazione in tempo reale rimossa per evitare conflitti con AJAX submit
    const forms = document.querySelectorAll('.post-form, .reply-form-content');
    
    forms.forEach(form => {
        
        // Validazione in tempo reale
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    });
}

/**
 * Valida un singolo campo
 */
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';
    
    // Rimuovi errori precedenti
    clearFieldError(field);
    
    // Validazione per nome autore
    if (fieldName === 'author') {
        if (value.length === 0) {
            errorMessage = 'Il nome è obbligatorio';
            isValid = false;
        } else if (value.length < 2) {
            errorMessage = 'Il nome deve essere di almeno 2 caratteri';
            isValid = false;
        } else if (value.length > 50) {
            errorMessage = 'Il nome non può superare i 50 caratteri';
            isValid = false;
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(value)) {
            errorMessage = 'Il nome può contenere solo lettere e spazi';
            isValid = false;
        }
    }
    
    // Validazione per contenuto
    if (fieldName === 'content') {
        if (value.length === 0) {
            errorMessage = 'Il contenuto è obbligatorio';
            isValid = false;
        } else if (value.length < 10) {
            errorMessage = 'Il contenuto deve essere di almeno 10 caratteri';
            isValid = false;
        } else if (value.length > 1000) {
            errorMessage = 'Il contenuto non può superare i 1000 caratteri';
            isValid = false;
        }
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

/**
 * Valida l'intero form
 */
function validateForm(form) {
    const fields = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;
    
    fields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Mostra errore per un campo
 */
function showFieldError(field, message) {
    field.classList.add('error');
    
    // Rimuovi messaggio di errore esistente
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Aggiungi nuovo messaggio di errore
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

/**
 * Rimuove errore da un campo
 */
function clearFieldError(field) {
    field.classList.remove('error');
    const errorMessage = field.parentNode.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

/**
 * Inizializza i contatori di caratteri
 */
function initializeCharacterCounters() {
    const textareas = document.querySelectorAll('textarea');
    
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength') || 1000;
        
        // Crea contatore se non esiste
        let counter = textarea.parentNode.querySelector('.char-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'char-counter';
            textarea.parentNode.appendChild(counter);
        }
        
        // Funzione per aggiornare il contatore
        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${textarea.value.length}/${maxLength} caratteri`;
            
            if (remaining < 50) {
                counter.style.color = '#e74c3c';
            } else if (remaining < 100) {
                counter.style.color = '#f39c12';
            } else {
                counter.style.color = '#7f8c8d';
            }
        }
        
        // Aggiorna contatore all'input
        textarea.addEventListener('input', updateCounter);
        
        // Inizializza contatore
        updateCounter();
    });
}

/**
 * Inizializza il toggle per le risposte
 */
function initializeReplyToggle() {
    const replyButtons = document.querySelectorAll('.btn-reply');
    
    replyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const postId = this.getAttribute('data-post-id');
            const replyForm = document.getElementById(`reply-form-${postId}`);
            
            if (replyForm) {
                // Toggle visibilità form
                if (replyForm.style.display === 'none' || !replyForm.style.display) {
                    replyForm.style.display = 'block';
                    this.textContent = 'Annulla Risposta';
                    this.classList.add('btn-secondary');
                    this.classList.remove('btn-reply');
                    
                    // Focus sul textarea
                    const textarea = replyForm.querySelector('textarea');
                    if (textarea) {
                        setTimeout(() => textarea.focus(), 100);
                    }
                } else {
                    replyForm.style.display = 'none';
                    this.textContent = 'Rispondi';
                    this.classList.add('btn-reply');
                    this.classList.remove('btn-secondary');
                }
            }
        });
    });
}

/**
 * Inizializza il pulsante scroll to top
 */
function initializeScrollToTop() {
    // Crea pulsante se non esiste
    let scrollButton = document.querySelector('.scroll-top');
    if (!scrollButton) {
        scrollButton = document.createElement('button');
        scrollButton.className = 'scroll-top';
        scrollButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
        scrollButton.setAttribute('aria-label', 'Torna in cima');
        document.body.appendChild(scrollButton);
    }
    
    // Mostra/nascondi pulsante in base allo scroll
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollButton.classList.add('visible');
        } else {
            scrollButton.classList.remove('visible');
        }
    });
    
    // Scroll to top al click
    scrollButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * Inizializza la gestione dell'invio form con AJAX
 */
function initializeFormSubmission() {
    const forms = document.querySelectorAll('.post-form, .reply-form-content');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm(this)) {
                return false;
            }
            
            submitFormAjax(this);
            
            // Assicurati che il form non venga inviato
            return false;
        });
    });
}

/**
 * Invia form tramite AJAX
 */
function submitFormAjax(form) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // Mostra stato di caricamento
    submitButton.textContent = 'Invio in corso...';
    submitButton.disabled = true;
    form.classList.add('loading');
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => {
            if (response.ok) {
                // Redirect per evitare il doppio invio
                window.location.href = window.location.pathname;
            } else {
                throw new Error('Errore nell\'invio del form');
            }
        })
    .catch(error => {
        console.error('Errore:', error);
        showMessage('Errore durante l\'invio. Riprova.', 'error');
    })
    .finally(() => {
        // Ripristina stato del pulsante
        submitButton.textContent = originalText;
        submitButton.disabled = false;
        form.classList.remove('loading');
    });
}

/**
 * Mostra messaggio di notifica
 */
function showMessage(text, type = 'info') {
    // Rimuovi messaggi esistenti
    const existingMessages = document.querySelectorAll('.message');
    existingMessages.forEach(msg => msg.remove());
    
    const message = document.createElement('div');
    message.className = `message ${type}`;
    
    const icon = type === 'success' ? 'fas fa-check-circle' : 
                type === 'error' ? 'fas fa-exclamation-circle' : 
                'fas fa-info-circle';
    
    message.innerHTML = `<i class="${icon}"></i> ${text}`;
    
    // Inserisci dopo l'header
    const header = document.querySelector('.forum-header');
    if (header && header.nextSibling) {
        header.parentNode.insertBefore(message, header.nextSibling);
    } else {
        document.querySelector('.container').prepend(message);
    }
    
    // Rimuovi automaticamente dopo 5 secondi
    setTimeout(() => {
        if (message.parentNode) {
            message.remove();
        }
    }, 5000);
}

/**
 * Inizializza l'aggiornamento automatico dei post
 */
function initializeAutoRefresh() {
    // Aggiorna ogni 30 secondi
    setInterval(() => {
        refreshPosts();
    }, 30000);
}

/**
 * Aggiorna i post senza ricaricare la pagina
 */
function refreshPosts() {
    fetch(window.location.href + '?ajax=1')
    .then(response => response.json())
    .then(data => {
        if (data.posts) {
            updatePostsContainer(data.posts);
        }
    })
    .catch(error => {
        console.log('Aggiornamento automatico fallito:', error);
    });
}

/**
 * Aggiorna il contenitore dei post
 */
function updatePostsContainer(newPosts) {
    const postsContainer = document.querySelector('.posts-container');
    if (!postsContainer) return;
    
    const currentPosts = postsContainer.querySelectorAll('.post');
    const currentPostIds = Array.from(currentPosts).map(post => 
        post.getAttribute('data-post-id')
    );
    
    // Controlla se ci sono nuovi post
    const hasNewPosts = newPosts.some(post => 
        !currentPostIds.includes(post.id.toString())
    );
    
    if (hasNewPosts) {
        // Mostra notifica di nuovi post
        showMessage('Nuovi post disponibili!', 'info');
    }
}

/**
 * Utility: Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Utility: Sanitizza input HTML
 */
function sanitizeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

/**
 * Gestione errori globali
 */
window.addEventListener('error', function(e) {
    console.error('Errore JavaScript:', e.error);
});

/**
 * Gestione promesse rifiutate
 */
window.addEventListener('unhandledrejection', function(e) {
    console.error('Promise rifiutata:', e.reason);
});

/**
 * Toggle del form di risposta
 */
function toggleReplyForm(postId) {
    const replyForm = document.getElementById(`reply-form-${postId}`);
    if (replyForm) {
        if (replyForm.style.display === 'none' || replyForm.style.display === '') {
            replyForm.style.display = 'block';
            // Focus sul primo campo del form
            const firstInput = replyForm.querySelector('input[name="author"]');
            if (firstInput) {
                firstInput.focus();
            }
        } else {
            replyForm.style.display = 'none';
        }
    }
}

// Esporta funzioni per uso globale se necessario
window.toggleReplyForm = toggleReplyForm;
window.ForumJS = {
    showMessage,
    validateForm,
    refreshPosts
};