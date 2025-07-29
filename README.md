# 🗨️ Mini Forum

> Un sistema di forum moderno e responsive sviluppato in PHP con MySQL, progettato per dimostrare competenze di sviluppo web full-stack.

## 📋 Descrizione del Progetto

Mini Forum è un'applicazione web completa che permette agli utenti di creare post e rispondere alle discussioni. Il progetto è stato sviluppato utilizzando tecnologie web moderne e best practices di sviluppo, con particolare attenzione all'esperienza utente e alla sicurezza.

### ✨ Caratteristiche Principali

- **Sistema di Post e Risposte**: Creazione di nuovi post e sistema di risposte threaded
- **Design Responsive**: Interfaccia ottimizzata per desktop, tablet e mobile
- **Validazione Avanzata**: Validazione lato client e server per garantire l'integrità dei dati
- **Sicurezza**: Protezione contro SQL injection e XSS attacks
- **AJAX Integration**: Invio asincrono dei form per una migliore UX
- **Statistiche in Tempo Reale**: Contatori dinamici di post e risposte
- **Design Moderno**: UI pulita e intuitiva con animazioni CSS

## 🛠️ Tecnologie Utilizzate

### Backend
- **PHP 8.0+**: Linguaggio di programmazione principale
- **MySQL**: Database relazionale per la persistenza dei dati
- **PDO**: PHP Data Objects per l'accesso sicuro al database

### Frontend
- **HTML5**: Markup semantico e accessibile
- **CSS3**: Styling avanzato con Flexbox, Grid e animazioni
- **JavaScript (ES6+)**: Interattività e validazione lato client
- **Font Awesome**: Libreria di icone per l'interfaccia

### Architettura
- **MVC Pattern**: Separazione logica tra Model, View e Controller
- **Responsive Design**: Mobile-first approach
- **Progressive Enhancement**: Funzionalità base garantite anche senza JavaScript

## 📁 Struttura del Progetto

```
mini-forum/
├── assets/
│   ├── css/
│   │   └── style.css          # Stili principali dell'applicazione
│   └── js/
│       └── script.js          # JavaScript per interattività
├── config/
│   └── database.php           # Configurazione database
├── includes/
│   └── functions.php          # Funzioni PHP principali
├── index.php                  # Pagina principale dell'applicazione
├── index.html                 # DEMO VERSION (da rimuovere per uso locale)
└── README.md                  # Documentazione del progetto
```

## 🚀 Installazione e Configurazione

### Prerequisiti
- PHP 8.0 o superiore
- MySQL 5.7 o superiore
- Server web (Apache/Nginx) o PHP built-in server

### Installazione

1. **Clona il repository**
   ```bash
   git clone https://github.com/xDreessy/mini-forum.git
   cd mini-forum
   ```

2. **Configura il database**
   - Crea un database MySQL
   - Importa lo schema del database (le tabelle verranno create automaticamente al primo avvio)
   - Modifica il file `config/database.php` con le tue credenziali:
   ```php
   $host = 'localhost';
   $dbname = 'tuo_database';
   $username = 'tuo_username';
   $password = 'tua_password';
   ```

3. **Rimuovi il file demo** (IMPORTANTE per uso locale)
   ```bash
   rm index.html
   ```
   > ⚠️ **Attenzione**: Il file `index.html` è una versione demo statica per GitHub Pages. Deve essere rimosso per utilizzare la versione PHP funzionale.

4. **Avvia il server**
   ```bash
   # Usando il server built-in di PHP
   php -S localhost:8080
   
   # Oppure configura il tuo server web per puntare alla directory del progetto
   ```

5. **Accedi all'applicazione**
   - Apri il browser e vai su `http://localhost:8080`
   - L'applicazione creerà automaticamente le tabelle necessarie al primo accesso

## 💡 Funzionalità Dettagliate

### Sistema di Post
- Creazione di nuovi post con validazione
- Visualizzazione cronologica dei post
- Conteggio automatico delle risposte

### Sistema di Risposte
- Risposte threaded per ogni post
- Validazione dei dati in tempo reale
- Invio asincrono tramite AJAX

### Sicurezza
- Prepared statements per prevenire SQL injection
- Sanitizzazione dell'input utente
- Validazione lato client e server
- Escape dell'output per prevenire XSS

### User Experience
- Design responsive per tutti i dispositivi
- Feedback visivo per le azioni dell'utente
- Animazioni fluide e transizioni
- Accessibilità migliorata

## 🎯 Obiettivi del Progetto

Questo progetto è stato sviluppato per dimostrare:

- **Competenze Full-Stack**: Sviluppo completo di un'applicazione web
- **Best Practices**: Utilizzo di pattern e convenzioni standard
- **Sicurezza Web**: Implementazione di misure di sicurezza essenziali
- **Design Responsivo**: Creazione di interfacce adattive
- **Problem Solving**: Risoluzione di problemi tecnici complessi

## 🔧 Personalizzazione

Il progetto è facilmente estendibile e personalizzabile:

- **Temi**: Modifica `assets/css/style.css` per cambiare l'aspetto
- **Funzionalità**: Aggiungi nuove feature modificando `includes/functions.php`
- **Database**: Estendi lo schema per supportare nuove funzionalità
- **API**: Implementa endpoint REST per integrazioni esterne

## 📱 Demo Online

Una versione demo statica è disponibile su GitHub Pages per visualizzare l'interfaccia e il design del progetto. La demo utilizza dati statici e non include le funzionalità backend.

> **Nota**: La demo online (`index.html`) è puramente illustrativa e non rappresenta le funzionalità complete dell'applicazione PHP.

## 🤝 Contributi

Questo progetto è stato sviluppato come portfolio personale. Suggerimenti e feedback sono sempre benvenuti!

## 📞 Contatti

**Ciro Casoria**
- 🐙 **GitHub**: [@xDreessy](https://github.com/xDreessy)
- 💼 **LinkedIn**: [Ciro Casoria](https://www.linkedin.com/in/ciro-casoria-01b93b201/)
- 📧 **Email**: [ciro062012@icloud.com](mailto:ciro062012@icloud.com)

---

## 📄 Licenza

Questo progetto è stato creato per **opportunità lavorative** e come dimostrazione delle competenze di sviluppo web. È disponibile per la valutazione da parte di potenziali datori di lavoro e collaboratori.

---

*Sviluppato con ❤️ per dimostrare passione e competenza nello sviluppo web*