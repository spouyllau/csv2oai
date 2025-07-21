<?php
// save.php : Sauvegarde du contenu CSV édité en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csv = $_POST['csv'] ?? '';
    if ($csv !== '') {
        file_put_contents('data.csv', $csv);
        http_response_code(200);
    } else {
        http_response_code(400);
        echo "Aucune donnée à enregistrer.";
    }
    exit;
}
?>