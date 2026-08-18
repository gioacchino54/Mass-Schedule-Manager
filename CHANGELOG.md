# Changelog

Tutte le modifiche rilevanti a `com_messe`, `mod_messe` e al meccanismo di distribuzione/aggiornamento sono documentate in questo file.

## [1.3.30] - com_messe / pkg_messe
- Fix accessibilità/contrasto colori: il badge del rito Ambrosiano nella lista chiese del backend usava le classi Bootstrap generiche `bg-info text-dark`, il cui aspetto reale dipende dalle variabili CSS del template di amministrazione — su alcuni temi il contrasto risultava scarso. Sostituito con colori fissi espliciti, identici a quelli già usati nel badge del frontend e del modulo. Corretta anche l'assenza dell'import della classe `Text` (il testo del badge era hardcoded in italiano, non tradotto).

## [1.3.29] - pkg_messe (mod_messe 1.1.12)
- Aggiunto anche nel modulo `mod_messe` il badge "Rito Romano" / "Rito Ambrosiano" accanto al nome della chiesa, già presente nel componente `com_messe` ma mai implementato nel template del modulo. Stesso stile grafico in entrambi.

## [1.3.28] - com_messe / pkg_messe (mod_messe 1.1.11)
- Etichettato il **Mercoledì delle Ceneri** (rito romano, Pasqua - 46 giorni) e il **Lunedì delle Ceneri** (rito ambrosiano, dove le Ceneri sono differite dal mercoledì al lunedì successivo alla I domenica di Quaresima, Pasqua - 41 giorni). A differenza delle solennità, questa etichetta **non cambia il tipo di orario del giorno** (resta feriale/vigilia/festivo normale): le Ceneri non sono un giorno "festivo" liturgicamente, solo un giorno feriale con un rito aggiuntivo. Calcolo sicuro rispetto al cambio ora legale (`strtotime()`).

## [1.3.27] - com_messe / pkg_messe (mod_messe 1.1.11)
- **Nomi liturgici tradizionali per le domeniche di Avvento e Quaresima**, distinti per rito:
  - Avvento romano (4 domeniche): Ad te levavi, Populus Sion, Gaudete, Rorate.
  - Avvento ambrosiano (6 domeniche): della Venuta del Signore, dei Figli del Regno, delle Profezie adempiute, dell'Ingresso del Messia, del Precursore, della Divina Maternità.
  - Quaresima romana (5 domeniche, la 6ª è la Domenica delle Palme già gestita): Invocavit, Reminiscere, Oculi, Laetare, Judica.
  - Quaresima ambrosiana (5 domeniche, stesse date del rito romano, nomi diversi): all'inizio della Quaresima, della Samaritana, di Abramo, del cieco nato, di Lazzaro.

