-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.3.13 — Fix logica messa prefestiva: ora una messa feriale
-- viene considerata "prefestiva" (vigiliare) solo se è una messa serale,
-- cioè a partire dalla nuova opzione "Ora minima messa prefestiva"
-- (default 16 = dalle 16:00), configurabile nelle Opzioni del componente
-- e nei parametri del modulo. In precedenza veniva presa sempre l'ultima
-- messa feriale del giorno, anche se era una messa del mattino.
SELECT 1;
