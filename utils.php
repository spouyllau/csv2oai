<?php
function load_records($filename = 'data.csv') {
    $records = [];

    if (!file_exists($filename)) {
        return $records;
    }

    $fp = fopen($filename, 'r');
    if (!$fp) return $records;

    // Supprime BOM UTF-8 éventuel
    $firstLine = fgets($fp);
    $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
    $headers = str_getcsv(trim($firstLine), ';');

    // Nettoyage des entêtes
    $headers = array_map(function ($h) {
        return strtolower(trim($h));
    }, $headers);

    while (($row = fgetcsv($fp, 0, ';')) !== false) {
        // Ignorer lignes vides ou incomplètes
        if (count($row) < 2) continue;

        $record = [];
        foreach ($headers as $i => $key) {
            $record[$key] = isset($row[$i]) ? trim($row[$i]) : '';
        }

        // Valeurs par défaut
        $record['identifier'] = $record['identifier'] ?: uniqid('oai:generated:', true);
        $record['date'] = validate_date($record['date']) ?: '2023-01-01';

        $records[] = $record;
    }

    fclose($fp);
    return $records;
}

function get_record_by_id($identifier, $records) {
    foreach ($records as $rec) {
        if ($rec['identifier'] === $identifier) {
            return $rec;
        }
    }
    return null;
}

function validate_date($date) {
    // Accepte YYYY-MM-DD, YYYY-MM, YYYY
    if (preg_match('/^\d{4}(-\d{2}){0,2}$/', $date)) {
        return $date;
    }
    return null;
}
?>