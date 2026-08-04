-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.3.14 — Modalità Messa Prefestiva configurabile per chiesa:
-- nuova colonna messe_chiese.modalita_prefestiva (nessuna / vigiliare /
-- dedicato / feriale_serale), nuovo tipo 'prefestivo' nell'ENUM
-- messe_orari.tipo per orari dedicati. La migrazione dello schema è
-- gestita in modo idempotente da script.php::postflight().
SELECT 1;
