<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Formulaire de Demande de Stage</title>
<style>
  * {
    box-sizing: border-box;
  }
  body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to bottom, black, darkgreen); /* Fond dégradé noir à vert foncé */
    margin: 0;
    padding: 0;
  }
  .container {
    max-width : 1000px;
    margin: 50px auto;
    background-color: rgba(255, 255, 255, 0.8); /* Fond blanc transparent */
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5); /* Ombre légère */
  }
  h1 {
    text-align: center;
    color: #4CAF50; /* Vert */
    margin-bottom: 20px;
  }
  p {
    text-align: center;
    color: #4CAF50; /* Vert */
    margin-bottom: 20px;}
  .form-group {
    margin-bottom: 20px;
  }
  label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #666; /* Gris */
  }
  input[type="text"],
  input[type="email"],
  textarea,
  input[type="file"] {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc; /* Bordure grise */
    background-color: #d0dfe8; /* Bleu-gris */
    color: white; /* Texte blanc */
  }
  textarea {
    height: 150px;
    resize: none;
  }
  input[type="file"] {
    background-color: black; /* Fond noir */
    color: white; /* Texte blanc */
    cursor: pointer;
  }
  button[type="submit"] {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: none;
    background-color: #4CAF50; /* Vert */
    color: white; /* Texte blanc */
    cursor: pointer;
    font-weight: bold;
  }
  button[type="submit"]:hover {
    background-color: #45a049; /* Vert légèrement plus foncé au survol */
  }
  .contact-info {
    background-color: #fff; /* Fond blanc */
    border-radius: 20px;
    padding: 20px;
    margin-top: 30px;
  }
</style>
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
    <input type="file" name="cv" id="cv">
    <input type="submit" value="Upload CV" name="submit">
</div>
<button type="submit">Envoyer</button>
</form>
    <div class="contact-info">
        <h2>Contact</h2>
        <p><strong>Phone:</strong> +216 24 201 201</p>
        <p><strong>Email:</strong> contact@aftercode.tn</p>
        <p><strong>Alternate Email:</strong> contact.aftercode@gmail.com</p>
        <p><strong>Location:</strong> Avenue la ligue arab, Dar Chaabane Al Fehri, Nabeul</p>
    </div>
</div>

</body>
</html>
<?php
require_once(__DIR__ . '/../db/DB.php');

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
      $upload_dir = '__DIR__ . '/../uploads'./;

      // Vérifier si le répertoire d'uploads existe, sinon le créer
      if (!file_exists($upload_dir)) {
          mkdir($upload_dir, 0777, true); // Créer le répertoire avec les permissions 0777
      }

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
              echo "Demande de stage enregistrée avec succès";
          } else {
              echo "Erreur: " . $sql . "<br>" . $conn->error;
          }
      } else {
          echo "Erreur lors du téléchargement du fichier.";
      }
  } else {
      echo "Erreur: Aucun fichier CV n'a été téléchargé ou une erreur est survenue lors du téléchargement.";
  }

  $conn->close();
}
