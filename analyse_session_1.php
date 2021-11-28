<?php
# Cette ligne doit etre la premiere car elle écrit dans l'en-tête technique, invisble mais avant le début des balises
require_once( 'modules/session_api.php' );
?>
<!DOCTYPE html>
<html lang="">
  <body>
	<h1>Analyse session</h1>
<p>PHP définit systématiquement un cookie 'PHPSESSID'.</p>
<p>La valeur donnée à ce cookie est en hexadécimal, générée de manière à ce que 2 utilisateurs n'aient pas la même valeur.<br />
Si vous rafraichissez la page, ce cookie reste avec la même valeur. </p>
<p>Si vous effacez le cookie avant de rafraichir, une nouvelle valeur est fournie.</p>
<p>Ensuite, cliquez sur ce lien : <a href="analyse_session_2.php">analyse_session_2.php</a></p>
</body>
</html>