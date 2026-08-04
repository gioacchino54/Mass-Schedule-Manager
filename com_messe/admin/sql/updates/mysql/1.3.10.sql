-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.3.10 — Fix errore "in_array(): Argument #2 must be of type
-- array, int given" sul frontend quando il campo "giorni" di un orario
-- contiene un solo numero. MesseHelper::parseGiorni() ora interpreta
-- correttamente il formato comma-separated salvato dal form admin
-- (es. "1,2,3,4,5"), con fallback JSON per compatibilità.
SELECT 1;
