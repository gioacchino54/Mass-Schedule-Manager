<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Helper — Motore calcolo liturgico e orari
 * Compatibile con Joomla 5 / 6 — Multilingua
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

class MesseHelper
{
    public static function calcolaPasqua(int $Y): int
    {
        $a = $Y % 19;
        $b = (int) floor($Y / 100);
        $c = $Y % 100;
        $d = (int) floor($b / 4);
        $e = $b % 4;
        $f = (int) floor(($b + 8) / 25);
        $g = (int) floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = (int) floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = (int) floor(($a + 11 * $h + 22 * $l) / 451);

        $month = (int) floor(($h + $l - 7 * $m + 114) / 31);
        $day   = (($h + $l - 7 * $m + 114) % 31) + 1;

        return mktime(0, 0, 0, $month, $day, $Y);
    }

    public static function getSolennitaFisse(string $rito = 'romano'): array
    {
        $comuni = [
            '01-01' => Text::_('COM_MESSE_SOL_01_01'),
            '01-06' => Text::_('COM_MESSE_SOL_01_06'),
            '08-15' => Text::_('COM_MESSE_SOL_08_15'),
            '11-01' => Text::_('COM_MESSE_SOL_11_01'),
            '12-08' => Text::_('COM_MESSE_SOL_12_08'),
            '12-25' => Text::_('COM_MESSE_SOL_12_25'),
            '12-26' => Text::_('COM_MESSE_SOL_12_26'),
        ];

        if ($rito === 'ambrosiano') {
            return array_merge($comuni, [
                '12-07' => Text::_('COM_MESSE_SOL_12_07'),
            ]);
        }

        return array_merge($comuni, [
            '06-24' => Text::_('COM_MESSE_SOL_06_24'),
            '06-29' => Text::_('COM_MESSE_SOL_06_29'),
        ]);
    }

    public static function getFesteMobili(int $pasqua, string $rito = 'romano'): array
    {
        $feste = [
            date('m-d', $pasqua)                  => Text::_('COM_MESSE_FESTA_PASQUA'),
            date('m-d', $pasqua + 1  * 86400)      => Text::_('COM_MESSE_FESTA_LUNEDI_ANGELO'),
            date('m-d', $pasqua + 39 * 86400)      => Text::_('COM_MESSE_FESTA_ASCENSIONE'),
            date('m-d', $pasqua + 49 * 86400)      => Text::_('COM_MESSE_FESTA_PENTECOSTE'),
            date('m-d', $pasqua + 50 * 86400)      => Text::_('COM_MESSE_FESTA_LUNEDI_PENTECOSTE'),
            date('m-d', $pasqua + 60 * 86400)      => Text::_('COM_MESSE_FESTA_CORPUS_DOMINI'),
            date('m-d', $pasqua + 68 * 86400)      => Text::_('COM_MESSE_FESTA_SACRO_CUORE'),
        ];

        if ($rito === 'ambrosiano') {
            $feste[date('m-d', $pasqua + 7 * 86400)] = Text::_('COM_MESSE_FESTA_DOMENICA_ALBIS');
        }

        return $feste;
    }

    public static function getGiorni(): array
    {
        return [
            0 => Text::_('COM_MESSE_GIORNO_0'),
            1 => Text::_('COM_MESSE_GIORNO_1'),
            2 => Text::_('COM_MESSE_GIORNO_2'),
            3 => Text::_('COM_MESSE_GIORNO_3'),
            4 => Text::_('COM_MESSE_GIORNO_4'),
            5 => Text::_('COM_MESSE_GIORNO_5'),
            6 => Text::_('COM_MESSE_GIORNO_6'),
        ];
    }

    public static function getGruppi(): array
    {
        return [
            'feriale'     => Text::_('COM_MESSE_GRUPPO_FERIALE'),
            'prefestivo'  => Text::_('COM_MESSE_GRUPPO_PREFESTIVO'),
            'vigilia'     => Text::_('COM_MESSE_GRUPPO_VIGILIA'),
            'festivo'     => Text::_('COM_MESSE_GRUPPO_FESTIVO'),
            'particolare' => Text::_('COM_MESSE_GRUPPO_PARTICOLARE'),
        ];
    }


