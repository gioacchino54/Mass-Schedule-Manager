# Changelog

Tutte le modifiche rilevanti a `com_messe`, `mod_messe` e al meccanismo di distribuzione/aggiornamento sono documentate in questo file.

## [1.3.26] - com_messe / pkg_messe (mod_messe 1.1.11)
- **Domenica delle Palme**: aggiunta all'elenco delle feste mobili riconosciute, ora etichettata correttamente sul frontend invece di apparire come generico "Festivo".
- **Nuova sezione "Settimana Santa"** per ogni chiesa: riti particolari (Via Crucis, Confessioni, Cena del Signore, ecc.) inseribili per giorno di riferimento (Domenica delle Palme, Lunedì/Martedì/Mercoledì Santo, Giovedì Santo, Venerdì Santo, Sabato Santo di giorno), con data calcolata automaticamente ogni anno in base alla Pasqua. Ogni rito ha una modalità **Aggiungi** (si somma all'orario normale del giorno, default) o **Sostituisci** (lo rimpiazza interamente — utile per Giovedì/Venerdì Santo). Nuova tabella `messe_settimana_santa`.
- **Eccezioni "aggiuntive"**: nuovo campo Modalità (Sostituisci / Aggiungi) per ogni "Celebrazione Speciale". In modalità Aggiungi, l'eccezione si somma all'orario normale del giorno della settimana invece di sostituirlo interamente — risolve il caso del 24 dicembre in giorno feriale, dove prima la messa feriale del mattino spariva del tutto, nascosta dalla sola celebrazione speciale serale. Il rilevamento automatico "prefestivo" viene bypassato per i giorni con evento aggiuntivo, dato che la celebrazione speciale è già gestita manualmente. Nuova colonna `messe_eccezioni.modalita`.
- Corretto il calcolo di "prossima messa" perché scelga sempre l'orario cronologicamente più vicino (scorrendo l'elenco già ordinato) invece di fissarsi sul primo elaborato nel ciclo interno — bug emerso proprio con le eccezioni aggiuntive, dove l'ordine di elaborazione non coincideva con l'ordine cronologico.
- **Fix importante (cambio ora legale)**: il calcolo di tutte le feste mobili (Ascensione, Pentecoste, Corpus Domini, Sacro Cuore, ecc.) e della Veglia Pasquale usava aritmetica su secondi grezzi (N×86400) per spostarsi dalla data di Pasqua — vulnerabile al cambio dell'ora legale: se il periodo attraversa la data del cambio ora (fine marzo o fine ottobre), il calcolo sbagliava di un'ora e poteva risultare nel giorno di calendario sbagliato. Riscontrato testando la Domenica delle Palme 2026, che cade esattamente il giorno del cambio ora. Sostituito con aritmetica basata su `strtotime()`, sicura rispetto al cambio ora.

## [Meccanismo di aggiornamento] - non versionato (fix su pkg_messe.xml lato server)
- **Fix critico**: il file `pkg_messe.xml` (descrittore update server) mancava del tag `<client>site</client>`. Senza questo tag, Joomla assegna di default `client_id = 1` (amministratore) a ogni `<update>`, che non corrisponde al `client_id` reale del pacchetto installato (`0`, "site") — l'aggiornamento veniva quindi scartato silenziosamente, senza nessun errore visibile, pur essendo il file XML raggiungibile e valido. Diagnosticato tramite CLI (`php cli/joomla.php update:extensions:check`) e confronto con il codice sorgente di `ExtensionAdapter.php` di Joomla.
- Aggiunto anche il checksum `sha256` nel tag `<downloadurl>`, richiesto da Joomla per la convalida di integrità del pacchetto scaricato (altrimenti mostra un avviso di sicurezza dopo l'installazione).
- Vedi [`distribution/README.md`](distribution/README.md) per la checklist da seguire ad ogni nuova release.

## [1.3.24] - com_messe / pkg_messe
- Nuova opzione per chiesa **"Sabato con Solennità"**: quando una solennità (fissa o mobile) cade proprio di sabato, determina se usare l'orario **Festivo** (default: tratta l'intera giornata come la solennità stessa) oppure l'orario **Vigiliare** (tratta comunque la sera come anticipo della domenica successiva). In precedenza il comportamento era sempre "festivo", senza possibilità di scelta.
- Nuova colonna `messe_chiese.sabato_solennita` (`festivo` / `vigiliare`), migrata automaticamente su siti già esistenti.

