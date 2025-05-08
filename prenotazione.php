<?php

// Avvia la sessione (utile per gestire dati persistenti tra le pagine)
session_start();

// Recupera l'azione dal form, se presente
$action = $_POST['action'] ?? null;

// Parametri di connessione al database
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "carpooling";

// Connessione al database MySQL
$connessione = new mysqli($host, $user, $password, $database);

// Verifica della connessione
if ($connessione === false) {
    die("Errore di connessione: " . $connessione->connect_error);
}

// Verifica che i dati necessari siano stati inviati tramite POST
if (isset($_POST['id_viaggio']) && isset($_POST['email'])) {
    $id_viaggio = $_POST['id_viaggio'];
    $Email = $_POST['email'];

    // Recupera l'ID del passeggero associato all'email
    $query = "SELECT id_passeggero FROM passeggero WHERE email = '$Email'";
    $result = $connessione->query($query);

    if ($result->num_rows > 0) {
        // Email trovata, otteniamo l'id_passeggero
        $row = $result->fetch_assoc();
        $id_utente = $row['id_passeggero'];

        // Inserisce la partecipazione al viaggio nella tabella "partecipa" con stato "accettata"
        $queryInsert = "INSERT INTO partecipa (id_viaggio, id_passeggero, stato) 
                        VALUES ('$id_viaggio', '$id_utente', 'accettata')";

        if ($connessione->query($queryInsert) === TRUE) {
            echo "Partecipazione registrata con successo!";
        } else {
            echo "Errore durante la registrazione della partecipazione: " . $connessione->error;
        }
    } else {
        // Nessun utente trovato con quell'email
        echo "Utente non trovato.";
    }
} else {
    // Dati richiesti non forniti
    echo "Dati mancanti.";
}

// Chiusura della connessione al database
$connessione->close();
?>
