<?php
# Cette ligne doit etre la premiere car elle écrit dans l'en-tête technique, invisble mais avant le début des balises
require_once( 'modules/session_api.php' );
?>
<!DOCTYPE html>
<html lang="">
  <body>
	<h1>Analyse session</h1>

Ici on souhaite fermer la session, oublier son contenu 
<a href="?deconnexion=">(Déconnexion)</a>
</body>
</html>