    /**
     * Verifica se una data ricade in un periodo stagionale attivo
     * e restituisce il periodo applicabile (o null)
     */
    public static function getPeriodoAttivo(array $periodi, int $giorno, string $tipo): ?object
    {
        $mese = (int) date('n', $giorno);
        $md   = date('m-d', $giorno);

        // Whitelist tipi validi
        $tipiOrarioValidi = ['feriale', 'vigilia', 'festivo', 'tutti'];

        foreach ($periodi as $p) {
            // Valida tipo_orario
            if (!in_array($p->tipo_orario ?? '', $tipiOrarioValidi)) continue;

            // Filtro per tipo orario
            if ($p->tipo_orario !== 'tutti' && $p->tipo_orario !== $tipo) continue;

            $attivo = false;

            if ($p->tipo_data === 'mesi') {
                $mesiAttivi = json_decode($p->mesi ?? '[]', true);
                // Valida che siano interi 1-12
                if (!is_array($mesiAttivi)) continue;
                $mesiAttivi = array_filter($mesiAttivi, fn($m) => is_int($m) && $m >= 1 && $m <= 12);
                $attivo = in_array($mese, $mesiAttivi);
            } elseif ($p->tipo_data === 'date') {
                // Valida formato MM-GG prima del confronto
                $inizio = $p->data_inizio ?? '';
                $fine   = $p->data_fine   ?? '';
                if (!preg_match('/^\d{2}-\d{2}$/', $inizio)) continue;
                if (!preg_match('/^\d{2}-\d{2}$/', $fine))   continue;
                $attivo = ($md >= $inizio && $md <= $fine);
            }

            if ($attivo) return $p;
        }

        return null;
    }

