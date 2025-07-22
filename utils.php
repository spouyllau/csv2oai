<?php
// utils.php

function load_records($filename = 'data.csv') {
    $records = [];
    if (!file_exists($filename)) return $records;

    $handle = fopen($filename, 'r');
    if (!$handle) return $records;

    $headers = fgetcsv($handle, 0, ';');
    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        $record = array_combine($headers, $row);
        if (!isset($record['identifier_oai']) || empty($record['identifier_oai'])) {
            //static $id = 0;
            //$record['identifier_oai'] = 'oai:exemple:' . (++$id);
            $record['identifier_oai'] = $record['identifier_oai'];
        }
        if (!isset($record['date']) || empty($record['date'])) {
            $record['date'] = date('Y-m-d');
        }
        if (!isset($record['set'])) {
            $record['set'] = '';
        }
        $records[] = $record;
    }

    fclose($handle);
    return $records;
}

function get_record_by_id($identifier, $records) {
    foreach ($records as $record) {
        if ($record['identifier_oai'] === $identifier) return $record;
    }
    return null;
}

function validate_date($date) {
    return preg_match('/^\\d{4}(-\\d{2})?(-\\d{2})?$/', $date);
}

function get_base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
}

function extract_sets($records) {
    $sets = [];
    foreach ($records as $r) {
        if (!empty($r['set']) && !in_array($r['set'], $sets)) {
            $sets[] = $r['set'];
        }
    }
    return $sets;
}
?>