<?php

// Recupera l'azione dal form, se presente
$action = $_POST['action'] ?? null;

// Parametri di connessione al database
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

// Recupero del ruolo selezionato dal form ("autista" o altro)
$ruolo = $_POST['Ruolo'];

if($ruolo === 'autista'){

// Recupero dati dal form
$Nome = $_POST['Nome_registrazione'];
$Cognome = $_POST['Cognome_registrazione'];
$Sesso = $_POST['Sesso_registrazione'];
$Password = $_POST['Password_Registrazione'];
$Telefono = $_POST['telefono_Registrazione'];
$Email = $_POST['Email_registrazione'];
$Dati = $_POST['dati_Registrazione'];
$Patente = $_POST['patente_registrazione'];
$Scadenza = $_POST['scadenza_registrazione'];

// Verifica che i dati siano stati ricevuti
if (!$Nome || !$Cognome || !$Sesso || !$Password || !$Telefono || !$Email || !$Dati || !$Patente || !$Scadenza) {
    die("Errore: tutti i campi sono obbligatori.");
}

// Crittografia sicura della password con algoritmo BCRYPT
$hashedPassword = password_hash($Password, PASSWORD_BCRYPT);

// Query di inserimento nel database per autista
$sql = "INSERT INTO autista (nome, cognome, sesso, recapito_telefono, email, foto, dati_auto, numero_patente, scadenza_patente, password) VALUES ('$Nome', '$Cognome', '$Sesso', '$Telefono', '$Email', foto ,'$Dati', '$Patente', '$Scadenza', '$hashedPassword')";

// Esecuzione della query e gestione del risultato
 if ($connessione->query($sql)) {
        // Reindirizzamento alla pagina di accesso in caso di successo
header('Location: accesso.html');
exit();
} else {
    // Mostra errore in caso di problemi con l'inserimento
    echo "<p>Errore: " . $connessione->error . "</p>";
}
} 
else
{
    
// Recupero dati dal form di registrazione per passeggero
$Nome = $_POST['Nome_registrazione2'];
$Cognome = $_POST['Cognome_registrazione2'];
$Email = $_POST['Email_registrazione2'];
$Password = $_POST['Password_Registrazione2'];
$Telefono = $_POST['telefono_registrazione2'];
$Documento = $_POST['documento_Registrazione2'];

// Verifica che tutti i campi siano stati compilati
if (!$Nome || !$Cognome || !$Email || !$Password || !$Telefono || !$Documento) {
    die("Errore: tutti i campi sono obbligatori.");
}

// Crittografia sicura della password
$hashedPassword = password_hash($Password, PASSWORD_BCRYPT);

// Query di inserimento nel database per passeggero
$sql = "INSERT INTO passeggero (nome, cognome, telefono, email, documento, password) VALUES ('$Nome', '$Cognome', '$Telefono', '$Email', '$Documento', '$hashedPassword')";
 // Esecuzione della query e gestione del risultato
 if ($connessione->query($sql)) {
    // Reindirizzamento alla pagina di accesso in caso di successo
header('Location: accesso.html');
exit();
} else {
    // Mostra errore in caso di problemi con l'inserimento
    echo "<p>Errore: " . $connessione->error . "</p>";
}
}

?>
