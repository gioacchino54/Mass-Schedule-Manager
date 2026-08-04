# Changelog

Tutte le modifiche rilevanti a `com_messe` e `mod_messe` sono documentate in questo file.

## [1.3.23] - pkg_messe
- Corretti gli URL dell'update server e del footer da `http` a `https`.

## [1.3.22] - com_messe
- Aggiunto un footer con copyright e licenza (GNU GPL v2 o successiva) in fondo alle view backend "Chiese" (elenco) e "Chiesa" (aggiungi/modifica), con anno aggiornato automaticamente.

## [1.3.21] - com_messe
- Cambiato il valore di default di "Giorni anticipo prefestive" da 14 a 5, per evitare che una prefestiva compaia nell'elenco molto più avanti nel tempo rispetto alle altre messe mostrate. Vincolo minimo del campo abbassato da 7 a 1.

## [1.3.20] - com_messe
- Fix: il form dei parametri della voce di menu "Orari Messe" era rimasto indietro e non includeva i campi relativi alla messa prefestiva aggiunti nelle versioni recenti. Ora configurabili anche per singola voce di menu.

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

*Per la cronologia dettagliata di ogni singola versione, vedi i file in `com_messe/admin/sql/updates/mysql/`.*
