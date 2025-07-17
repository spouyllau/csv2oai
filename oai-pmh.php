<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/xml; charset=utf-8');
require 'utils.php';

$verb = $_GET['verb'] ?? '';
$identifier = $_GET['identifier'] ?? '';
$metadataPrefix = $_GET['metadataPrefix'] ?? 'oai_dc';
$resumptionToken = isset($_GET['resumptionToken']) ? intval($_GET['resumptionToken']) : 0;

$records = load_records();
$batchSize = 10;

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<OAI-PMH xmlns=\"http://www.openarchives.org/OAI/2.0/\">\n";
echo "  <responseDate>" . gmdate('Y-m-d\TH:i:s\Z') . "</responseDate>\n";
echo "  <request verb=\"$verb\">" . htmlspecialchars($_SERVER['REQUEST_URI']) . "</request>\n";

switch ($verb) {
  case 'Identify':
    echo "  <Identify>
    <repositoryName>Serveur OAI-PMH pour fichier CSV</repositoryName>
    <baseURL>" . htmlspecialchars("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) . "</baseURL>
    <protocolVersion>2.0</protocolVersion>
    <adminEmail>stephane.pouyllau@gmail.com</adminEmail>
    <earliestDatestamp>1900-01-01T00:00:00Z</earliestDatestamp>
    <deletedRecord>no</deletedRecord>
    <granularity>YYYY-MM-DD</granularity>
  </Identify>\n";
    break;

  case 'ListMetadataFormats':
    echo "  <ListMetadataFormats>
      <metadataFormat>
        <metadataPrefix>oai_dc</metadataPrefix>
        <schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>
        <metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>
      </metadataFormat>
    </ListMetadataFormats>\n";
    break;

  case 'ListIdentifiers':
    echo "  <ListIdentifiers>\n";
    for ($i = $resumptionToken; $i < min($resumptionToken + $batchSize, count($records)); $i++) {
      $rec = $records[$i];
      echo "    <header>
        <identifier>{$rec['identifier']}</identifier>
        <datestamp>{$rec['date']}</datestamp>
      </header>\n";
    }
    if ($i < count($records)) {
      echo "    <resumptionToken>" . ($resumptionToken + $batchSize) . "</resumptionToken>\n";
    }
    echo "  </ListIdentifiers>\n";
    break;

  case 'ListRecords':
    echo "  <ListRecords>\n";
    for ($i = $resumptionToken; $i < min($resumptionToken + $batchSize, count($records)); $i++) {
      $rec = $records[$i];
      echo "    <record>
        <header>
          <identifier>{$rec['identifier']}</identifier>
          <datestamp>{$rec['date']}</datestamp>
        </header>
        <metadata>
          <oai_dc:dc xmlns:oai_dc=\"http://www.openarchives.org/OAI/2.0/oai_dc/\"
                     xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
                     xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
                     xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/oai_dc/
                     http://www.openarchives.org/OAI/2.0/oai_dc.xsd\">
            <dc:identifier>{$rec['url']}</dc:identifier>
            <dc:title>{$rec['title']}</dc:title>
            <dc:creator>{$rec['creator']}</dc:creator>
            <dc:subject>{$rec['subject']}</dc:subject>
            <dc:description>{$rec['description']}</dc:description>
            <dc:publisher>{$rec['publisher']}</dc:publisher>
            <dc:date>{$rec['date']}</dc:date>
            <dc:type>{$rec['type']}</dc:type>
            <dc:format>{$rec['format']}</dc:format>
            <dc:language>{$rec['language']}</dc:language>
            <dc:coverage>{$rec['coverage']}</dc:coverage>
            <dc:rights>{$rec['rights']}</dc:rights>
            <dc:relation>{$rec['relation']}</dc:relation>
          </oai_dc:dc>
        </metadata>
      </record>\n";
    }
    if ($i < count($records)) {
      echo "    <resumptionToken>" . ($resumptionToken + $batchSize) . "</resumptionToken>\n";
    }
    echo "  </ListRecords>\n";
    break;

  case 'GetRecord':
    $rec = get_record_by_id($identifier, $records);
    if ($rec) {
      echo "  <GetRecord>
      <record>
        <header>
          <identifier>{$rec['identifier']}</identifier>
          <datestamp>{$rec['date']}</datestamp>
        </header>
        <metadata>
          <oai_dc:dc xmlns:oai_dc=\"http://www.openarchives.org/OAI/2.0/oai_dc/\"
                     xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
                     xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
                     xsi:schemaLocation=\"http://www.openarchives.org/OAI/2.0/oai_dc/
                     http://www.openarchives.org/OAI/2.0/oai_dc.xsd\">
            <dc:identifier>{$rec['url']}</dc:identifier>
            <dc:title>{$rec['title']}</dc:title>
            <dc:creator>{$rec['creator']}</dc:creator>
            <dc:subject>{$rec['subject']}</dc:subject>
            <dc:description>{$rec['description']}</dc:description>
            <dc:publisher>{$rec['publisher']}</dc:publisher>
            <dc:date>{$rec['date']}</dc:date>
            <dc:type>{$rec['type']}</dc:type>
            <dc:format>{$rec['format']}</dc:format>
            <dc:language>{$rec['language']}</dc:language>
            <dc:coverage>{$rec['coverage']}</dc:coverage>
            <dc:rights>{$rec['rights']}</dc:rights>
            <dc:relation>{$rec['relation']}</dc:relation>
          </oai_dc:dc>
        </metadata>
      </record>
    </GetRecord>\n";
    } else {
      echo "<error code='idDoesNotExist'>No record found</error>\n";
    }
    break;

  default:
    echo "<error code='badVerb'>Unknown or missing verb</error>\n";
}

echo "</OAI-PMH>\n";
?>
