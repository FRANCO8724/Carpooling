<?php
session_start();

// Recupera l'azione dal form, se presente
$action = $_POST['action'] ?? null;

// Parametri di connessione al database
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "carpooling";

// Creazione della connessione
$connessione = new mysqli($host, $user, $password, $database);

// Verifica connessione
if ($connessione->connect_error) {
    die("Errore di connessione: " . $connessione->connect_error);
}

// Verifica se i parametri necessari sono stati inviati via POST
if (isset($_POST['id_viaggio']) && isset($_POST['id_passeggero'])) {
    // Recupera i valori dai POST
    $id_viaggio = $_POST['id_viaggio'];
    $id_passeggero = $_POST['id_passeggero'];
    
    // Definisci il valore per "stato"
    $stato = 'respinta';  // Impostato direttamente o può essere preso da $_POST

    // Prepara la query
    $stmt = $connessione->prepare("UPDATE `partecipa` SET `stato` = ? WHERE `id_viaggio` = ? AND `id_passeggero` = ?");
    
    if ($stmt === false) {
        die("Errore nella preparazione della query: " . $connessione->error);
    }

    // Associa i parametri
    $stmt->bind_param("sii", $stato, $id_viaggio, $id_passeggero); // "s" per stringa, "ii" per interi

    // Esegui la query
    $stmt->execute();

    // Controlla se la query è andata a buon fine
    if ($stmt->affected_rows > 0) {
        echo "Record aggiornato con successo.";
    } else {
        echo "Nessun record aggiornato.";
    }

    // Chiudi la dichiarazione
    $stmt->close();
} else {
    echo "Dati mancanti o errati!";
}

// Chiudi la connessione
$connessione->close();
?>
