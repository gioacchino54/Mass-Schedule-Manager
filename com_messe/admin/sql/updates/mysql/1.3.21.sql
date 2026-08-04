-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.3.21 — Cambiato il valore di default di "Giorni anticipo
-- prefestive" da 14 a 5, per evitare che una prefestiva compaia
-- nell'elenco molto più avanti nel tempo rispetto alle altre messe
-- mostrate (disallineamento con "days_window", default 7). Il vincolo
-- minimo del campo è stato abbassato da 7 a 1 per permettere questo
-- valore. NOTA: questo è solo il nuovo default per installazioni/campi
-- non ancora salvati — se avevi già configurato 14 nelle Opzioni,
-- resta 14 finché non lo cambi manualmente.
SELECT 1;
