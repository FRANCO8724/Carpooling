<?php

// Avvia la sessione
session_start();

$action = $_POST['action'] ?? null;

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "carpooling";

// Connessione al database
$connessione = new mysqli($host, $user, $password, $database);

// Verifica connessione
if ($connessione === false) {
    die("Errore di connessione: " . $connessione->connect_error);
}

// Recupero dati dal form
$Password = $_POST['Password_utente'];
$Email = $_POST['Email_utente'];

// Controlla prima nella tabella passeggero
$stmt = $connessione->prepare("SELECT Password, id_passeggero FROM passeggero WHERE Email = ?");
$stmt->bind_param("s", $Email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Recupera la password criptata dal database
    $row = $result->fetch_assoc();
    $hashedPasswordFromDb = $row['Password'];

    // Usa password_verify per confrontare la password in chiaro con quella criptata
    if (password_verify($Password, $hashedPasswordFromDb)) {
        // Se la password è corretta, memorizza l'email dell'utente nella sessione
        $_SESSION['email'] = $Email;
        // Reindirizza alla pagina passeggero.php
        header("Location: passeggero.php");
        exit;
    } else {
        echo "Password errata.<br>"; // Debug
    }
}

// Chiudi lo statement della tabella passeggero
$stmt->close();

// Se l'email non è trovata nella tabella passeggero, controlla nella tabella autista
$stmt = $connessione->prepare("SELECT Password, id_autista FROM autista WHERE Email = ?");
$stmt->bind_param("s", $Email);
$stmt->execute();
$result = $stmt->get_result();

// Se l'email è trovata nella tabella autista
if ($result->num_rows > 0) {
    // Recupera la password criptata dal database
    $row = $result->fetch_assoc();
    $hashedPasswordFromDb = $row['Password'];

    // Usa password_verify per confrontare la password in chiaro con quella criptata
    if (password_verify($Password, $hashedPasswordFromDb)) {
        // Se la password è corretta, memorizza l'email dell'utente nella sessione
        $_SESSION['email'] = $Email;
        // Reindirizza alla pagina autista.php
        header("Location: autista.php");
        exit;
    } else {
        echo "Password errata.<br>"; // Debug
    }
}

// Chiudi lo statement della tabella autista
$stmt->close();

// Se l'email non è trovata in nessuna delle tabelle
echo "Email non trovata nel database.<br>";

// Chiudi la connessione
$connessione->close();

exit;

?>
