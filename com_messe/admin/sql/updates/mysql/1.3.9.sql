-- Gestione Orari Messe by Gioacchino Cipriano
-- Update 1.3.9 — Fix pulsante Salva al primo inserimento: il model save()
-- ora passa $data per riferimento e scrive l'id direttamente in $data['id'],
-- evitando che populateState() (che legge l'id dalla request, sempre 0 su
-- un nuovo inserimento) sovrascriva l'id appena creato letto tramite getState()
SELECT 1;
