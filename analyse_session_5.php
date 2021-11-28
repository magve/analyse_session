<?php
# Cette ligne doit etre la premiere car elle écrit dans l'en-tête technique, invisble mais avant le début des balises
require_once( 'modules/session_api.php' );
?>
<!doctype html>
<html lang="fr-FR">
  <body>
	<h1>Analyse session</h1>
	<p>Contenu de la valeur en session : <?=session_get("information")?><br/>
	Cliquez sur ce lien <a href="analyse_session_6.php">analyse_session_6.php</a><br />
	Pour un nouvel essai <a href="analyse_session_3.php">analyse_session_3.php</a></p>
</body>
</html>