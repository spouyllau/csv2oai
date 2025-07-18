<?php
// oai-pmh.php

require_once 'utils.php';

header('Content-Type: text/xml; charset=UTF-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

$records = load_records('data.csv');
$sets = extract_sets($records);

$verb = $_GET['verb'] ?? '';
$identifier = $_GET['identifier'] ?? '';
$metadataPrefix = $_GET['metadataPrefix'] ?? '';
$resumptionToken = $_GET['resumptionToken'] ?? null;
$batchSize = 10;
$baseURL = get_base_url();

function format_record($record) {
    $xml = "<record>\n";
    $xml .= "  <header>\n";
    $xml .= "    <identifier>" . htmlspecialchars($record['identifier']) . "</identifier>\n";
    $xml .= "    <datestamp>" . htmlspecialchars($record['date']) . "</datestamp>\n";
    if (!empty($record['set'])) {
        $xml .= "    <setSpec>" . htmlspecialchars($record['set']) . "</setSpec>\n";
    }
    $xml .= "  </header>\n";
    $xml .= "  <metadata>\n";
    $xml .= "    <oai_dc:dc xmlns:oai_dc=\"http://www.openarchives.org/OAI/2.0/oai_dc/\" 
             xmlns:dc=\"http://purl.org/dc/elements/1.1/\" 
             xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" 
             xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/oai_dc/ 
             http://www.openarchives.org/OAI/2.0/oai_dc.xsd\">\n";
    foreach ($record as $key => $value) {
        if (in_array($key, ['title','creator','subject','description','publisher','date','type','format','language','coverage','rights']) && !empty($value)) {
            $xml .= "      <dc:$key>" . htmlspecialchars($value) . "</dc:$key>\n";
        }
    }
    $xml .= "    </oai_dc:dc>\n";
    $xml .= "  </metadata>\n";
    $xml .= "</record>\n";
    return $xml;
}

function format_header($record) {
    $xml = "<header>\n";
    $xml .= "  <identifier>" . htmlspecialchars($record['identifier']) . "</identifier>\n";
    $xml .= "  <datestamp>" . htmlspecialchars($record['date']) . "</datestamp>\n";
    if (!empty($record['set'])) {
        $xml .= "  <setSpec>" . htmlspecialchars($record['set']) . "</setSpec>\n";
    }
    $xml .= "</header>\n";
    return $xml;
}

function list_sets($sets) {
    $xml = "<ListSets>\n";
    foreach ($sets as $set) {
        $xml .= "  <set>\n";
        $xml .= "    <setSpec>" . htmlspecialchars($set) . "</setSpec>\n";
        $xml .= "    <setName>" . htmlspecialchars(ucfirst($set)) . "</setName>\n";
        $xml .= "  </set>\n";
    }
    $xml .= "</ListSets>\n";
    return $xml;
}

$date = gmdate('Y-m-d\TH:i:s\Z');
echo "<OAI-PMH xmlns=\"http://www.openarchives.org/OAI/2.0/\">\n";
echo "  <responseDate>$date</responseDate>\n";
echo "  <request verb=\"$verb\">$baseURL</request>\n";

switch ($verb) {
    case 'Identify':
        echo "  <Identify>\n";
        echo "    <repositoryName>Mon Entrepot OAI</repositoryName>\n";
        echo "    <baseURL>$baseURL</baseURL>\n";
        echo "    <protocolVersion>2.0</protocolVersion>\n";
        echo "    <adminEmail>admin@example.org</adminEmail>\n";
        echo "    <earliestDatestamp>2000-01-01</earliestDatestamp>\n";
        echo "    <deletedRecord>no</deletedRecord>\n";
        echo "    <granularity>YYYY-MM-DD</granularity>\n";
        echo "  </Identify>\n";
        break;

    case 'ListMetadataFormats':
        echo "  <ListMetadataFormats>\n";
        echo "    <metadataFormat>\n";
        echo "      <metadataPrefix>oai_dc</metadataPrefix>\n";
        echo "      <schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>\n";
        echo "      <metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>\n";
        echo "    </metadataFormat>\n";
        echo "  </ListMetadataFormats>\n";
        break;
    
    case 'ListIdentifiers':
        echo "  <ListIdentifiers>\n";
        $start = $resumptionToken ? intval($resumptionToken) : 0;
        $setParam = $_GET['set'] ?? null;

        // Filtrage par set si un paramètre est passé
        $filteredRecords = $records;
            if ($setParam) {
            $filteredRecords = array_filter($records, function ($record) use ($setParam) {
                return isset($record['set']) && $record['set'] === $setParam;
            });
            $filteredRecords = array_values($filteredRecords); // réindexation après filtrage
            }

        $chunk = array_slice($filteredRecords, $start, $batchSize);

        foreach ($chunk as $record) {
            echo format_header($record);
        }

        if ($start + $batchSize < count($filteredRecords)) {
            echo "  <resumptionToken>" . ($start + $batchSize) . "</resumptionToken>\n";
        }
        echo "  </ListIdentifiers>\n";
        break;
    
    case 'ListRecords':
        echo "  <ListRecords>\n";

        $start = $resumptionToken ? intval($resumptionToken) : 0;
        $setParam = $_GET['set'] ?? null;

        // Filtrage par set si fourni
        $filteredRecords = $records;
        if ($setParam) {
            $filteredRecords = array_filter($records, function ($record) use ($setParam) {
                return isset($record['set']) && $record['set'] === $setParam;
            });
            $filteredRecords = array_values($filteredRecords); // Réindexation après filtrage
        }

        $chunk = array_slice($filteredRecords, $start, $batchSize);
        foreach ($chunk as $record) {
            echo format_record($record);
        }

        if ($start + $batchSize < count($filteredRecords)) {
            echo "  <resumptionToken>" . ($start + $batchSize) . "</resumptionToken>\n";
        }

    echo "  </ListRecords>\n";
    break;

    case 'GetRecord':
        $record = get_record_by_id($identifier, $records);
        if ($record) {
            echo "  <GetRecord>\n";
            echo format_record($record);
            echo "  </GetRecord>\n";
        }
        break;

    case 'ListSets':
        echo list_sets($sets);
        break;

    default:
        echo "  <error code=\"badVerb\">Verbe OAI inconnu ou non pris en charge</error>\n";
}

echo "</OAI-PMH>\n";
?>