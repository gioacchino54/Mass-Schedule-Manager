-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.3.12 — Nuova opzione componente "Mantieni dati alla
-- disinstallazione" (attiva di default): disinstallando com_messe le
-- tabelle (chiese, orari, eccezioni, periodi) non vengono più eliminate
-- automaticamente, per non perdere la configurazione durante un
-- aggiornamento/reinstallazione. Rimosso il DROP incondizionato dal
-- manifest; la decisione ora passa da script.php in base all'opzione.
SELECT 1;
