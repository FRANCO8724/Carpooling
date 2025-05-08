<?php

session_start();

// Recupera l'email dell'utente dalla sessione, se presente
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}

// Azione inviata dal form
$action = $_POST['action'] ?? null;

// Connessione al database
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "carpooling";

$connessione = new mysqli($host, $user, $password, $database);

// Controllo connessione
if ($connessione === false) {
    die("Errore di connessione: " . $connessione->connect_error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestione Dati</title>
    <link rel="stylesheet" href="css/stylesacc.css">
    <script>
        function mostraForm(tipo) {
            const forms = document.querySelectorAll('.form-container');
            forms.forEach(form => form.style.display = 'none');
            document.getElementById(tipo).style.display = 'block';
        }
        // Funzione che viene chiamata quando una riga viene cliccata dal passeggero
        function selezionaViaggio(idViaggio) {
        // Creiamo una richiesta AJAX
        var xhr = new XMLHttpRequest();
        //Colleghiamo al file prenotaszione.php
        xhr.open("POST", "prenotazione.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Inviamo l'ID del viaggio e l'email dell'utente
        var email = '<?php echo $_SESSION['email']; ?>';  // Recupera l'email dell'utente dalla sessione
        xhr.send("id_viaggio=" + idViaggio + "&email=" + email);

        // Gestiamo la risposta del server
        xhr.onload = function() {
            if (xhr.status == 200) {
                alert("Viaggio prenotato con successo!");
            } else {
                alert("Errore durante la prenotazione.");
            }
        };
    }
</script>
</head>
<body>
    <h2>Seleziona l'azione da eseguire</h2>
    <button onclick="mostraForm('cerca')">Cerca viaggio</button>
    <button onclick="mostraForm('prenota')">Visualizza prenotazione</button>
    <button onclick="mostraForm('valuta')">Valuta viaggio</button>
    <button onclick="mostraForm('voti')">Visualizza voti autista</button>

<!-- Form che permette all'utente di ricercare un viaggio data città di partenza e città di arrivo-->
<div id="cerca" class="form-container" style="display: none;">
        <h2>Inserissci i dati del viaggio da cercare: </h2>
        <form method="POST"> 

        Città di partenza: <input type="text" name="partenza" required>
        <br><br>
        Città di arrivo: <input type="text" name="arrivo" required>
        <br><br>

        <!-- Funzione che evita all'utente di inserire una data sbagliata -->
        <?php
            // Calcola la data di domani
            $domani = date('Y-m-d', strtotime('+1 day'));
        ?>
        Data di partenza: <input type="date" name="data" value="<?php echo $domani; ?>" min="<?php echo $domani; ?>" required>
        <br><br>

        <input type="hidden" name="action" value="trova">
        <input type="submit" value="Cerca"> 
        </form>

        <?php
        // Verifica che il modulo sia stato inviato con il tasto "Cerca"
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'trova') {
    // Ottieni i dati dal form
    $Partenza = $_POST['partenza'];
    $Arrivo = $_POST['arrivo'];
    $Data = $_POST['data'];

    // Query per ottenere i viaggi in base ai dati inseriti
    $query = "SELECT * FROM `viaggio` WHERE citta_partenza = '$Partenza' AND citta_arrivo = '$Arrivo' AND data = '$Data'";

    $result = $connessione->query($query);

    // Se ci sono risultati
    if ($result->num_rows > 0) {
            echo "<h3>Viaggi trovati:</h3>";
        // Ciclo per mostrare ogni riga della tabella dei viaggi
        while ($row = $result->fetch_assoc()) {

            // Controlla se lo stato del viaggio è "chiusa"
        if ($row['stato'] == 'chiuse') {
            continue;  // Se stato è "chiusa", salta questa iterazione e non mostra il viaggio
        }
            //Stampa dei risultati nelle relative tabelle
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

            echo "<tr onclick='selezionaViaggio({$row['id_viaggio']})'>
                    <td>{$row['citta_partenza']}</td>
                    <td>{$row['citta_arrivo']}</td>
                    <td>{$row['data']}</td>
                    <td>{$row['tempo']}</td>
                    <td>{$row['orario']}</td>
                    <td>{$row['numero_passeggeri']}</td>
                    <td>{$row['soste']}</td>
                    <td>{$row['costo']}</td>
                  </tr>";

            // Ottieni l'ID dell'autista per il viaggio corrente
            $idAutista = $row['id_autista']; 
            
            // Query per ottenere i dati dell'autista associato al viaggio
            $query2 = "SELECT * FROM `autista` WHERE id_autista = '$idAutista'";
            $resultAutista = $connessione->query($query2);

            // Se ci sono dati dell'autista stampo la sua riga della tabella relativa
            if ($resultAutista->num_rows > 0) {
                echo "<table border='1' cellpadding='8' style='width: 100%; table-layout: fixed; margin-bottom: 20px;'>";
                echo "<tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Sesso</th>
                        <th>Telefono</th>
                        <th>Dati Auto</th>
                        <th>Scadenza Patente</th>
                      </tr>";

                while ($autista = $resultAutista->fetch_assoc()) {
                    echo "<tr>
                            <td>{$autista['nome']}</td>
                            <td>{$autista['cognome']}</td>
                            <td>{$autista['sesso']}</td>
                            <td>{$autista['recapito_telefono']}</td>
                            <td>{$autista['dati_auto']}</td>
                            <td>{$autista['scadenza_patente']}</td>
                            <td></td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Autista non trovato per questo viaggio.</p>";
            }
        }
          //Gestione degli esiti
        echo "</table>";

    } else {
        echo "<p>Nessun viaggio trovato per i criteri selezionati.</p>";
    }
    }
    ?>

</div>

<!-- Form che permette all'utente di visualizzare i viaggi che ha prenotato-->
<div id="prenota" class="form-container" style="display: none;">
    <h2>Tuoi viaggi: </h2>
    <form method="POST">
        <input type="hidden" name="action" value="Visualizza">
        <input type="submit" value="Mostra"> 
    </form>

    <?php
    // Verifica che il modulo sia stato inviato con il tasto "Mostra"
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'Visualizza') {

        // Prendi l'ID del passeggero
        $query2 = "SELECT id_passeggero FROM passeggero WHERE email = '$email'"; 
        $result2 = $connessione->query($query2);
        
        $idPasseggero = null;

        if ($result2 && $result2->num_rows > 0) {
            $row = $result2->fetch_assoc();
            $idPasseggero = $row['id_passeggero'];
        }

        // Prendi tutti gli ID dei viaggi a cui partecipa il passeggero
        $query3 = "SELECT id_viaggio FROM partecipa WHERE id_passeggero = '$idPasseggero'"; 
        $result3 = $connessione->query($query3);

        //Se ci sono risultati mostrali nelle relative tabelle
        if ($result3 && $result3->num_rows > 0) {            
            
            while ($row = $result3->fetch_assoc()) {
                $idViaggio = $row['id_viaggio'];

                echo "<h3>Viaggio trovato:</h3>";
                // Query per ottenere i dettagli del viaggio
                $query = "SELECT * FROM `viaggio` WHERE id_viaggio = '$idViaggio'";
                $result = $connessione->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Stampa i dettagli di ogni viaggio
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
                        echo "</table><br>";
                    // Recupera ID autista
                    $idAutista = $row['id_autista'];

                    // Query per recuperare i dati dell'autista (modifica nome tabella e colonne secondo il tuo DB)
                    $queryAutista = "SELECT * FROM autista WHERE id_autista = '$idAutista'";
                    $resultAutista = $connessione->query($queryAutista);

                    if ($resultAutista && $resultAutista->num_rows > 0) {
                        $autista = $resultAutista->fetch_assoc();

                        echo "<h4>Dati Autista:</h4>";
                        echo "<table border='1' cellpadding='8' style='width: 100%; table-layout: fixed;'>";
                        echo "<tr>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Email</th>
                                <th>Telefono</th>
                              </tr>";

                        echo "<tr>
                                <td>{$autista['nome']}</td>
                                <td>{$autista['cognome']}</td>
                                <td>{$autista['email']}</td>
                                <td>{$autista['recapito_telefono']}</td>
                              </tr>";
                        echo "</table><br><br>";
                    } else {
                        echo "<p>Dati autista non disponibili.</p><br>";
                    }
                }
            }
        }
    } else {
        echo "<p>Non ci sono viaggi associati a questo passeggero.</p>";
    }
    }
    ?>
