<?php

// Recupera il valore 'action' dal form, se presente, altrimenti imposta null
$action = $_POST['action'] ?? null;

// Credenziali per la connessione al database
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "carpooling";

// Connessione al database MySQL
$connessione = new mysqli($host, $user, $password, $database);

// Controlla se la connessione ha avuto successo
if ($connessione === false) {
    die("Errore di connessione: " . $connessione->connect_error);
}

// Avvia la sessione PHP per recuperare i dati utente
session_start();
$Email = $_SESSION['email'];  //Recupero l'email dalla pagina di login 


?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestione Dati</title>
    <link rel="stylesheet" href="css/stylesacc.css">
<script>
    // Mostra il form selezionato nascondendo gli altri
        function mostraForm(tipo) {
            const forms = document.querySelectorAll('.form-container');
            forms.forEach(form => form.style.display = 'none');
            document.getElementById(tipo).style.display = 'block';
        }
        // Funzione per accettare un passeggero 
        function selezionaPasseggero(idViaggio, idPasseggero) {
        // Creiamo una richiesta AJAX
        var xhr = new XMLHttpRequest();
        //richiama la pagina accettazione.php
        xhr.open("POST", "accettazione.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Inviamo l'ID del viaggio e l'email dell'utente
        xhr.send("id_viaggio=" + idViaggio + "&id_passeggero=" + idPasseggero);

        // Gestiamo la risposta del server
        xhr.onload = function() {
            if (xhr.status == 200) {
                alert("Prenotazione accettata!");
            } else {
                alert("Errore durante l' accettazione.");
            }
        };
    }
    // Funzione per chiudere le prenotazioni di un viaggio 
    function chiudiViaggio(idViaggio) {
        // Creiamo una richiesta AJAX
        var xhr = new XMLHttpRequest();
        //richiama la pagina chiudi.php
        xhr.open("POST", "chiudi.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Inviamo l'ID del viaggio e l'email dell'utente
        var email = '<?php echo $_SESSION['email']; ?>';  // Recupera l'email dell'utente dalla sessione
        xhr.send("id_viaggio=" + idViaggio + "&email=" + email);

        // Gestiamo la risposta del server
        xhr.onload = function() {
            if (xhr.status == 200) {
                alert("Viaggio chiuso con successo!");
            } else {
                alert("Errore durante la prenotazione.");
            }
        };
    }
</script>

</head>
<body>
    <!-- Creazione dei 4 form -->
    <h2>Seleziona l'azione da eseguire</h2>
    <button onclick="mostraForm('Add_viaggio')">Aggiungi viaggio</button>
    <button onclick="mostraForm('Add_accettazione')">Accettare prenotazioni</button>
    <button onclick="mostraForm('Add_prenotazione')">Chiudi prenotazioni</button>
    <button onclick="mostraForm('Add_recensione')">Aggiungi recensione</button>
    <button onclick="mostraForm('Add_visualizza')">Visualizza recensione</button>
    
    <!-- Aggiungi un viaggio -->
    <div id="Add_viaggio" class="form-container" style="display: none;">
        <h2>Inserisci le informazioni relative al viaggio: </h2>
        <form method="POST">
            Città di partenza: <input type="text" name="partenza" required>
            <br><br>
            Città di arrivo: <input type="text" name="arrivo" required>
            <br><br>

            <?php
            // Calcola la data di domani
            $domani = date('Y-m-d', strtotime('+1 day'));
            ?>
            Data di partenza: <input type="date" name="data" value="<?php echo $domani; ?>" min="<?php echo $domani; ?>" required>
            <br><br>

            Durata: <input type="number" name="durata" required min="1" max="23" placeholder="Inserisci un durata tra 1 e 23" class="input-lungo">
            <br><br>
            Orario di partenza: <input type="time" name="orario" required>
            <br><br>
            Numero massimo di passeggeri: <input type="number" name="n_passeggeri" min="1" required>
            <br><br>
            Soste: <input type="number" name="sosta" required min="1">
            <br><br>
            Prezzo: <input type="number" name="costo" required min="1">
            <br><br>
            <input type="hidden" name="action" value="Viaggio">
        <input type="submit" value="Aggiungi"> 
        </form>
        
        <?php
        //Memoprizzazione di tutti i dati inseriti dall'utente nel php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'Viaggio') {
        // Raccogli i dati inviati dal form
        $Partenza = $_POST['partenza'];
        $Arrivo = $_POST['arrivo'];
        $Data = $_POST['data'];
        $Durata = $_POST['durata'];
        $Orario = $_POST['orario'];
        $Passeggeri = $_POST['n_passeggeri'];
        $Soste = $_POST['sosta'];
        $Prezzo = $_POST['costo'];

        // Esegui la query per trovare l'id dell'autista
        $query = "SELECT id_autista FROM autista WHERE email = '$Email' LIMIT 1";
        // Esegui la query
        $result = $connessione->query($query);

        if ($result && $result->num_rows > 0) {
            // Se l'autista esiste, prendi il suo id
            $row = $result->fetch_assoc();
            $id_autista = $row['id_autista'];
        } else {
            echo "<p>Errore: Nome dell'autista non trovato</p>";
        }

        $dataCorrente = new DateTime();
        
        //Controlli sulle variabili per evitare inserimento di valori non ammissibili
        if (!$Data) {
            $errori[] = "La data inserita non è valida.";
        } elseif ($Data <= $dataCorrente) {
            $errori[] = "La data deve essere successiva alla data odierna.";
        }

        // Controlla che "Passeggeri" sia un numero positivo
        if (!is_numeric($Passeggeri) || $Passeggeri <= 0) {
            $errori[] = "Il numero di passeggeri deve essere un valore numerico positivo.";
        }        

        // Controlla che "Durata" sia un numero positivo
        if (!is_numeric($Durata) || $Durata <= 0) {
            $errori[] = "La durata deve essere un valore numerico positivo.";
        }        

        // Controlla che "Soste" sia un numero positivo
        if (!is_numeric($Soste) || $Soste <= 0) {
            $errori[] = "La sosta deve essere un valore numerico positivo.";
        }  

        // Controlla che "Prezzo" sia un numero positivo
        if (!is_numeric($Prezzo) || $Prezzo <= 0) {
            $errori[] = "Il prezzo deve essere un valore numerico positivo.";
        }                

            // Crea una query per inserire i dati nella tabella viaggio
            $sql = "INSERT INTO viaggio (citta_partenza, citta_arrivo, data, tempo, orario, numero_passeggeri, soste, costo, id_autista, stato) VALUES ('$Partenza', '$Arrivo', '$Data', '$Durata','$Orario', '$Passeggeri', '$Soste', '$Prezzo', '$id_autista', 'aperte')";
            
            // Esegui la query
            if ($connessione->query($sql)) {
                echo "<p>Dati inseriti con successo!</p>";
            } else {
                echo "<p>Errore: " . $connessione->error . "</p>";
            }
        }

        ?>
    </div>

    <!-- Accetta un viaggio -->
    <div id="Add_accettazione" class="form-container" style="display: none;">
        <h2>Inserisci le informazioni relative al viaggio: </h2>
        <form method="POST">
            <input type="hidden" name="action" value="Accetta">
            <input type="submit" value="Visualizza"> 
        </form>

       <?php
        //Memoprizzazione di tutti i dati inseriti dall'utente nel php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'Accetta') {
          
        $query = "SELECT id_autista FROM autista WHERE email = '$Email'"; 
        $result = $connessione->query($query);
        
        if ($result && $result->num_rows > 0) {
            // Ottieni l'ID dell'autista dalla query
            $row = $result->fetch_assoc();
            $idAutista = $row['id_autista'];
        }

        $query2 = "SELECT * FROM `viaggio` WHERE id_autista = '$idAutista'";
        $result2 = $connessione->query($query2);
        
        // Se ci sono risultati
        if ($result2->num_rows > 0) {
        // Ciclo per mostrare ogni riga della tabella dei viaggi
        while ($row = $result2->fetch_assoc()) {

            // Controlla se lo stato del viaggio è "chiusa"
        if ($row['stato'] == 'chiuse') {
           continue;  // Se stato è "chiusa", salta questa iterazione e non mostra il viaggio
        }
        
        echo "<table border='1' cellpadding='8' style='width: 100%; table-layout: fixed;'>";
        echo "<tr>
                <th>Partenza</th>
                <th>Arrivo</th>
                <th>Data</th>
                <th>Durata (h)</th>
                <th>Orario</th>
                <th>Posti</th>                       
                <th>Soste</th>                        
                <th>Costo (€)</th>
              </tr>";

        echo "<tr>
                <td>{$row['citta_partenza']}</td>
                <td>{$row['citta_arrivo']}</td>
                <td>{$row['data']}</td>
                <td>{$row['tempo']}</td>
                <td>{$row['orario']}</td>
                <td>{$row['numero_passeggeri']}</td>
                <td>{$row['soste']}</td>
                <td>{$row['costo']}</td>
              </tr>";
    
        
        // Query per ottenere i dati dell'passeggero associato al viaggio
        $query3 = "SELECT id_passeggero FROM partecipa WHERE id_viaggio = '{$row['id_viaggio']}'";
        $result3 = $connessione->query($query3);

        if ($result3 && $result3->num_rows > 0) {
        // Ora cicliamo su tutti i passeggeri
        while ($passeggero_row = $result3->fetch_assoc()) {
        $idPasseggero = $passeggero_row['id_passeggero'];

        // Query per ottenere i dettagli del passeggero
        $query4 = "SELECT * FROM `passeggero` WHERE id_passeggero = '$idPasseggero'";
        $result4 = $connessione->query($query4);

        if ($result4 && $result4->num_rows > 0) {
            // Cicliamo sui dati del passeggero
            while ($passeggero = $result4->fetch_assoc()) {
                echo "<table border='1' cellpadding='8' style='width: 100%; table-layout: fixed; margin-bottom: 20px;'>";
                echo "<tr >
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Telefono</th>
                        <th>Email</th>
                      </tr>";
                echo "<tr onclick='selezionaPasseggero({$row['id_viaggio']},$idPasseggero )'>
                        <td>{$passeggero['nome']}</td>
                        <td>{$passeggero['cognome']}</td>
                        <td>{$passeggero['telefono']}</td>
                        <td>{$passeggero['email']}</td>
                      </tr>";
                echo "</table>";
            }//Gestione della risposta del server
        } else {
            echo "<p>Passeggero non trovato per questo viaggio.</p>";
        }
        }
        } else {
            echo "<p>Nessun passeggero trovato per questo viaggio.</p>";
        }
                }
            }
        }
     ?>
    </div>
 
    <!-- Chiudi prenotazioni -->
    <div id="Add_prenotazione" class="form-container" style="display: none;">
        <h2>Inserisci le informazioni relative al viaggio: </h2>
        <form method="POST">
            <input type="hidden" name="action" value="prenotato">
            <input type="submit" value="Visualizza"> 
        </form>

       <?php
        //Memorizzazione di tutti i dati inseriti dall'utente nel php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'prenotato') {

        //Recupera l'id dell'autista
        $query = "SELECT id_autista FROM autista WHERE email = '$Email' "; 
        $result = $connessione->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $idAutista = $row['id_autista'];
        } 

        //Recupera tutte le informazionio relative al viaggio dall'id dell'utente
        $query2 = "SELECT * FROM `viaggio` WHERE id_autista = '$idAutista'";
        $result2 = $connessione->query($query2);
        
        // Se ci sono risultati
        if ($result2->num_rows > 0) {
        // Ciclo per mostrare ogni riga della tabella dei viaggi
        while ($row = $result2->fetch_assoc()) {

        // Controlla se lo stato del viaggio è "chiusa"
        if ($row['stato'] == 'chiuse') {
           continue;  // Se stato è "chiusa", salta questa iterazione e non mostra il viaggio
        }
        
        echo "<table border='1' cellpadding='8' style='width: 100%; table-layout: fixed;'>";
        echo "<tr>
                <th>Partenza</th>
                <th>Arrivo</th>
                <th>Data</th>
                <th>Durata (h)</th>
                <th>Orario</th>
                <th>Posti</th>                       
                <th>Soste</th>                        
                <th>Costo (€)</th>
              </tr>";

        echo "<tr onclick='chiudiViaggio({$row['id_viaggio']})'>
                <td>{$row['citta_partenza']}</td>
                <td>{$row['citta_arrivo']}</td>
                <td>{$row['data']}</td>
                <td>{$row['tempo']}</td>
                <td>{$row['orario']}</td>
                <td>{$row['numero_passeggeri']}</td>
                <td>{$row['soste']}</td>
                <td>{$row['costo']}</td>
              </tr>";
    
        
        }
        }
        }
     ?>
    </div>

    <!-- Aggiungi recensione -->