    public static function calcolaOrari(object $chiesa, int $now, array $params = []): array
    {
        date_default_timezone_set('Europe/Rome');

        $rito             = $chiesa->rito ?? 'romano';
        $Y                = (int) date('Y', $now);
        $windowStandard   = (int) ($params['days_window']     ?? 7);
        $windowPrefestivo = (int) ($params['days_prefestivo'] ?? 14);
        $windowSpeciali   = (int) ($params['days_speciali']   ?? 15);
        $oraPrefestivo    = (int) ($params['ora_prefestivo']  ?? 16);
        $oraPrefestivoMax = (int) ($params['ora_prefestivo_max'] ?? 20);
        $usaSogliaOrariaPrefestiva = !array_key_exists('usa_soglia_oraria_prefestiva', $params)
                                        || !empty($params['usa_soglia_oraria_prefestiva']);
        $mostraGiornoPrefestivo = !empty($params['mostra_giorno_prefestivo']);
        $periodiInfluenzanoPrefestiva = !array_key_exists('periodi_influenzano_prefestiva', $params)
                                            || !empty($params['periodi_influenzano_prefestiva']);
        $windowMax        = max($windowStandard, $windowPrefestivo, $windowSpeciali);

        $pasqua         = self::calcolaPasqua($Y);
        $solennitaFisse = self::getSolennitaFisse($rito);
        $festeMobili    = self::getFesteMobili($pasqua, $rito);
        $giorniNomi     = self::getGiorni();
        $vigiliaKey     = date('m-d', $pasqua - 86400);

        $modalitaPrefestiva = $chiesa->modalita_prefestiva ?? 'feriale_serale';
        $sabatoSolennita    = $chiesa->sabato_solennita ?? 'festivo';

        $orari = ['feriale' => [], 'vigilia' => [], 'festivo' => [], 'prefestivo' => []];
        foreach ($chiesa->orari as $o) {
            $giorni = self::parseGiorni($o->giorni ?? null);
            $orari[$o->tipo][] = [
                'h'      => (int) $o->ora,
                'm'      => (int) $o->minuti,
                'label'  => $o->label ?? null,
                'giorni' => $giorni,
            ];
        }

        $eccezioni = [];
        foreach ($chiesa->eccezioni as $e) {
            $eccezioni[$e->data_md][] = [
                'h'     => (int) $e->ora,
                'm'     => (int) $e->minuti,
                'label' => $e->label,
                'luogo' => $e->luogo ?? null,
            ];
        }

        if (!isset($eccezioni[$vigiliaKey])) {
            $eccezioni[$vigiliaKey][] = [
                'h'     => (int) ($chiesa->ora_veglia    ?? 21),
                'm'     => (int) ($chiesa->minuti_veglia ?? 0),
                'label' => Text::_('COM_MESSE_FESTA_VEGLIA_PASQUALE'),
                'luogo' => null,
            ];
        }

        $elencoMesse = [];
        $prossima    = null;
        $labelPross  = null;
        $luogoPross  = null;

        for ($i = 0; $i <= $windowMax; $i++) {

            $giorno           = strtotime("+$i day", $now);
            $md               = date('m-d', $giorno);
            $w                = (int) date('w', $giorno);
            $nomeCelebrazione = null;

            if (isset($eccezioni[$md])) {
                if ($i <= $windowSpeciali) {
                    foreach ($eccezioni[$md] as $ec) {
                        $ts = mktime((int)$ec['h'], (int)$ec['m'], 0,
                            (int) date('m', $giorno),
                            (int) date('d', $giorno),
                            (int) date('Y', $giorno)
                        );
                        $elencoMesse[] = [
                            'ts'           => $ts,
                            'tipo'         => 'particolare',
                            'nome'         => $ec['label'] ?? '',
                            'label'        => $ec['label'] ?? null,
                            'luogo'        => $ec['luogo'] ?? null,
                            'giorno_label' => $giorniNomi[$w],
                        ];
                        if ($ts > $now && $prossima === null) {
                            $prossima   = $ts;
                            $labelPross = $ec['label'] ?? null;
                            $luogoPross = $ec['luogo'] ?? null;
                        }
                    }
                }
                continue;
            }

            if (isset($solennitaFisse[$md]) || isset($festeMobili[$md])) {
                $nomeCelebrazione = $solennitaFisse[$md] ?? $festeMobili[$md];
                // Se la solennità cade proprio di sabato, l'orario da usare
                // per quella sera dipende dall'opzione configurata sulla
                // chiesa: "festivo" (default) tratta l'intera giornata come
                // la solennità stessa; "vigiliare" la tratta comunque come
                // anticipo della domenica successiva.
                $tipo = ($w === 6 && $sabatoSolennita === 'vigiliare') ? 'vigilia' : 'festivo';
            } elseif ($w === 0) {
                $tipo = 'festivo';
            } elseif ($w === 6) {
                $tipo = 'vigilia';
            } else {
                $domani    = strtotime('+1 day', $giorno);
                $mdDomani  = date('m-d', $domani);
                $isDomaniF = isset($solennitaFisse[$mdDomani]) || isset($festeMobili[$mdDomani]);

                // Rito Ambrosiano: Ascensione e Pentecoste cadono sempre di domenica
                // quindi NON hanno giorno prefestivo — rimane vigiliare normale
                if ($isDomaniF && $rito === 'ambrosiano') {
                    $festeDomenicaAmbrosiano = [
                        date('m-d', $pasqua + 49 * 86400), // Pentecoste
                        date('m-d', $pasqua + 39 * 86400), // Ascensione (domenica nel rito ambrosiano)
                    ];
                    if (in_array($mdDomani, $festeDomenicaAmbrosiano)) {
                        $isDomaniF = false;
                    }
                }

                $tipo = $isDomaniF ? 'prefestivo' : 'feriale';
            }

            if ($tipo === 'prefestivo' && $i > $windowPrefestivo) continue;
            if (!in_array($tipo, ['prefestivo', 'particolare']) && $i > $windowStandard) continue;

            // Determina quale gruppo di orari usare per il giorno prefestivo
            // in base alla modalità configurata per la chiesa.
            if ($tipo === 'prefestivo') {
                if ($modalitaPrefestiva === 'nessuna') {
                    continue; // questa chiesa non ha messa prefestiva
                }
                $tipoOrari = match ($modalitaPrefestiva) {
                    'vigiliare' => 'vigilia',
                    'dedicato'  => 'prefestivo',
                    default     => 'feriale', // 'feriale_serale' (o valore non riconosciuto)
                };
            } else {
                $tipoOrari = $tipo;
            }

            // Periodi stagionali: determinano quali orari esistono
            // realmente quel giorno per il tipo di base (feriale/vigilia/
            // ecc.). Per il tipo "prefestivo" questo viene usato solo come
            // FONTE degli orari candidati: la selezione vera e propria
            // (fascia oraria + ultima messa del giorno, per la modalità
            // "feriale_serale") viene comunque applicata sopra, così un
            // "Orario Estivo" non genera più una prefestiva sbagliata (es.
            // un orario del mattino), ma nemmeno viene più ignorato del
            // tutto: se durante l'estate l'unica messa feriale è alle 8:30,
            // e rientra nella fascia configurata, verrà mostrata quella;
            // altrimenti (fuori fascia) quel giorno non avrà prefestiva.
            $periodi       = $chiesa->periodi ?? [];
            $periodoAttivo = !empty($periodi) ? self::getPeriodoAttivo($periodi, $giorno, $tipoOrari) : null;

            if ($periodoAttivo && $tipo !== 'prefestivo') {
                if ($periodoAttivo->azione === 'sopprimi') {
                    // Sopprime tutti gli orari del tipo per questo periodo
                    continue;
                } elseif ($periodoAttivo->azione === 'sostituisci' && !empty($periodoAttivo->orari_nuovi)) {
                    // Sostituisce con orari alternativi
                    $orariAlternativi = json_decode($periodoAttivo->orari_nuovi, true) ?? [];
                    foreach ($orariAlternativi as $oa) {
                        if (!empty($oa['giorni']) && !in_array($w, $oa['giorni'])) continue;
                        $ts = mktime((int)$oa['h'], (int)$oa['m'], 0,
                            (int) date('m', $giorno),
                            (int) date('d', $giorno),
                            (int) date('Y', $giorno)
                        );
                        $elencoMesse[] = [
                            'ts'           => $ts,
                            'tipo'         => $tipo,
                            'nome'         => $nomeCelebrazione,
                            'label'        => ($oa['label'] ?? null) . ' (' . $periodoAttivo->nome . ')',
                            'luogo'        => null,
                            'giorno_label' => $giorniNomi[$w],
                        ];
                        if ($ts > $now && $prossima === null) {
                            $prossima   = $ts;
                            $labelPross = $oa['label'] ?? null;
                            $luogoPross = null;
                        }
                    }
                    continue;
                }
            }

            // Determina la fonte degli orari candidati per il giorno:
            // il periodo stagionale attivo (se presente) sostituisce la
            // fonte, altrimenti si usa l'orario configurato normalmente.
            if ($tipo === 'prefestivo' && $periodoAttivo && $periodiInfluenzanoPrefestiva) {
                if ($periodoAttivo->azione === 'sopprimi') {
                    $orariDelGiorno = [];
                } elseif ($periodoAttivo->azione === 'sostituisci' && !empty($periodoAttivo->orari_nuovi)) {
                    $orariAlternativi = json_decode($periodoAttivo->orari_nuovi, true) ?? [];
                    $orariDelGiorno = array_map(function($oa) use ($periodoAttivo) {
                        return [
                            'h'      => (int) ($oa['h'] ?? 0),
                            'm'      => (int) ($oa['m'] ?? 0),
                            'label'  => trim(($oa['label'] ?? '') . ' (' . $periodoAttivo->nome . ')'),
                            'giorni' => $oa['giorni'] ?? null,
                        ];
                    }, $orariAlternativi);
                } else {
                    $orariDelGiorno = $orari[$tipoOrari] ?? [];
                }
            } else {
                if (empty($orari[$tipoOrari])) continue;
                $orariDelGiorno = $orari[$tipoOrari];
            }

            // Per il prefestivo, il modo in cui vengono scelti gli orari
            // dipende dalla modalità configurata sulla chiesa:
            // - 'vigiliare'/'dedicato': si usano TUTTI gli orari della fonte
            //   determinata sopra, filtrati solo per giorno della settimana;
            // - 'feriale_serale' (comportamento storico): tra gli orari
            //   della fonte si prende solo l'ultima messa del giorno; se la
            //   fascia oraria è attiva (opzione facoltativa), quella messa
            //   deve inoltre rientrare nell'intervallo configurato (es.
            //   16:00-20:00), altrimenti quel giorno non ha prefestiva.
            if ($tipo === 'prefestivo') {
                // Filtra sempre per giorni applicabili
                $orariDelGiorno = array_filter($orariDelGiorno, function($o) use ($w) {
                    return empty($o['giorni']) || in_array($w, $o['giorni']);
                });

                if ($modalitaPrefestiva === 'feriale_serale') {
                    if ($usaSogliaOrariaPrefestiva) {
                        // Solo messe comprese nella fascia oraria (es. 16:00-20:00)
                        $orariDelGiorno = array_filter($orariDelGiorno, function($o) use ($oraPrefestivo, $oraPrefestivoMax) {
                            return (int) $o['h'] >= $oraPrefestivo && (int) $o['h'] <= $oraPrefestivoMax;
                        });
                    }
                    // Ordina per ora decrescente e prende solo il primo (l'ultimo della giornata)
                    usort($orariDelGiorno, fn($a, $b) =>
                        ($b['h'] * 60 + $b['m']) <=> ($a['h'] * 60 + $a['m'])
                    );
                    $orariDelGiorno = array_slice($orariDelGiorno, 0, 1);
                }
                // Per 'vigiliare' e 'dedicato' si mostrano tutti gli orari
                // della fonte determinata sopra, nessun'altra restrizione.
            }

            foreach ($orariDelGiorno as $o) {
                if ($tipo !== 'prefestivo' && !empty($o['giorni']) && !in_array($w, $o['giorni'])) continue;

                $ts = mktime((int)$o['h'], (int)$o['m'], 0,
                    (int) date('m', $giorno),
                    (int) date('d', $giorno),
                    (int) date('Y', $giorno)
                );

                $elencoMesse[] = [
                    'ts'           => $ts,
                    'tipo'         => $tipo,
                    'nome'         => $nomeCelebrazione,
                    'label'        => $o['label'] ?? null,
                    'luogo'        => null,
                    'giorno_label' => $giorniNomi[$w],
                    // Per il prefestivo non si mostra normalmente l'elenco
                    // giorni della ricorrenza sottostante (es. "Lun-Ven" del
                    // feriale), essendo un'occorrenza singola legata a una
                    // data specifica. Se l'opzione è attiva, si mostra invece
                    // solo il giorno della settimana di QUESTA occorrenza.
                    'giorni'       => ($tipo === 'prefestivo')
                                        ? ($mostraGiornoPrefestivo ? [$w] : null)
                                        : ($o['giorni'] ?? null),
                ];

                if ($ts > $now && $prossima === null) {
                    $prossima   = $ts;
                    $labelPross = $o['label'] ?? null;
                    $luogoPross = null;
                }
            }
        }

        usort($elencoMesse, fn($a, $b) => $a['ts'] <=> $b['ts']);

        return [
            'prossima' => $prossima ? [
                'ts'    => $prossima,
                'label' => $labelPross,
                'luogo' => $luogoPross,
            ] : null,
            'elenco'  => $elencoMesse,
            'giorni'  => $giorniNomi,
        ];
    }

    /**
     * Interpreta il campo "giorni" salvato dal form admin come lista di
     * numeri separati da virgola (es. "1,2,3,4,5"), con fallback a JSON
     * per eventuali valori salvati in quel formato. Restituisce un array
     * di interi 0-6 (0=domenica) o null se vuoto/non valido.
     */
    private static function parseGiorni($raw): ?array
    {
        if (empty($raw)) {
            return null;
        }

        if (is_array($raw)) {
            $giorni = $raw;
        } else {
            $raw = trim((string) $raw);

            // Prova prima come JSON array (eventuali valori salvati in quel formato)
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $giorni = $decoded;
            } else {
                // Formato standard del form admin: lista separata da virgole
                $giorni = explode(',', $raw);
            }
        }

        $giorni = array_values(array_unique(array_map(
            'intval',
            array_filter($giorni, fn($v) => $v !== '' && $v !== null)
        )));

        return !empty($giorni) ? $giorni : null;
    }
}
