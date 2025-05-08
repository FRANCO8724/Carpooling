<?php

// Avvia una nuova sessione o riprende una sessione esistente
session_start();

// Recupera l'azione inviata tramite POST, se esiste; altrimenti assegna null
$action = $_POST['action'] ?? null;

// Parametri di connessione al database
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "carpooling";

// Crea una nuova connessione al database MySQL
$connessione = new mysqli($host, $user, $password, $database);

// Controlla se ci sono stati errori nella connessione
if ($connessione === false) {
    // Termina lo script in caso di errore e stampa il messaggio
    die("Errore di connessione: " . $connessione->connect_error);
}

// Controlla se è stato inviato l'id del viaggio tramite POST
if (isset($_POST['id_viaggio'])) {
    $id_viaggio = $_POST['id_viaggio'];

    // Verifica che l'ID sia numerico (per sicurezza)
    if (is_numeric($id_viaggio)) {
        // Prepara la query SQL per aggiornare lo stato del viaggio in "chiuse"
        $sql = "UPDATE viaggio SET stato = 'chiuse' WHERE id_viaggio = ?";
        
        // Prepara l'istruzione SQL per l'esecuzione sicura (prepared statement)
        if ($stmt = $connessione->prepare($sql)) {
            // Collega il parametro (id_viaggio) alla query, come intero
            $stmt->bind_param("i", $id_viaggio);
            
            // Esegue la query
            if ($stmt->execute()) {
                // Informa l'utente che l'aggiornamento è andato a buon fine
                echo "Lo stato del viaggio è stato aggiornato con successo a 'chiuse'.";
            } else {
                // Errore nell'esecuzione della query
                echo "Errore nell'aggiornamento dello stato: " . $connessione->error;
            }
            
            // Chiude la dichiarazione per liberare risorse
            $stmt->close();
        } else {
            // Errore nella preparazione della query
            echo "Errore nella preparazione della query: " . $connessione->error;
        }
    } else {
        // L'ID inviato non è valido
        echo "ID del viaggio non valido.";
    }
} else {
    // Nessun ID è stato fornito tramite POST
    echo "ID del viaggio non fornito.";
}

// Chiude la connessione al database
$connessione->close();
?>
