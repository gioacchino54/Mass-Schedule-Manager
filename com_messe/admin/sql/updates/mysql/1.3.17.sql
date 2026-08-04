-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.3.17 — Fix: i periodi stagionali (es. "Orario Estivo" con
-- azione sopprimi/sostituisci) non vengono più applicati al calcolo
-- della messa prefestiva, che ora si basa sempre sull'orario configurato
-- "normale" (feriale/vigiliare/dedicato a seconda della modalità scelta
-- per la chiesa), indipendentemente da eventuali periodi attivi quel
-- giorno.
SELECT 1;
