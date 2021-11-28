<?php
# Cette classe est inspirée par un projet professionel :
# https://github.com/mantisbt/mantisbt/blob/master/core/session_api.php
#... en simplifiant beaucoup mais en gardant l'esprit et le format de la documentation

/**
 * Session API
 *
 * La sauvegarde d'une valeur dans la session nécessite plusieurs manipulations,
 * qui sont rassemblées ici pour ne plus y penser après et pour simplifier...
 *
 * En ajoutant en première ligne de code d'une page la ligne :
 *     require_once( 'modules/session_api.php' );
 * on rend disponible les fonctions:
 *     session_set( nom, valeur )     : pour définir une valeur
 *     session_get( nom )             : pour récupérer une valeur quelconque
 *     session_get_int( nom )         :\
 *     session_get_string( nom )      :- pour récupérer une valeur typée 
 *     session_get_bool( nom )        :/
 *
 * pour pouvoir se deconnecter, il suffit d'appeler le lien :
 *      <a href="?deconnexion=">(Déconnexion)</a>
 * Ceci va vider la session et remplacer le cookie de session
 *
 * un mécanisme de "site en travaux", est défini voir 'offline.php' plus bas.
 *
 * Ce fichier contient dans l'ordre
 * 1. déclaration d'une classe abstraite
 * 2. implémentation de la classe abstraite
 * 3. déclaration de fonctions décritent au-dessus qui utilisent la classe et 
 *    que l'on appelera depuis les pages où on en aura besoin
 * 4. code qui sera exécuté systématiquement quand une page fera require_once
 *
 * @package module
 * @subpackage SessionAPI
 */
$g_session = null;

/**
 * Abstract interface for a Session handler.<br />
 * Ceci n'est pas destiné à être utilisé ailleurs que dans cette API, c'est 
 * dela mécanique interne.
 * 
 * Partie 1. déclaration d'une classe abstraite
 */
abstract class AbstractSimpleSession {
	/**
	 * Session ID
	 */
	protected $id;

	/**
	 * Constructor qui va me permettre d'initialiser la mecanique generale de cette classe
	 */
	abstract function __construct();

	/**
	 * get session data
	 * @param string $p_name    The name of the value to set.
	 * @param mixed  $p_default The value to set.
	 * @return string
	 */
	abstract function get( $p_name, $p_default = null );

	/**
	 * set session data
	 * @param string $p_name  The name of the value to set.
	 * @param mixed  $p_value The value to set.
	 * @return void
	 */
	abstract function set( $p_name, $p_value );

	/**
	 * delete session data
	 * @param string $p_name The name of the value to set.
	 * @return void
	 */
	abstract function delete( $p_name );

	/**
	 * destroy session
	 * @return void
	 */
	abstract function destroy();

	/**
	 * log session in php_error.log file
	 * @return void
	 */
	abstract function log();
}

/**
 * Implementation de la classe abstraite, qui va être utilisée plus bas
 *
 * Implementation of the abstract simple session interface using
 * standard PHP sessions stored on the server's filesystem according
 * to PHP's session.* settings in 'php.ini'.
 * 
 * Partie 2. implémentation de la classe abstraite 
 */
class SimpleSession extends AbstractSimpleSession {
	/**
	 * Constructor
	 * @param integer $p_session_id The session id.
	 */
	function __construct( $p_session_id = null ) {
		global $g_cookie_secure_flag_enabled;		

		# Handle session cookie and caching
		session_cache_limiter( 'nocache' );
		session_set_cookie_params( 0, '/', '', $g_cookie_secure_flag_enabled, true );

		# Handle existent session ID
		if( !is_null( $p_session_id ) ) {
			session_id( $p_session_id );
		}

		# Initialize the session
		session_start();
		$this->id = session_id();

		# Initialize the keyed session store
		if( !isset( $_SESSION[$this->id] ) ) {
			$_SESSION[$this->id] = array();
		}
	}

	/**
	 * get session data
	 * @param string $p_name    The name of the value to set.
	 * @param mixed  $p_default The value to set.
	 * @return string
	 */
	function get( $p_name, $p_default = null ) {
		if( isset( $_SESSION[$this->id][$p_name] ) ) {
			return $_SESSION[$this->id][$p_name];
		}
		if( func_num_args() == 2 ) {
			return $p_default;
		}
	}

	/**
	 * set session data
	 * @param string $p_name  The name of the value to set.
	 * @param mixed  $p_value The value to set.
	 * @return void
	 */
	function set( $p_name, $p_value ) {
		$_SESSION[$this->id][$p_name] = $p_value;
	}

