# Gestione Orari Messe (com_messe + mod_messe)

*Versione pacchetto: 1.3.23 (com_messe 1.3.22 · mod_messe 1.1.11)*

## Descrizione breve (per il campo "Short Description" di JED)

Componente Joomla per gestire e pubblicare gli orari delle Sante Messe di una o più chiese, con messe prefestive configurabili, eccezioni per festività, periodi speciali (es. estivi) e un modulo widget per mostrarli ovunque nel sito.

## Descrizione estesa

**Gestione Orari Messe** è un componente per Joomla 5 e 6 pensato per parrocchie, diocesi e siti di comunità religiose che devono pubblicare in modo semplice e sempre aggiornato gli orari delle Sante Messe di una o più chiese.

Dal pannello di amministrazione è possibile:

- gestire un numero illimitato di chiese, ciascuna con nome, descrizione, indirizzo e rito (romano o ambrosiano);
- impostare gli orari settimanali suddivisi per **feriale**, **vigilia** (sabato) e **festivo**, con possibilità di limitare un singolo orario solo ad alcuni giorni della settimana (es. il martedì e il giovedì), mostrati automaticamente per nome sul frontend;
- configurare in modo flessibile, per ciascuna chiesa, **come gestire la messa prefestiva** (l'anticipo di una festa infrasettimanale, es. la sera prima di una solennità): nessuna, stesso orario della vigiliare del sabato, un orario dedicato specifico, oppure calcolo automatico dall'ultima messa feriale serale (con fascia oraria min-max facoltativa);
- scegliere se i **periodi stagionali attivi** (es. orario estivo) debbano determinare anche l'orario della messa prefestiva, oppure se questa debba sempre ignorarli;
- gestire **eccezioni puntuali** per una data specifica (es. orario speciale per una festività patronale);
- gestire **periodi** (intervalli di date o interi mesi, es. luglio-agosto) durante i quali un orario può essere soppresso o sostituito con un orario alternativo (utile per gli orari estivi);
- mostrare sul frontend, per ciascuna chiesa, la prossima Santa Messa in programma e l'elenco completo degli orari, con l'opzione di mostrare anche il giorno della settimana nell'elenco delle messe prefestive;
- includere una modalità di test con data simulata, utile per verificare in anteprima come verranno mostrati gli orari in un giorno futuro (es. Natale) senza dover attendere quella data;
- scegliere se **mantenere i dati nel database** in caso di disinstallazione del componente, per non perdere la configurazione durante un aggiornamento.

Il pacchetto include anche **mod_messe**, un modulo per il frontend che permette di mostrare il prossimo orario di Messa (o l'elenco completo) di una chiesa specifica in qualunque posizione del template, ideale per la home page o la sidebar.

## Caratteristiche principali

- Gestione multi-chiesa con rito romano/ambrosiano
- Orari feriali, vigilia e festivi, con limitazione opzionale a specifici giorni della settimana (mostrati per nome sul frontend, es. "Martedì, Giovedì")
- **Messa prefestiva configurabile per chiesa** in 4 modalità (nessuna / stesso orario della vigiliare / orario dedicato / calcolo automatico da ultima messa feriale serale con fascia oraria facoltativa)
- Scelta se i periodi stagionali influenzano o meno il calcolo della messa prefestiva
- Eccezioni per singole date (festività, eventi particolari)
- Periodi con soppressione o sostituzione degli orari (es. orari estivi)
- Visualizzazione automatica della prossima Santa Messa
- Modulo widget dedicato per il frontend, configurabile per chiesa
- Modalità di test con data simulata
- Opzione per mantenere i dati nel database in caso di disinstallazione (utile per aggiornamenti puliti)
- Pulsanti di salvataggio del form completi (Salva / Salva e Nuovo / Salva e Chiudi / Chiudi)
- Footer con copyright e licenza nel backend, con anno aggiornato automaticamente
- Interfaccia amministrativa in italiano e inglese
- Sviluppato secondo gli standard Joomla (namespace PSR-4, MVC, ACL)
- Rilasciato con licenza GNU GPL v2 o successiva

## Requisiti

- Joomla! 5.x o 6.x
- PHP 8.1 o superiore
- MySQL / MariaDB con supporto InnoDB e utf8mb4

## Installazione

1. Da **Sistema → Gestisci → Installa**, carica il file `pkg_messe_vX.X.X.zip` (il pacchetto contiene sia il componente `com_messe` sia il modulo `mod_messe`).
2. Al termine dell'installazione, il componente crea automaticamente le tabelle necessarie nel database.
3. In caso di aggiornamento a una versione successiva, non è necessario disinstallare la versione precedente: è sufficiente installare il nuovo pacchetto sopra quello esistente.

## Guida all'uso del componente (com_messe)

### 1. Aggiungere una chiesa

Vai su **Componenti → Gestione Orari Messe → Chiese → Nuovo**. Inserisci nome, descrizione, indirizzo e rito. Salva.

### 2. Impostare gli orari settimanali

Nella scheda di modifica della chiesa, per ciascuna fascia (Feriale, Vigilia, Festivo) puoi aggiungere uno o più orari, indicando ora, minuti e un'etichetta opzionale (es. "Messa cantata"). Nel campo "Giorni" puoi indicare i giorni della settimana in cui vale quell'orario specifico (numeri da 0 a 6, dove 0 = domenica, separati da virgola, es. `2,4` per martedì e giovedì); lasciando il campo vuoto, l'orario si applica a tutti i giorni della fascia selezionata. Sul frontend il giorno viene mostrato automaticamente per nome (es. "Martedì, Giovedì") accanto all'orario.

### 3. Modalità Messa Prefestiva

Ogni chiesa può gestire in modo diverso la messa che anticipa una festa infrasettimanale (es. la sera prima del 1° novembre). Nella card **"Modalità Messa Prefestiva"** puoi scegliere tra:

- **Nessuna** — questa chiesa non ha messa prefestiva;
- **Stesso orario della Vigiliare (Sabato)** — riusa automaticamente gli orari già inseriti nella sezione "Vigilia";
- **Orario dedicato specifico** — usa gli orari inseriti nella sezione "Prefestiva dedicata" (identica alle altre, con supporto al campo "Giorni");
- **Ultima messa feriale serale** — calcolo automatico: prende l'ultima messa feriale del giorno, con una fascia oraria min-max facoltativa (vedi Opzioni del componente) entro cui deve rientrare, altrimenti non mostra nessuna prefestiva quel giorno.

Se in quel giorno è attivo un **periodo stagionale** (es. "Orario Estivo"), puoi scegliere se questo debba determinare anche l'orario della prefestiva (opzione "Applica i periodi stagionali alla messa prefestiva", attiva di default) oppure se la prefestiva debba sempre basarsi sull'orario normale, ignorando i periodi.

### 4. Pulsanti del form

- **Salva** — salva e resta in modifica sulla stessa chiesa
- **Salva e Nuovo** — salva e apre un form vuoto per una nuova chiesa
- **Salva e Chiudi** — salva e torna all'elenco chiese
- **Chiudi** — chiude senza salvare

### 5. Eccezioni per una data specifica

Nella sezione "Eccezioni" puoi aggiungere un orario speciale valido solo per una data precisa (formato giorno-mese), utile per festività patronali o eventi occasionali.

### 6. Periodi (es. orari estivi)

Nella sezione "Periodi" puoi definire un intervallo di date o un elenco di mesi durante i quali un tipo di orario (es. feriale) viene soppresso oppure sostituito con orari alternativi.

### 7. Opzioni del componente

Da **Componenti → Gestione Orari Messe → Opzioni** puoi configurare:

- se mostrare la prossima Messa in evidenza;
- quanti giorni in avanti considerare per feriali (default 7), prefestivi (default 5) ed eventi speciali;
- se applicare una **fascia oraria** (minimo/massimo, es. 16:00-20:00) alla modalità "Ultima messa feriale serale", ed eventualmente disattivarla del tutto;
- se **mostrare il giorno della settimana** nell'elenco "Messe Prefestive" (es. "17:30 Giovedì" invece del solo "17:30");
- se i **periodi stagionali** influenzano il calcolo della messa prefestiva;
- la modalità di test (con data simulata) per verificare in anteprima la visualizzazione;
- se **mantenere i dati nel database** in caso di disinstallazione del componente (attivo di default: consigliato se prevedi di reinstallare/aggiornare il pacchetto).

Le stesse opzioni relative alla prefestiva sono disponibili anche a livello di singola **voce di menu** (per sovrascrivere il comportamento globale su una pagina specifica).

## Guida all'uso del modulo (mod_messe)

1. Vai su **Contenuti → Moduli di sito → Nuovo → Orari Messe**.
2. Seleziona la chiesa da mostrare tra quelle pubblicate.
3. Scegli se mostrare la prossima Messa, l'elenco completo degli orari, o entrambi.
4. Imposta la posizione del modulo nel template e le pagine in cui deve apparire.
5. Nei parametri del modulo puoi configurare le stesse opzioni di fascia oraria prefestiva, visualizzazione giorno e periodi stagionali disponibili nel componente.
6. Salva e pubblica.

## Supporto e licenza

Il componente è distribuito sotto licenza **GNU General Public License v2 o successiva**. Per segnalazioni o richieste di supporto, contattare l'autore.

---
*Autore: Gioacchino Cipriano*

*Estensione realizzata con il supporto di Claude AI (Anthropic).*
