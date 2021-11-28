<?php
# Cette ligne doit etre la premiere car elle écrit dans l'en-tête technique, invisble mais avant le début des balises
require_once( 'modules/session_api.php' );
?>
<!DOCTYPE html>
<html lang="">
  <body>
	<h1>Analyse session</h1>
	
	<p>Ici un formulaire qui demande une information_a_conserver :
	<form method="post" action="analyse_session_4.php">
	<input type="text" name="information_a_conserver">
	<input type="submit" value="Envoyer">
	</form>
	
	Ce lien envoie vers la même cible mais avec le formulaire vide :</p>
    <a href="analyse_session_4.php">analyse_session_4.php</p>
</body>
</html>