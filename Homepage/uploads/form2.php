<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Formulaire de Demande de Stage</title>
<link rel="stylesheet" href="css2.css">
</head>
<body>
<div class="container">
    <h1>Formulaire de Demande de Stage</h1>
    <p>Trouvez votre stage au sein de notre Entreprise</p>
    <form action="#" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="tel">Téléphone :</label>
            <input type="text" id="tel" name="tel" required>
        </div>
        <div class="form-group">
            <label for="domaine">Domaine de Stages :</label>
            <input type="text" id="domaine" name="domaine" required>
        </div>
        <div class="form-group">
            <label for="type">Type de Stage :</label>
            <input type="text" id="type" name="type" required>
        </div>
        <div class="form-group">
            <label for="universite">Nom de votre université actuelle :</label>
            <input type="text" id="universite" name="universite" required>
        </div>
        <div class="form-group">
            <label for="motivation">Motivation pour le stage :</label>
            <textarea id="motivation" name="motivation" required></textarea>
        </div>
        <div class="form-group">
            <label for="cv">CV (PDF) :</label>
            <input type="file" id="cv" name="cv" required accept=".pdf">
        </div>
        <button type="submit">Envoyer</button>
    </form>
    <div class="contact-info">
        <h2>Contact</h2>
        <p><strong>Phone:</strong> +216 71 001 298</p>
        <p><strong>Email:</strong> assistance@tunisietelecom.tn</p>
        <p><strong>Location:</strong> Cité Ennassim, avenue du japon Montplaisir 1073 Tunis</p>
    </div>
</div>

</body>
</html>
<?php
require_once(__DIR__ . '/DB.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // récupérer les valeurs du formulaire ici
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $domaine = $_POST['domaine'];
    $type_stage = $_POST['type'];
    $universite = $_POST['universite'];
    $motivation = $_POST['motivation'];
    $cv_file = $_FILES['cv'];

    // Vérifier si un fichier a été téléchargé et s'il n'y a pas d'erreur
    if ($cv_file['error'] === UPLOAD_ERR_OK && isset($cv_file['tmp_name']) && is_uploaded_file($cv_file['tmp_name'])) {
        // Définir le répertoire de destination pour le CV
        $upload_dir = __DIR__ . '/../uploads/';

        // Générer un nom de fichier unique pour éviter les collisions
        $cv_filename = uniqid() . '_' . basename($cv_file["name"]);
        // Chemin complet du fichier CV sur le serveur
        $cv_path = $upload_dir . $cv_filename;

        // Déplacer le fichier téléchargé vers le répertoire d'uploads
        if (move_uploaded_file($cv_file["tmp_name"], $cv_path)) {
            // Enregistrer les données dans la base de données
            $sql = "INSERT INTO demandes_stage (prenom, nom, email, tel, domaine, type_stage, universite, motivation, cv_path)
            VALUES ('$prenom', '$nom', '$email', '$tel', '$domaine', '$type_stage', '$universite', '$motivation', '$cv_path')";

            if ($conn->query($sql) === TRUE) {
                $message = "Demande de stage enregistrée avec succès";
            } else {
                $message = "Erreur: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $message = "Erreur lors du téléchargement du fichier.";
        }
    } else {
        $message = "Erreur: Aucun fichier CV n'a été téléchargé ou une erreur est survenue lors du téléchargement.";
    }

    $conn->close();
}
?>
