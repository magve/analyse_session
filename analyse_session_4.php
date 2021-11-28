<?php
# Cette ligne doit etre la premiere car elle écrit dans l'en-tête technique, invisble mais avant le début des balises
require_once( 'modules/session_api.php' );

# on écrit dans le fichier php_error.log (c:\MAMP\logs\php_error.log)
error_log('la page analyse_session_4.php dit :');
session_log();

if (isset($_POST["information_a_conserver"])){
	# si elle est fournie, on sauvegarde l'information en mémoire
	session_set( "information", $_POST["information_a_conserver"] );
}

session_log();


?>
<!DOCTYPE html>
<html lang="">
  <body>
	<h1>Analyse session</h1>
	<p>Ici, en entête de page on a traité ce que le formulaire a envoyé dans la requête.<br/>
<?php

if (isset($_POST["information_a_conserver"])){
	echo( "<h4>POST reçu :".$_POST["information_a_conserver"]."</h4>" );
}
?>	
	L'information a été stockée dans la session.<br/>
	Vous pouvez constater que l'information saisie n'est pas dans la page (pas de champ 'hidden').<br/>
Constatez que si vous rappelez la page, il n'y a plus de 'POST reçu' : <a href="analyse_session_4.php">analyse_session_4.php</a><br/>
Vous pouvez retourner changer le contenu du champ, la valeur sera remplacée <a href="analyse_session_3.php">analyse_session_3.php</a><br/>
Ensuite, cliquez sur ce lien : <a href="analyse_session_5.php">analyse_session_5.php</a></p>

<p>Dans la page, pour visualiser le contenu de la session, on a utilisé la fonction session_log() qui écrit dans le fichier php_error.log (c:\MAMP\logs\php_error.log)</p>
</body>
</html>