	/**
	 * delete session data
	 * @param string $p_name The name of the value to set.
	 * @return void
	 */
	function delete( $p_name ) {
		unset( $_SESSION[$this->id][$p_name] );
	}

	/**
	 * destroy session
	 * @return void
	 */
	function destroy() {
		error_log("destroy SESSION ".$this->id);
		unset( $_SESSION[$this->id] );
		# Initialize the keyed session store with a new array
		$_SESSION[$this->id] = array();
		session_regenerate_id();
	}

	/**
	 * log session
	 * @return void
	 */
	function log() {
		# print all values in session
		error_log("SESSION: ".$this->id);
		foreach ( array_keys($_SESSION[$this->id]) as $aKey ){
			error_log($aKey." = ".$_SESSION[$this->id][$aKey]);
		}
		error_log("-------");
	}
}

/* 
 * Partie 3. déclaration de fonctions décritent au-dessus qui utilisent la classe et 
 *    que l'on appelera depuis les pages où on en aura besoin
 */

/**
 * Fonction interne à ce package, ne pas appeler de l'extérieur
 * Initialize the appropriate session handler.
 * @param string $p_session_id Session ID.
 * @return void
 */
function session_init( $p_session_id = null ) {
	global $g_session;

	$g_session = new SimpleSession( $p_session_id );
}

/**
 * Get arbitrary data from the session.
 * @param string $p_name    Session variable name.
 * @param mixed  $p_default Default value.
 * @return mixed Session variable
 */
function session_get( $p_name, $p_default = null ) {
	global $g_session;

	$t_args = func_get_args();
	return call_user_func_array( array( $g_session, 'get' ), $t_args );
}

/**
 * Get an integer from the session.
 * @param string       $p_name    Session variable name.
 * @param integer|null $p_default Default value.
 * @return integer Session variable
 */
function session_get_int( $p_name, $p_default = null ) {
	$t_args = func_get_args();
	return (int)call_user_func_array( 'session_get', $t_args );
}

/**
 * Get a boolean from the session.
 * @param string       $p_name    Session variable name.
 * @param boolean|null $p_default Default value.
 * @return boolean Session variable
 */
function session_get_bool( $p_name, $p_default = null ) {
	$t_args = func_get_args();
	return true && call_user_func_array( 'session_get', $t_args );
}

/**
 * Get a string from the session.
 * @param string      $p_name    Session variable name.
 * @param string|null $p_default Default value.
 * @return string Session variable
 */
function session_get_string( $p_name, $p_default = null ) {
	$t_args = func_get_args();
	return '' . call_user_func_array( 'session_get', $t_args );
}

/**
 * Set a session variable.
 * @param string $p_name  Session variable name.
 * @param mixed  $p_value Variable value.
 * @return void
 */
function session_set( $p_name, $p_value ) {
	global $g_session;
	$g_session->set( $p_name, $p_value );
}

/**
 * Delete a session variable.
 * @param string $p_name Session variable name.
 * @return void
 */
function session_delete( $p_name ) {
	global $g_session;
	$g_session->delete( $p_name );
}

/**
 * Destroy the session entirely.
 * @return void
 */
function session_clean() {
	global $g_session;
	if(isset($g_session)){
		$g_session->destroy();
	}
}

/**
 * Destroy the session entirely.
 * @return void
 */
function session_log() {
	global $g_session;
	if(isset($g_session)){
		$g_session->log();
	}
}

/*
 * Partie 4. code qui sera exécuté systématiquement quand une page fera require_once
 */

/*
 * Before doing anything... check if site is down for maintenance
 *
 * ici tout le monde reçoit la page 'offline' quand le fichier existe
 * ... sauf l'administrateur du site qui ajoute manuellement à la fin
 * à la fin de l'URL '?admin=' 
 * Quand la maintenance est terminée il suffit de renommer le fichier
 */
if( file_exists( 'offline.php' ) && !isset( $_GET['admin'] ) ) {
	include( 'offline.php' );
	exit;
}

# Initialize the session
session_init();

// pour se deconnecter
if (isset($_GET['deconnexion'])){
	error_log("deconnexion");
	// on initialise une nouvelle session
	session_clean();
	echo "<html>\n<head>\n";
	echo "\t<meta http-equiv=\"Refresh\" content=\"3; URL='/'\" />\n";
	echo "</head><body>déconnexion réalisée, vous allez être redirigé vers la page d'accueil...</body></html>\n";
	die();
}
