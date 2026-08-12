<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Helper modulo — Accesso DB + Motore calcolo orari — Multilingua
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Module\Messe\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Carica la lingua del componente com_messe per le chiavi COM_MESSE_SOL_* e COM_MESSE_FESTA_*

class ModMesseHelper
{
    /**
     * Carica la lingua del componente com_messe.
     * Necessario perché il modulo non carica automaticamente i file .ini del componente.
     */
    public static function caricaLingua(): void
    {
        $lang = Factory::getApplication()->getLanguage();
        $lang->load('com_messe', JPATH_SITE);
    }

    public static function getChiesa(int $id): ?object
    {
        self::caricaLingua();
        $db = Factory::getDbo();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_chiese'))
            ->where($db->quoteName('id')        . ' = ' . $id)
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query);
        $chiesa = $db->loadObject();

        if (!$chiesa) return null;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_orari'))
            ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            ->order('tipo ASC, ordering ASC');
        $db->setQuery($query);
        $chiesa->orari = $db->loadObjectList();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_eccezioni'))
            ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            ->where($db->quoteName('published') . ' = 1')
            ->order('data_md ASC');
        $db->setQuery($query);
        $chiesa->eccezioni = $db->loadObjectList();

        // Periodi stagionali
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_periodi'))
            ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query);
        $chiesa->periodi = $db->loadObjectList();

        // Settimana Santa
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_settimana_santa'))
            ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query);
        $chiesa->settimanaSanta = $db->loadObjectList();

        return $chiesa;
    }

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
        // NB: strtotime("±N days", ...) invece di N*86400 secondi: sicuro
        // rispetto al cambio dell'ora legale (vedi MesseHelper in com_messe
        // per la spiegazione completa).
        $feste = [
            date('m-d', strtotime('-7 days', $pasqua))  => Text::_('COM_MESSE_FESTA_DOMENICA_PALME'),
            date('m-d', $pasqua)                        => Text::_('COM_MESSE_FESTA_PASQUA'),
            date('m-d', strtotime('+1 days', $pasqua))  => Text::_('COM_MESSE_FESTA_LUNEDI_ANGELO'),
            date('m-d', strtotime('+39 days', $pasqua)) => Text::_('COM_MESSE_FESTA_ASCENSIONE'),
            date('m-d', strtotime('+49 days', $pasqua)) => Text::_('COM_MESSE_FESTA_PENTECOSTE'),
            date('m-d', strtotime('+50 days', $pasqua)) => Text::_('COM_MESSE_FESTA_LUNEDI_PENTECOSTE'),
            date('m-d', strtotime('+60 days', $pasqua)) => Text::_('COM_MESSE_FESTA_CORPUS_DOMINI'),
            date('m-d', strtotime('+68 days', $pasqua)) => Text::_('COM_MESSE_FESTA_SACRO_CUORE'),
        ];

        if ($rito === 'ambrosiano') {
            $feste[date('m-d', strtotime('+7 days', $pasqua))] = Text::_('COM_MESSE_FESTA_DOMENICA_ALBIS');
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
            'feriale'     => Text::_('MOD_MESSE_GRUPPO_FERIALE'),
            'prefestivo'  => Text::_('MOD_MESSE_GRUPPO_PREFESTIVO'),
            'vigilia'     => Text::_('MOD_MESSE_GRUPPO_VIGILIA'),
            'festivo'     => Text::_('MOD_MESSE_GRUPPO_FESTIVO'),
            'particolare' => Text::_('MOD_MESSE_GRUPPO_PARTICOLARE'),
        ];
    }


    public static function getPeriodoAttivo(array $periodi, int $giorno, string $tipo): ?object
    {
        $mese = (int) date('n', $giorno);
        $md   = date('m-d', $giorno);

        $tipiOrarioValidi = ['feriale', 'vigilia', 'festivo', 'tutti'];

        foreach ($periodi as $p) {
            if (!in_array($p->tipo_orario ?? '', $tipiOrarioValidi)) continue;
            if ($p->tipo_orario !== 'tutti' && $p->tipo_orario !== $tipo) continue;

            $attivo = false;

            if ($p->tipo_data === 'mesi') {
                $mesiAttivi = json_decode($p->mesi ?? '[]', true);
                if (!is_array($mesiAttivi)) continue;
                $mesiAttivi = array_filter($mesiAttivi, fn($m) => is_int($m) && $m >= 1 && $m <= 12);
                $attivo = in_array($mese, $mesiAttivi);
            } elseif ($p->tipo_data === 'date') {
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
        $vigiliaKey     = date('m-d', strtotime('-1 days', $pasqua));

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
                'h'        => (int) $e->ora,
                'm'        => (int) $e->minuti,
                'label'    => $e->label,
                'luogo'    => $e->luogo ?? null,
                'modalita' => $e->modalita ?? 'sostituisci',
            ];
        }

        if (!isset($eccezioni[$vigiliaKey])) {
            $eccezioni[$vigiliaKey][] = [
                'h'        => (int) ($chiesa->ora_veglia    ?? 21),
                'm'        => (int) ($chiesa->minuti_veglia ?? 0),
                'label'    => Text::_('COM_MESSE_FESTA_VEGLIA_PASQUALE'),
                'luogo'    => null,
                'modalita' => 'sostituisci',
            ];
        }

        $offsetSettimanaSanta = [
            'palme'           => -7,
            'lunedi_santo'    => -6,
            'martedi_santo'   => -5,
            'mercoledi_santo' => -4,
            'giovedi_santo'   => -3,
            'venerdi_santo'   => -2,
            'sabato_santo'    => -1,
        ];
        $settimanaSantaByDate = [];
        foreach ($chiesa->settimanaSanta ?? [] as $s) {
            $offset = $offsetSettimanaSanta[$s->giorno_riferimento] ?? null;
            if ($offset === null) continue;
            $dataRito = date('Y-m-d', strtotime(($offset >= 0 ? '+' : '') . $offset . ' days', $pasqua));
            $settimanaSantaByDate[$dataRito][] = [
                'h'        => (int) $s->ora,
                'm'        => (int) $s->minuti,
                'label'    => $s->label,
                'luogo'    => $s->luogo ?? null,
                'modalita' => $s->modalita ?? 'aggiungi',
            ];
        }

        $elencoMesse = [];
        $prossima    = null;
        $labelPross  = null;
        $luogoPross  = null;

        for ($i = 0; $i <= $windowMax; $i++) {

            $giorno           = strtotime("+$i day", $now);
            $md               = date('m-d', $giorno);
            $ymd              = date('Y-m-d', $giorno);
            $w                = (int) date('w', $giorno);
            $nomeCelebrazione = null;

            $eventiSpeciali = [];
            if (isset($eccezioni[$md]))            $eventiSpeciali = array_merge($eventiSpeciali, $eccezioni[$md]);
            if (isset($settimanaSantaByDate[$ymd])) $eventiSpeciali = array_merge($eventiSpeciali, $settimanaSantaByDate[$ymd]);

            if (!empty($eventiSpeciali)) {
                if ($i <= $windowSpeciali) {
                    foreach ($eventiSpeciali as $ec) {
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
                $tutteAggiuntive = true;
                foreach ($eventiSpeciali as $ec) {
                    if (($ec['modalita'] ?? 'sostituisci') !== 'aggiungi') {
                        $tutteAggiuntive = false;
                        break;
                    }
                }
                if (!$tutteAggiuntive) {
                    continue;
                }

                $tipo = match (true) {
                    $w === 0 => 'festivo',
                    $w === 6 => 'vigilia',
                    default  => 'feriale',
                };
            } elseif (isset($solennitaFisse[$md]) || isset($festeMobili[$md])) {
                $nomeCelebrazione = $solennitaFisse[$md] ?? $festeMobili[$md];
                $tipo = ($w === 6 && $sabatoSolennita === 'vigiliare') ? 'vigilia' : 'festivo';
            } elseif ($w === 0) {
                $tipo = 'festivo';
            } elseif ($w === 6) {
                $tipo = 'vigilia';
            } else {
                $domani    = strtotime('+1 day', $giorno);
                $mdDomani  = date('m-d', $domani);
                $isDomaniF = isset($solennitaFisse[$mdDomani]) || isset($festeMobili[$mdDomani]);

                if ($isDomaniF && $rito === 'ambrosiano') {
                    $festeDomenicaAmbrosiano = [
                        date('m-d', strtotime('+49 days', $pasqua)),
                        date('m-d', strtotime('+39 days', $pasqua)),
                    ];
                    if (in_array($mdDomani, $festeDomenicaAmbrosiano)) {
                        $isDomaniF = false;
                    }
                }

                $tipo = $isDomaniF ? 'prefestivo' : 'feriale';
            }

            if ($tipo === 'prefestivo' && $i > $windowPrefestivo) continue;
            if (!in_array($tipo, ['prefestivo', 'particolare']) && $i > $windowStandard) continue;

            if ($tipo === 'prefestivo') {
                if ($modalitaPrefestiva === 'nessuna') {
                    continue;
                }
                $tipoOrari = match ($modalitaPrefestiva) {
                    'vigiliare' => 'vigilia',
                    'dedicato'  => 'prefestivo',
                    default     => 'feriale',
                };
            } else {
                $tipoOrari = $tipo;
            }

            // Periodi stagionali: determinano la fonte degli orari
            // candidati anche per il tipo "prefestivo" (vedi
            // MesseHelper::calcolaOrari in com_messe per la spiegazione
            // completa). Per i tipi normali il comportamento resta invariato.
            $periodi       = $chiesa->periodi ?? [];
            $periodoAttivo = !empty($periodi) ? self::getPeriodoAttivo($periodi, $giorno, $tipoOrari) : null;

            if ($periodoAttivo && $tipo !== 'prefestivo') {
                if ($periodoAttivo->azione === 'sopprimi') {
                    continue;
                } elseif ($periodoAttivo->azione === 'sostituisci' && !empty($periodoAttivo->orari_nuovi)) {
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
            // dipende dalla modalità configurata sulla chiesa (vedi
            // MesseHelper::calcolaOrari in com_messe per la spiegazione
            // completa).
            if ($tipo === 'prefestivo') {
                $orariDelGiorno = array_filter($orariDelGiorno, function($o) use ($w) {
                    return empty($o['giorni']) || in_array($w, $o['giorni']);
                });

                if ($modalitaPrefestiva === 'feriale_serale') {
                    if ($usaSogliaOrariaPrefestiva) {
                        $orariDelGiorno = array_filter($orariDelGiorno, function($o) use ($oraPrefestivo, $oraPrefestivoMax) {
                            return (int) $o['h'] >= $oraPrefestivo && (int) $o['h'] <= $oraPrefestivoMax;
                        });
                    }
                    usort($orariDelGiorno, fn($a, $b) =>
                        ($b['h'] * 60 + $b['m']) <=> ($a['h'] * 60 + $a['m'])
                    );
                    $orariDelGiorno = array_slice($orariDelGiorno, 0, 1);
                }
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

        $prossima = null; $labelPross = null; $luogoPross = null;
        foreach ($elencoMesse as $m) {
            if ($m['ts'] > $now) {
                $prossima   = $m['ts'];
                $labelPross = $m['label'] ?? null;
                $luogoPross = $m['luogo'] ?? null;
                break;
            }
        }

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

            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $giorni = $decoded;
            } else {
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