## [1.3.26] - com_messe / pkg_messe (mod_messe 1.1.11)
- **Domenica delle Palme**: aggiunta all'elenco delle feste mobili riconosciute, ora etichettata correttamente sul frontend invece di apparire come generico "Festivo".
- **Nuova sezione "Settimana Santa"** per ogni chiesa: riti particolari (Via Crucis, Confessioni, Cena del Signore, ecc.) inseribili per giorno di riferimento, con data calcolata automaticamente ogni anno in base alla Pasqua. Ogni rito ha una modalità **Aggiungi** (si somma all'orario normale, default) o **Sostituisci** (lo rimpiazza interamente). Nuova tabella `messe_settimana_santa`.
- **Eccezioni "aggiuntive"**: nuovo campo Modalità (Sostituisci / Aggiungi) per ogni "Celebrazione Speciale". In modalità Aggiungi, l'eccezione si somma all'orario normale del giorno invece di sostituirlo interamente — risolve il caso del 24 dicembre in giorno feriale. Nuova colonna `messe_eccezioni.modalita`.
- Corretto il calcolo di "prossima messa" perché scelga sempre l'orario cronologicamente più vicino, scorrendo l'elenco già ordinato.
- **Fix importante (cambio ora legale)**: il calcolo di tutte le feste mobili e della Veglia Pasquale usava aritmetica su secondi grezzi (N×86400), vulnerabile al cambio dell'ora legale. Sostituita con aritmetica basata su `strtotime()`.

## [Meccanismo di aggiornamento] - non versionato (fix su pkg_messe.xml lato server)
- **Fix critico**: il file `pkg_messe.xml` mancava del tag `<client>site</client>`, causando lo scarto silenzioso degli aggiornamenti da parte di Joomla. Diagnosticato tramite CLI (`php cli/joomla.php update:extensions:check`).
- Aggiunto checksum `sha256` nel tag `<downloadurl>`.
- Vedi [`distribution/README.md`](distribution/README.md) per la checklist ad ogni release.

## [1.3.24] - com_messe / pkg_messe
- Nuova opzione per chiesa **"Sabato con Solennità"**: quando una solennità cade di sabato, determina se usare l'orario **Festivo** (default) oppure **Vigiliare**. Nuova colonna `messe_chiese.sabato_solennita`.

## [1.3.23] - pkg_messe
- Corretti gli URL dell'update server e del footer da `http` a `https`.
- Aggiunto footer con copyright e licenza in fondo alle view backend, con anno aggiornato automaticamente. *(com_messe 1.3.22)*

## [1.3.21] - com_messe / pkg_messe 1.3.22
- Cambiato il default di "Giorni anticipo prefestive" da 14 a 5.

## [1.3.20] - com_messe / pkg_messe 1.3.21
- Fix: form voce di menu aggiornato con i campi prefestiva mancanti.
- Fix: cartella lingue del pacchetto spostata da `admin/language` a `language`.

## [1.3.19] - com_messe
- Fix: i periodi stagionali ora determinano correttamente l'orario della messa prefestiva quando attivi. Opzione facoltativa per disattivare.

## [1.3.18] - com_messe
- Modalità "Ultima messa feriale serale": soglia oraria diventa fascia min-max, resa facoltativa.

## [1.3.17] - com_messe
- Fix: i periodi stagionali non influenzano più il calcolo della prefestiva (poi reso opzionale nella 1.3.19).

## [1.3.16] - com_messe
- Nuova opzione "Mostra il giorno della settimana nelle Messe Prefestive".

## [1.3.15] - com_messe
- Fix visualizzazione "Messe Prefestive": mostrato solo l'orario, non più l'elenco giorni della fascia sottostante.

## [1.3.14] - com_messe
- Modalità Messa Prefestiva configurabile per chiesa (4 modalità). Nuova colonna `messe_chiese.modalita_prefestiva`.

## [1.3.13] - com_messe
- Fix logica messa prefestiva: solo messe serali contano come prefestive (nuova opzione "Ora minima").

## [1.3.12] - com_messe
- Nuova opzione "Mantieni dati alla disinstallazione" (attiva di default).

## [1.3.11] - com_messe
- Frontend: visualizzazione dei giorni della settimana nel campo "giorni" di un orario.

## [1.3.10] - com_messe
- Fix errore `in_array()` sul frontend quando il campo "giorni" contiene un solo numero.

## [1.3.9] - com_messe
- Fix pulsante Salva al primo inserimento: model `save()` passa `$data` per riferimento.

## [1.3.8] - com_messe
- Fix etichette pulsanti toolbar form Chiesa.

## [1.3.7] - com_messe
- Toolbar form Chiesa: 4 pulsanti distinti — Salva / Salva e Nuovo / Salva e Chiudi / Chiudi.

## [1.3.6] - com_messe
- Fix installazione/disinstallazione tabelle DB, fix percorso SQL uninstall, aggiunto tag `<install>` nel manifest.

## [1.3.5] e precedenti
- Security hardening, riscrittura completa del backend form chiesa.

---

*Per la cronologia dettagliata di ogni singola versione, vedi i file in `com_messe/admin/sql/updates/mysql/`.*
