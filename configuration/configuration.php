<?php
/*************************************/
/*******       VARIABLE       ********/
/*************************************/
const TITLE = 'Starloco';
date_default_timezone_set('Europe/Paris');

/** URL INTERNE SITE**/
const URL_SITE = 'https://starloco.fr/';
const URL_BOUTIQUE = '?page=shop';

/** Social Network **/
const VIEW_TAG_DISCORD = 'yes';
const TAG_DISCORD = 'k3Yk9DuhgY';
const VIEW_TAG_TWITTER = 'no';
const TAG_TWITTER = 'starloco';
const VIEW_TAG_FACEBOOK = 'no';
const TAG_FACEBOOK = 'starloco';
const VIEW_TAG_GOOGLE = 'no';
const URL_GOOGLE = 'https://google.com';

/** VOTE rpg OU serveurprive **/
const QUEL_VOTE = 'serveurprive';
const URL_RPG = 'http://www.rpg-paradize.com/';
const URL_SERVEURPRIVE = 'https://serveur-prive.net/dofus/starloco/vote';
const TOKEN_SERVEURPRIVE = 'YOUR_TOKEN';

/** COMMUNAUTE**/
const URL_BARBOK = 'https://barbok.eratz.fr/';
const URL_RSS_NEWS_IPB = '';

/** Http://www... neccésaire dans l'URL (compatibilité Firefox).**/
const URL_LAUNCHER_1_29 = 'https://starloco.fr/download/updater/starloco.exe';
const URL_INSTALLATEUR = 'http://127.0.0.1/upload/Install.exe';
const URL_CONFIG = 'http://127.0.0.1/upload/config.xml';

/** PARTIE ADMINISTRATEUR DU SITE**/
/** Vous devez mettre chaque GUID de chaque compte pour pouvoir avoir accès au panel admin [1, 2, 3,4] etc....**/
define('ADMIN_GUID', [1, 4]);

/** Vous devez configurer chaque personne avec le GUID du compte pour donner les droits de voir chaques section pour pouvoir administrer array('GUID','GUID', 'GUID')**/
$restrictedPagesPermissions = array(
	'adm_site' => array('1','4'),
	'adm_gestion_domaine' => array('1','4'),
	'adm_logs_site' => array('1','4'),
	'adm_news' => array('1','4'),
	'adm_commentaires' => array('1','4'),
	'adm_news_rss' => array('1','4'),
	'adm_account' => array('1','4'),
	'adm_perso' => array('1','4'),
	'adm_loterie' => array('1','4'),
	'adm_loterie_gagnant' => array('1','4'),
	'adm_ticket' => array('1','4'),
	'adm_bugtracker' => array('1','4'),
	'adm_tickets_encyclopedie' => array('1','4'),
	'adm_categorie_boutique' => array('1','4'),
	'adm_boutique' => array('1','4'),
	'adm_achat_point' => array('1','4'),
	'adm_achat_boutique' => array('1','4'),
);

/**NOM POINT**/
const NOM_POINT = 'Bullions'; // Affiche le nom que vous voulez donner à vos points

/** Serveurs **/ //permet d'afficher le serveur dans etat des serveurs
const AFFICHER_SERVEUR_0 = 'no';
const SERVEUR_0 = 'Login';
const IMAGE_SERVEUR_0 = '601.png';
const ID_SERVEUR_0 = '601';
const IP_SERVEUR_0 = '127.0.0.1';
const PORT_SERVEUR_0 = '450';

const AFFICHER_SERVEUR_1 = 'yes';
const SERVEUR_1 = 'Eratz';
const IMAGE_SERVEUR_1 = '601.png';
const ID_SERVEUR_1 = '601';
const IP_SERVEUR_1 = '127.0.0.1';
const PORT_SERVEUR_1 = '5555';

const AFFICHER_SERVEUR_2 = 'A VOUS DE CHOISIR';
const SERVEUR_2 = 'Serveur Test';
const IMAGE_SERVEUR_2 = '42.png';
const ID_SERVEUR_2 = '';
const IP_SERVEUR_2 = '';
const PORT_SERVEUR_2 = '';