<div id="Add_recensione" class="form-container" style="display: none;">
    <h2>Inserisci le informazioni relative al viaggio: </h2>
    <form method="POST">
        Città di partenza: <input type="text" name="partenza" required>
        <br><br>
        Città di arrivo: <input type="text" name="arrivo" required>
        <br><br>

            <?php
            // Calcola la data di domani
            $domani = date('Y-m-d', strtotime('+1 day'));
            ?>
            Data di partenza: <input type="date" name="data" value="<?php echo $domani; ?>" min="<?php echo $domani; ?>" required>
            <br><br>

        Email utente: <input type="text" name="mail" required>
        <br><br>
        Commento: <input type="text" name="commento" required>
        <br><br>
        Voto: <input type="number" name="voto" required min="1" max="5">
        <br><br>
        <input type="hidden" name="action" value="invia">
        <input type="submit" value="Invia"> 
    </form>

    <?php
    // Verifica che il modulo sia stato inviato con il tasto "Invia"
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'invia') {

        //Recupero tutti i dati dal form
        $Partenza = $_POST['partenza'];
        $Arrivo = $_POST['arrivo'];
        $Data = $_POST['data'];
        $Mail= $_POST['mail'];
        $Commento = $_POST['commento'];
        $Voto = $_POST['voto'];


        // Connessione al database (assicurati che $connessione sia correttamente definita)
        $query = "SELECT id_autista, id_viaggio FROM viaggio WHERE citta_partenza = '$Partenza' AND citta_arrivo = '$Arrivo' AND data = '$Data'"; 
        $result = $connessione->query($query);

        // Stampa la query per il debug
    
        $IdAutista = null;
        $IdViaggio = null; 
        // Verifica se la query ha restituito un risultato
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $IdAutista = $row['id_autista'];
            $IdViaggio = $row['id_viaggio'];

        } 
    
        // Se il viaggio o l'autista non sono trovati, interrompi l'operazione
        if ($IdViaggio == null || $IdAutista == null) {
            echo "<p>Viaggio o autista non trovati per i dati inseriti.</p>";
            return;
        }
        
        //Recupera l' id del passeggero
        $query2 = "SELECT id_passeggero FROM passeggero WHERE email = '$Mail' "; 
        $result2 = $connessione->query($query2);
    
        if ($result2 && $result2->num_rows > 0) {
            $row = $result2->fetch_assoc();
            $idPasseggero = $row['id_passeggero'];

            // Inserisci i dati nel feedback
            $insertQuery = "INSERT INTO feedback (recensione, voto, id_viaggio, id_autista, id_utente, recensitore) VALUES ('$Commento', '$Voto', '$IdViaggio', '$IdAutista', '$idPasseggero', 'autista')";
            
            //Gestisci la risposta dell'operazione
            if ($connessione->query($insertQuery) === TRUE) {
                echo "Partecipazione registrata con successo!";
            } else {
                echo "Errore durante la registrazione della partecipazione:" . $connessione->error;
            }
        } else {
            echo "Utente non trovato.";
        }
    } else {
        echo "Dati mancanti.";
    }
    
    
    ?>
