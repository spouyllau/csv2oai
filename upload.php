<?php
// upload.php : Traitement du fichier CSV téléversé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvfile'])) {
    $upload = $_FILES['csvfile'];
    if ($upload['error'] === UPLOAD_ERR_OK && pathinfo($upload['name'], PATHINFO_EXTENSION) === 'csv') {
        move_uploaded_file($upload['tmp_name'], 'data.csv');
        echo "Fichier CSV remplacé avec succès.";
    } else {
        echo "Erreur lors de l'envoi du fichier.";
    }
    exit;
}
?>