const AFFICHER_SERVEUR_3 = 'no';
const SERVEUR_3 = 'A VOUS DE CHOISIR';
const IMAGE_SERVEUR_3 = 'A VOUS DE CHOISIR';
const ID_SERVEUR_3 = '';
const IP_SERVEUR_3 = '';
const PORT_SERVEUR_3 = '';

const AFFICHER_SERVEUR_4 = 'no';
const SERVEUR_4 = 'A VOUS DE CHOISIR';
const IMAGE_SERVEUR_4 = 'A VOUS DE CHOISIR';
const ID_SERVEUR_4 = '';
const IP_SERVEUR_4 = '';
const PORT_SERVEUR_4 = '';

const AFFICHER_SERVEUR_5 = 'no';
const SERVEUR_5 = 'A VOUS DE CHOISIR';
const IMAGE_SERVEUR_5 = 'A VOUS DE CHOISIR';
const ID_SERVEUR_5 = '';
const IP_SERVEUR_5 = '';
const PORT_SERVEUR_5 = '';

const AFFICHER_SERVEUR_6 = 'no';
const SERVEUR_6 = 'A VOUS DE CHOISIR';
const IMAGE_SERVEUR_6 = 'A VOUS DE CHOISIR';
const ID_SERVEUR_6 = '';
const IP_SERVEUR_6 = '';
const PORT_SERVEUR_6 = '';

const AFFICHER_SERVEUR_7 = 'no';
const SERVEUR_7 = 'A VOUS DE CHOISIR';
const IMAGE_SERVEUR_7 = 'A VOUS DE CHOISIR';
const ID_SERVEUR_7 = '';
const IP_SERVEUR_7 = '';
const PORT_SERVEUR_7 = '';

const AFFICHER_SERVEUR_8 = 'no';
const SERVEUR_8 = 'A VOUS DE CHOISIR';
const IMAGE_SERVEUR_8 = 'A VOUS DE CHOISIR';
const ID_SERVEUR_8 = '';
const IP_SERVEUR_8 = '';
const PORT_SERVEUR_8 = '';

const AFFICHER_SERVEUR_9 = 'no';
const SERVEUR_9 = 'A VOUS DE CHOISIR';
const IMAGE_SERVEUR_9 = 'A VOUS DE CHOISIR';
const ID_SERVEUR_9 = '';
const IP_SERVEUR_9 = '';
const PORT_SERVEUR_9 = '';

const AFFICHER_SERVEUR_10 = 'no';
const SERVEUR_10 = 'A VOUS DE CHOISIR';
const IMAGE_SERVEUR_10 = 'A VOUS DE CHOISIR';
const ID_SERVEUR_10 = '';
const IP_SERVEUR_10 = '';
const PORT_SERVEUR_10 = '';

/** Server login **/
const LOGIN_DB_NAME = 'starloco_login';
const LOGIN_DB_USER = 'starloco';
const LOGIN_DB_PASS = 'my_password';
$login = newPdo("127.0.0.1", LOGIN_DB_USER, LOGIN_DB_PASS, LOGIN_DB_NAME);

/** Server game **/
const JIVA_DB_NAME = 'starloco_game';
const JIVA_DB_USER = 'starloco';
const JIVA_DB_PASS = 'my_password';
$jiva = newPdo("127.0.0.1", JIVA_DB_USER, JIVA_DB_PASS, JIVA_DB_NAME);

/** Server web **/
const WEB_DB_NAME = 'starloco_web';
const WEB_DB_USER = 'starloco';
const WEB_DB_PASS = 'my_password';
$web = newPdo("127.0.0.1", WEB_DB_USER, WEB_DB_PASS, WEB_DB_NAME);

/** Votes **/
const PTS_PER_VOTE = '5';
const CLOUDFLARE_ENABLE = true;

/** Mysql **/
/** Variables **/
const DB_IP = '127.0.0.1';
const DB_NAME = 'starloco_web';
const DB_USER = 'starloco';
const DB_PASS = 'my_password';
$connection = newPdo(DB_IP, DB_USER, DB_PASS, DB_NAME);

