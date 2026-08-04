# Gestione Orari Messe (com_messe + mod_messe)

[![Licenza: GPL v2](https://img.shields.io/badge/Licenza-GPLv2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)

*[Read this in English](README.en.md)*

Componente Joomla per gestire e pubblicare gli orari delle Sante Messe di una o più chiese, con messe prefestive configurabili, eccezioni per festività, periodi speciali (es. orari estivi) e un modulo widget per mostrarli ovunque nel sito.

## Descrizione

**Gestione Orari Messe** è pensato per parrocchie, diocesi e siti di comunità religiose che devono pubblicare in modo semplice e sempre aggiornato gli orari delle Sante Messe di una o più chiese.

Dal pannello di amministrazione è possibile:

- gestire un numero illimitato di chiese, ciascuna con nome, descrizione, indirizzo e rito (romano o ambrosiano);
- impostare gli orari settimanali suddivisi per **feriale**, **vigilia** (sabato) e **festivo**, con possibilità di limitare un singolo orario solo ad alcuni giorni della settimana;
- configurare in modo flessibile, per ciascuna chiesa, **come gestire la messa prefestiva** (l'anticipo di una festa infrasettimanale): nessuna, stesso orario della vigiliare del sabato, un orario dedicato specifico, oppure calcolo automatico dall'ultima messa feriale serale (con fascia oraria facoltativa);
- scegliere se i **periodi stagionali attivi** (es. orario estivo) debbano determinare anche l'orario della messa prefestiva;
- gestire **eccezioni puntuali** per una data specifica (es. festività patronale);
- gestire **periodi** (intervalli di date o interi mesi) durante i quali un orario può essere soppresso o sostituito con un orario alternativo;
- mostrare sul frontend la prossima Santa Messa e l'elenco completo degli orari, con visualizzazione del giorno della settimana quando rilevante;
- includere una modalità di test con data simulata.

Il pacchetto include anche **mod_messe**, un modulo per il frontend che mostra il prossimo orario di Messa (o l'elenco completo) di una chiesa specifica in qualunque posizione del template.

## Caratteristiche principali

- Gestione multi-chiesa con rito romano/ambrosiano
- Orari feriali, vigilia e festivi, con limitazione opzionale a specifici giorni della settimana
- Messa prefestiva configurabile per chiesa in 4 modalità
- Scelta se i periodi stagionali influenzano il calcolo della messa prefestiva
- Eccezioni per singole date e periodi con soppressione/sostituzione degli orari
- Visualizzazione automatica della prossima Santa Messa
- Modulo widget dedicato per il frontend
- Modalità di test con data simulata
- Opzione per mantenere i dati nel database in caso di disinstallazione
- Pulsanti di salvataggio del form completi (Salva / Salva e Nuovo / Salva e Chiudi / Chiudi)
- Interfaccia amministrativa in italiano e inglese
- Sviluppato secondo gli standard Joomla (namespace PSR-4, MVC, ACL)

## Requisiti

- Joomla! 5.x o 6.x
- PHP 8.1 o superiore
- MySQL / MariaDB con supporto InnoDB e utf8mb4

## Installazione

Scarica l'ultimo pacchetto dalla sezione [Releases](../../releases) di questo repository (`pkg_messe_vX.X.X.zip`) e installalo da **Sistema → Gestisci → Installa** nel backend di Joomla. Il pacchetto contiene sia il componente `com_messe` sia il modulo `mod_messe`.

Per aggiornare, non è necessario disinstallare la versione precedente: installa semplicemente il nuovo pacchetto sopra quello esistente.

## Guida rapida all'uso

### Aggiungere una chiesa
**Componenti → Gestione Orari Messe → Chiese → Nuovo**. Inserisci nome, descrizione, indirizzo e rito.

### Orari settimanali
Per ciascuna fascia (Feriale, Vigilia, Festivo) puoi aggiungere uno o più orari con ora, minuti, etichetta opzionale, e un campo "Giorni" (0-6, 0=domenica, separati da virgola) per limitare l'orario a giorni specifici.

### Modalità Messa Prefestiva
Per ogni chiesa scegli tra: Nessuna / Stesso orario della Vigiliare / Orario dedicato specifico / Ultima messa feriale serale (con fascia oraria facoltativa).

### Periodi ed eccezioni
Gestisci orari stagionali (es. estivi) e date speciali dalle rispettive sezioni del form chiesa.

### Modulo mod_messe
**Contenuti → Moduli di sito → Nuovo → Orari Messe** per mostrare gli orari di una chiesa in qualsiasi posizione del template.

Per la guida completa alle opzioni disponibili, vedi la cartella [`docs/`](docs/) di questo repository.

## Struttura del repository

```
.
├── com_messe/      Sorgenti del componente
├── mod_messe/      Sorgenti del modulo
├── docs/           Documentazione estesa (IT/EN)
├── LICENSE         Testo della licenza GPL v2
└── README.md       Questo file
```

## Contribuire / Segnalare un problema

Le segnalazioni di bug e le richieste di funzionalità sono benvenute nella sezione [Issues](../../issues) di questo repository.

## Licenza

Distribuito sotto licenza **GNU General Public License v2 o successiva**. Vedi il file [LICENSE](LICENSE) per il testo completo.

## Autore

**Gioacchino Cipriano** — [gioacchinocipriano.it](https://gioacchinocipriano.it/)

*Estensione realizzata con il supporto di Claude AI (Anthropic).*