## [1.3.23] - pkg_messe
- Corretti gli URL dell'update server e del footer da `http` a `https`.
- Aggiunto footer con copyright e licenza (GNU GPL v2 o successiva) in fondo alle view backend "Chiese" (elenco) e "Chiesa" (aggiungi/modifica), con anno aggiornato automaticamente. *(com_messe 1.3.22)*

## [1.3.21] - com_messe / pkg_messe 1.3.22
- Cambiato il valore di default di "Giorni anticipo prefestive" da 14 a 5, per evitare che una prefestiva compaia nell'elenco molto più avanti nel tempo rispetto alle altre messe mostrate. Vincolo minimo del campo abbassato da 7 a 1.

## [1.3.20] - com_messe / pkg_messe 1.3.21
- Fix: il form dei parametri della voce di menu "Orari Messe" era rimasto indietro e non includeva i campi relativi alla messa prefestiva aggiunti nelle versioni recenti. Ora configurabili anche per singola voce di menu.
- Fix: cartella lingue del pacchetto spostata da `admin/language` a `language` (convenzione corretta per estensioni di tipo "package"), risolvendo la visualizzazione della chiave grezza `PKG_MESSE_XML_DESCRIPTION` invece del testo tradotto a fine installazione.

## [1.3.19] - com_messe
- Fix: quando un periodo stagionale (es. "Orario Estivo") è attivo, la messa prefestiva ora si basa sull'orario stagionale realmente in vigore quel giorno. Aggiunta opzione facoltativa "Applica i periodi stagionali alla messa prefestiva" (attiva di default).

## [1.3.18] - com_messe
- Modalità "Ultima messa feriale serale": la soglia oraria diventa una fascia min-max (es. 16:00-20:00) e viene resa facoltativa tramite la nuova opzione "Applica una fascia oraria alla prefestiva automatica".

## [1.3.17] - com_messe
- Fix: i periodi stagionali non vengono più applicati al calcolo della messa prefestiva, che si basa sempre sull'orario configurato "normale" (poi reso opzionale nella 1.3.19).

## [1.3.16] - com_messe
- Nuova opzione "Mostra il giorno della settimana nelle Messe Prefestive" (disattiva di default).

## [1.3.15] - com_messe
- Fix visualizzazione "Messe Prefestive": non viene più mostrato l'elenco dei giorni della settimana della fascia feriale sottostante, essendo un'occorrenza singola. Ora viene mostrato solo l'orario.

## [1.3.14] - com_messe
- Modalità Messa Prefestiva configurabile per chiesa: nuova colonna `messe_chiese.modalita_prefestiva` (nessuna / vigiliare / dedicato / feriale_serale), nuovo tipo `prefestivo` nell'ENUM `messe_orari.tipo`.

## [1.3.13] - com_messe
- Fix logica messa prefestiva: una messa feriale viene considerata "prefestiva" solo se è una messa serale (nuova opzione "Ora minima messa prefestiva", default 16).

## [1.3.12] - com_messe
- Nuova opzione componente "Mantieni dati alla disinstallazione" (attiva di default): disinstallando il componente le tabelle non vengono più eliminate automaticamente.

## [1.3.11] - com_messe
- Frontend: visualizzazione dei giorni della settimana impostati nel campo "giorni" di un orario (es. "Lunedì, Giovedì") accanto all'orario.

## [1.3.10] - com_messe
- Fix errore `in_array(): Argument #2 must be of type array, int given` sul frontend quando il campo "giorni" contiene un solo numero.

## [1.3.9] - com_messe
- Fix pulsante Salva al primo inserimento: il model `save()` ora passa `$data` per riferimento e scrive l'id direttamente in `$data['id']`.

## [1.3.8] - com_messe
- Fix etichette pulsanti toolbar form Chiesa (costanti di lingua Joomla `JTOOLBAR_APPLY` / `JTOOLBAR_SAVE`).

## [1.3.7] - com_messe
- Toolbar form Chiesa: 4 pulsanti distinti — Salva / Salva e Nuovo / Salva e Chiudi / Chiudi.

## [1.3.6] - com_messe
- Fix installazione/disinstallazione tabelle DB (rete di sicurezza in `script.php`), fix percorso SQL uninstall, aggiunto tag `<install>` nel manifest.

## [1.3.5] e precedenti
- Security: cast `(int)` su ora/minuti in `mktime`, fix data manifest, security hardening (CSRF, autorizzazioni, sanitizzazione input), aggiunta del metodo `add()` al controller, riscrittura completa del backend form chiesa.

---

*Per la cronologia dettagliata di ogni singola versione del componente, vedi i file in `com_messe/admin/sql/updates/mysql/`.*