/** Fonction **/
function newPdo($ip, $user, $pass, $db) {
	try {
		$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		$connection = new PDO('mysql:host=' . $ip . ';dbname=' . $db, $user, $pass, $options);
		$connection -> exec('SET NAMES utf8');
		return $connection;
	} catch(Exception $e) {
		die('Error : ' . $e -> getMessage());
	}
}

/** Don't tuch **/
const PAGE_WITHOUT_RIGHT_MENU = 'signin register lost_password';

/*************************************/
/*******       FUNCTION       ********/
/*************************************/

function checkState($ip, $port) {
	$socket = @fsockopen($ip, $port, $errno, $errstr, 10);
	if (!$socket) {
		return false;
	}
	fclose($socket);
	return true;
}

function convertDateEnToFr($date) {
	$split = explode(" ", $date);
	$month = "Unknow";

	switch(strtoupper($split[1])) {
		case "DEC": return str_replace("Dec", "Décembre", $date);
		case "JAN": return str_replace("Jan", "Janvier", $date);
		case "FEV": return str_replace("Fev", "Février", $date);
		case "MAR": return str_replace("Mar", "Mars", $date);
		case "AVR": return str_replace("Avr", "Avril", $date);
		case "MAI": return str_replace("Mai", "Mai", $date);
		case "JUI": return str_replace("Jui", "Juin", $date);
		case "JUL": return str_replace("Jul", "Juillet", $date);
		case "AOU": return str_replace("Aou", "Août", $date);
		case "SEP": return str_replace("Sep", "Septembre", $date);
		case "OCT": return str_replace("Oct", "Octobre", $date);
		case "NOV": return str_replace("Nov", "Novembre", $date);
		default: return "";
	}
}


function convertTimestampToUptime($startTime) {
	$time = round(microtime(true) * 1000) - $startTime;

	$day = (int) ($time / (3600 * 1000 * 24));
	$time %= 3600 * 1000 * 24;

	$hour = (int) ($time / (3600 * 1000));
	$time %= 3600 * 1000;

	$min = (int) ($time / (60 * 1000));
	$time %= 60 * 1000;

	$sec  = (int) ($time / 1000);

	return $day . "j " . $hour . "h " . $min . "m " . $sec . "s.";
}

function convertClassIdToString($id, $sexe) {
	switch($id) {
		case 1: return ($sexe == 0 ? "Féca" : "Fécatte");
		case 2: return ($sexe == 0 ? "Osanamodas" : "Osamodas");
		case 3: return ($sexe == 0 ? "Enutrof" : "Enutrof");
		case 4: return ($sexe == 0 ? "Sram" : "Sramette");
		case 5: return ($sexe == 0 ? "Xélor" : "Xélor");
		case 6: return ($sexe == 0 ? "Ecaflip" : "Ecaflip");
		case 7: return ($sexe == 0 ? "Eniripsa" : "Eniripsa");
		case 8: return ($sexe == 0 ? "Iop" : "Iopette");
		case 9: return ($sexe == 0 ? "Crâ" : "Crâtte");
		case 10: return ($sexe == 0 ? "Sadida" : "Sadida");
		case 11: return ($sexe == 0 ? "Sacrieur" : "Sacrieuse");
		case 12: return ($sexe == 0 ? "Pandawa" : "Pandawa");
	}
	return "Unknow";
}

function checkString($data) {
	if(!preg_match("/^[0-9A-zàâéèêïîöôüûùÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿ Ñ!?,.ñ_-]*$/", $data))
		return true;
	return false;
}

function parseDate($date) {
	$array = explode("~", $date);
	return "le " . $array[2] . "/" . $array[1] . "/" . $array[0] . " à " . $array[3] . "h" . $array[4];
}

function array_sort($array, $on, $order = SORT_ASC) {
	$new_array = array();
	$sortable_array = array();

	if(count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}

		switch ($order) {
			case SORT_ASC:
				asort($sortable_array);
				break;
			case SORT_DESC:
				arsort($sortable_array);
				break;
		}

		foreach ($sortable_array as $k => $v) {
			$new_array[$k] = $array[$k];
		}
	}

	return $new_array;
}