</div>

<!-- Form che permette all'utente di valutare un viaggio -->
<div id="valuta" class="form-container" style="display: none;">
    <h2>Valuta il tuo viaggio: </h2>
    <form method="POST">
        Città di partenza: <input type="text" name="partenza" required>
        <br><br>
        Città di arrivo: <input type="text" name="arrivo" required>
        <br><br>
    
        <!-- Funzione che evita all'utente di inserire una data sbagliata -->
        <?php
        // Calcola la data di domani
        $domani = date('Y-m-d', strtotime('+1 day'));
        ?>
        Data di partenza: <input type="date" name="data" value="<?php echo $domani; ?>" min="<?php echo $domani; ?>" required>
        <br><br>

        Commento: <input type="text" name="commento" required>
        <br><br>
        Voto: <input type="number" name="voto" required min="0" max="10">
        <br><br>
        <input type="hidden" name="action" value="invia">
        <input type="submit" value="Invia"> 
    </form>

    <?php
    // Verifica che il modulo sia stato inviato con il tasto "Invia"
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'invia') {

         // Ottieni i dati dal form
        $Partenza = $_POST['partenza'];
        $Arrivo = $_POST['arrivo'];
        $Data = $_POST['data'];
        $Commento = $_POST['commento'];
        $Voto = $_POST['voto'];


        // Recupera l'identificativo del passeggero e del viaggio dati i valori all interno di una riga
        $query = "SELECT id_autista, id_viaggio FROM viaggio WHERE citta_partenza = '$Partenza' AND citta_arrivo = '$Arrivo' AND data = '$Data'"; 
        $result = $connessione->query($query);
    
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
        
        //Recupera l'id del passeggero
        $query2 = "SELECT id_passeggero FROM passeggero WHERE email = '$email' "; 
        $result2 = $connessione->query($query2);
    
        if ($result2 && $result2->num_rows > 0) {
            $row = $result2->fetch_assoc();
            $idPasseggero = $row['id_passeggero'];

            // Inserisci i dati nel feedback
            $insertQuery = "INSERT INTO feedback (recensione, voto, id_viaggio, id_autista, id_utente, recensitore) VALUES ('$Commento', '$Voto', '$IdViaggio', '$IdAutista', '$idPasseggero', 'passeggero')";
            
            //Gestione del risultato dell' inserimento
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

<!-- Visualizza recensioni -->
<div id="voti" class="form-container" style="display: none;">
    <h2>Cerca recensioni autista: </h2>
    <form method="POST">
        Emai autista: <input type="text" name="email" required>
        <br><br>
        <input type="hidden" name="action" value="voto">
        <input type="submit" value="valutazioni"> 
    </form>

    <?php
    // Verifica che il modulo sia stato inviato con il tasto "Invia"
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'voto') {

        // Ottieni i dati dal form
        $Email = $_POST['email'];

        // Recupera l'identificativo dell'autista
        $query = "SELECT id_autista FROM autista WHERE email = '$Email'"; 
        $result = $connessione->query($query);
    
        $IdAutista = null;
        // Verifica se la query ha restituito un risultato
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $IdAutista = $row['id_autista'];

        } 
        
        // Recupera il voto,la recensione e il recensitore ovvero i dati dell feedback dati a un autista
        $query = "SELECT recensione, voto, recensitore FROM feedback WHERE id_autista = '$IdAutista'"; 
        $result = $connessione->query($query);
    
        if ($result && $result->num_rows > 0) {
            echo "<h3>Recensioni trovate:</h3>";
            $sommaVoti = 0;
            $numRecensioni = 0;

            while ($row = $result->fetch_assoc()) {

            // Ignora le recensioni dove il recensitore è "autista"
            if (strtolower(trim($row['recensitore'])) === 'autista') {
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
            echo "<p>Nessuna recensione trovata per questo autista.</p>";
        }
    }
?>
</div>

</body>
</html>