</div>

<!-- Visualizza recensioni degli utenti -->
<div id="Add_visualizza" class="form-container" style="display: none;">
    <h2>Cerca recensioni passeggero: </h2>
    <form method="POST">
        Emai passeggero: <input type="text" name="email" required>
        <br><br>
        <input type="hidden" name="action" value="voto">
        <input type="submit" value="valutazioni"> 
    </form>

    <?php
    // Verifica che il modulo sia stato inviato con il tasto "valutazione"
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'voto') {

        // Recupera l'identificativo del passeggero
        $query = "SELECT id_passeggero FROM passeggero WHERE email = '$Email'"; 
        $result = $connessione->query($query);
    
        $IdPasseggero = null;
        // Verifica se la query ha restituito un risultato
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $IdPasseggero = $row['id_passeggero'];

        } 
        
        // Recupera il voto,la recensione e il recensitore ovvero i dati dell feedback dati a un autista
        $query = "SELECT recensione, voto, recensitore FROM feedback WHERE id_passeggero = '$IdPasseggero'"; 
        $result = $connessione->query($query);
    
        if ($result && $result->num_rows > 0) {
            echo "<h3>Recensioni trovate:</h3>";
            $sommaVoti = 0;
            $numRecensioni = 0;

            while ($row = $result->fetch_assoc()) {

            // Ignora le recensioni dove il recensitore è "autista"
            if (strtolower(trim($row['recensitore'])) === 'passeggero') {
                continue;
            }

                $recensione = $row['recensione'];
                $voto = $row['voto'];
                echo "<p><strong>Voto:</strong> $voto<br><strong>Recensione:</strong> $recensione</p>";
                
                $sommaVoti += $voto;
                $numRecensioni++;
            }

            $votoMedio = $sommaVoti / $numRecensioni;
            echo "<h4>Voto medio: " . number_format($votoMedio, 2) . "</h4>";
        } else {
            echo "<p>Nessuna recensione trovata per questo utente.</p>";
        }
    }
?>

</body>
</html>