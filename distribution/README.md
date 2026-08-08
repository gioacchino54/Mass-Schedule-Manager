# Cartella distribution/

Questa cartella conserva una **copia di riferimento** del file `pkg_messe.xml` (il descrittore letto da Joomla per il controllo aggiornamenti), così com'è effettivamente pubblicato su:

```
https://gioacchinocipriano.it/updates/pkg_messe.xml
```

**Non viene letta da Joomla da qui** — è solo una copia di backup/versionamento nel repository, utile come modello di partenza per la prossima versione.

## Checklist per generare il file per una nuova versione

Quando rilasci una nuova versione (es. 1.3.24), copia questo file, poi:

1. Aggiorna `<version>` con il nuovo numero
2. Aggiorna il nome del file in `<downloadurl>` (es. `pkg_messe_v1.3.24.zip`)
3. **Ricalcola il checksum SHA-256** del nuovo zip:
   ```
   sha256sum pkg_messe_v1.3.24.zip
   ```
   e aggiorna l'attributo `sha256="..."` nel tag `<downloadurl>` — **è specifico di ogni singolo file**, cambia ad ogni release anche a parità di versione dichiarata.
4. **Non rimuovere mai** il tag `<client>site</client>` — senza, Joomla scarta silenziosamente l'aggiornamento (nessun errore visibile, semplicemente non viene trovato). Vedi la voce nel [CHANGELOG](../CHANGELOG.md) per i dettagli di questo bug.
5. Carica il file risultante su `https://gioacchinocipriano.it/updates/pkg_messe.xml`, **sostituendo** quello esistente (il nome file sul server deve essere esattamente `pkg_messe.xml`, non `update.xml`).
6. Carica anche il nuovo pacchetto zip a `https://gioacchinocipriano.it/updates/pkg_messe_vX.X.X.zip`.
