-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.3.19 — Fix: quando un periodo stagionale (es. "Orario Estivo")
-- è attivo, la messa prefestiva ora si basa sull'orario stagionale
-- realmente in vigore quel giorno (con la stessa selezione fascia
-- oraria/ultima messa), non più su quello normale. Aggiunta opzione
-- facoltativa "Applica i periodi stagionali alla messa prefestiva"
-- (attiva di default) per chi preferisce il comportamento precedente
-- (prefestiva sempre basata sull'orario normale, periodi ignorati).
SELECT 1;