function convertStatsToString($data) {
	$stats = explode(",", $data);
	$value = "";
	for($i = 0; $i < count($stats); $i++) {
		$array = explode("#", $stats[$i]);
		$id = $array[0];
		$de = hexdec($array[1]);
		$a = hexdec($array[2]);

		$x = convertStatsId($id);

		$suffix = explode(";", $x);
		$signe = explode(";", $x);
		if ($x == ";")
			continue;

		if($a == 0)
			echo $signe[1]." $de ".$suffix[0]." <br>" ;
		else
			echo $signe[1]." $de à $a ".$suffix[0]." <br>" ;
	}
	return "";
}

function convertStatsId($id) {
	$suffix = "";
	$signe = "";
	switch($id)	{
		case '99':
			$suffix = ' Vitalité.';
			$signe = '- ';
			break;
		case '9d':
			$suffix = ' Terre.';
			$signe = '- ';
			break;
		case '9b':
			$suffix = ' Feu.';
			$signe = '- ';
			break;
		case '9a':
			$suffix = ' Air.';
			$signe = '- ';
			break;
		case '98':
			$suffix = ' Eau.';
			$signe = '- ';
			break;
		case '7d':
			$suffix = ' Vitalité.';
			$signe = '+ ';
			break;
		case '7c':
			$suffix = ' Sagesse.';
			$signe = '+ ';
			break;
		case '76':
			$suffix = ' Terre.';
			$signe = '+ ';
			break;
		case '7e':
			$suffix = ' Feu.';
			$signe = '+ ';
			break;
		case '77':
			$suffix = ' Air.';
			$signe = '+ ';
			break;
		case '7b':
			$suffix = ' Eau.';
			$signe = '+ ';
			break;
		case '6f':
			$suffix = ' Pa.';
			$signe = '+ ';
			break;
		case '65':
			$suffix = ' Pa.';
			$signe = '- ';
			break;
		case '80':
			$suffix = ' Pm.';
			$signe = '+ ';
			break;
		case '7f':
			$suffix = ' Pm.';
			$signe = '- ';
			break;
		case '75':
			$suffix = ' Po.';
			$signe = '+ ';
			break;
		case '74':
			$suffix = ' Po.';
			$signe = '- ';
			break;
		case '70':
			$suffix = ' Dommage.';
			$signe = '+ ';
			break;
		case '91':
			$suffix = ' Dommage.';
			$signe = '- ';
			break;
		case '8a':
			$suffix = ' Dommage (%).';
			$signe = '+ ';
			break;
		case 'ba':
			$suffix = ' Dommage (%).';
			$signe = '- ';
			break;
		case 'dc':
			$suffix = ' Dommage renvoyé.';
			$signe = '+ ';
			break;
		case 'b2':
			$suffix = ' Soins.';
			$signe = '+ ';
			break;
		case 'b3':
			$suffix = ' Soins.';
			$signe = '- ';
			break;
		case '73':
			$suffix = ' Coup Critique.';
			$signe = '+ ';
			break;
		case '7a':
			$suffix = ' Echec Critique.';
			$signe = '+ ';
			break;
		case 'b6':
			$suffix = ' Invocation.';
			$signe = '+ ';
			break;
		case '9e':
			$suffix = ' Pod.';
			$signe = '+ ';
			break;
		case '9f':
			$suffix = ' Pod.';
			$signe = '- ';
			break;
		case 'ae':
			$suffix = ' Initiative.';
			$signe = '+ ';
			break;
		case 'af':
			$suffix = ' Initiative.';
			$signe = '- ';
			break;
		case 'b0':
			$suffix = ' Prospection.';
			$signe = '+ ';
			break;
		case 'b1':
			$suffix = ' Prospection.';
			$signe = '- ';
			break;
		case 'e1':
			$suffix = ' Piège.';
			$signe = '+ ';
			break;
		case 'e2':
			$suffix = ' Piège (%).';
			$signe = '+ ';
			break;
		case 'a0':
			$suffix = ' Esquive perte Pa (%).';
			$signe = '+ ';
			break;
		case 'aa2':
			$suffix = ' Esquive perte Pa (%).';
			$signe = '- ';
			break;
		case 'a1':
			$suffix = ' Esquive perte Pm (%).';
			$signe = '+ ';
			break;
		case 'a3':
			$suffix = ' Esquive perte Pm (%).';
			$signe = '- ';
			break;
		case 'f4':
			$suffix = ' Résistance Neutre.';
			$signe = '+ ';
			break;
		case 'f9':
			$suffix = ' Résistance Neutre.';
			$signe = '- ';
			break;
		case 'f0':
			$suffix = ' Résistance Terre.';
			$signe = '+ ';
			break;
		case 'f5':
			$suffix = ' Résistance Terre.';
			$signe = '- ';
			break;
		case 'f3':
			$suffix = ' Résistance Feu.';
			$signe = '+ ';
			break;
		case 'f8':
			$suffix = ' Résistance Feu.';
			$signe = '- ';
			break;
		case 'f2':
			$suffix = ' Résistance Air.';
			$signe = '+ ';
			break;
		case 'f7':
			$suffix = ' Résistance Air.';
			$signe = '- ';
			break;
		case 'f1':
			$suffix = ' Résistance Eau.';
			$signe = '+ ';
			break;
		case 'f6':
			$suffix = ' Résistance Eau.';
			$signe = '- ';
			break;
		case 'd6':
			$suffix = ' Résistance Neutre (%).';
			$signe = '+ ';
			break;
		case 'db':
			$suffix = ' Résistance Neutre (%).';
			$signe = '- ';
			break;
		case 'd2':
			$suffix = ' Résistance Terre (%).';
			$signe = '+ ';
			break;
		case 'd7':
			$suffix = ' Résistance Terre (%).';
			$signe = '- ';
			break;
		case 'd5':
			$suffix = ' Résistance Feu (%).';
			$signe = '+ ';
			break;
		case 'da':
			$suffix = ' Résistance Feu (%).';
			$signe = '- ';
			break;
		case 'd4':
			$suffix = ' Résistance Air (%).';
			$signe = '+ ';
			break;
		case 'd9':
			$suffix = ' Résistance Air (%).';
			$signe = '- ';
			break;
		case 'd3':
			$suffix = ' Résistance Eau (%).';
			$signe = '+ ';
			break;
		case 'd8':
			$suffix = ' Résistance Eau (%).';
			$signe = '- ';
			break;
		case 'fe':
			$suffix = ' Résistance Neutre face aux combattants (%).';
			$signe = '+ ';
			break;
		case '103':
			$suffix = ' Résistance Neutre face aux combattants (%).';
			$signe = '- ';
			break;
		case 'fa':
			$suffix = ' Résistance Terre face aux combattants (%).';
			$signe = '+ ';
			break;
		case 'ff':
			$suffix = ' Résistance Terre face aux combattants (%).';
			$signe = '- ';
			break;
		case 'fd':
			$suffix = ' Résistance Feu face aux combattants (%).';
			$signe = '+ ';
			break;
		case '102':
			$suffix = ' Résistance Feu face aux combattants (%).';
			$signe = '- ';
			break;
		case 'fb':
			$suffix = ' Résistance Eau face aux combattants (%).';
			$signe = '+ ';
			break;
		case '100':
			$suffix = ' Résistance Eau face aux combattants (%).';
			$signe = '- ';
			break;
		case 'fc':
			$suffix = ' Résistance Air face aux combattants (%).';
			$signe = '+ ';
			break;
		case '101':
			$suffix = ' Résistance Air face aux combattants (%).';
			$signe = '- ';
			break;
		case '108':
			$suffix = ' Résistance Neutre face aux combattants.';
			$signe = '+ ';
			break;
		case '104':
			$suffix = ' Résistance Terre face aux combattants.';
			$signe = '+ ';
			break;
		case '107':
			$suffix = ' Résistance Feu face aux combattants.';
			$signe = '+ ';
			break;
		case '105':
			$suffix = ' Résistance Eau face aux combattants.';
			$signe = '+ ';
			break;
		case '106':
			$suffix = ' Résistance Air face aux combattants.';
			$signe = '+ ';
			break;
		case '8b':
			$suffix = ' Energie.';
			$signe = '+';
			break;
		default:
			$suffix = '';
			$signe = '';
			break;
	}
	return $suffix.";".$signe;
}
?>
