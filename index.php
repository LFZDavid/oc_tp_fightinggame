<?php
// On enregistre notre autoload
function loadingClass($classname){
	require $classname.'.php';
}
spl_autoload_register('loadingClass');

session_start(); // Appel session_start APRES enregistrement de l'autoload

if (isset($_GET['deconnexion'])){
	session_destroy();
	header('Location: .');
	exit();
}

if (isset($_SESSION['char'])){ // Si la session existe, on restaure l'obj
	$char = $_SESSION['char'];
}


$db = new PDO('mysql:host=localhost;dbname=oc_tp_fighting_game','root','');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué
?>

<!DOCTYPE html>
<html>
  <head>
    <title>TP : Mini jeu de combat</title>
    <meta charset="utf-8" />
  </head>
  <body>
	<a href="index.php"><button>Accueil</button></a>
	<a href="index.php?deconnexion"><button>Deconnexion</button></a>
	


<?php
$manager = new CharManager($db);// on cré une instance de manager avec $db
?>

<!--Nombre de personnages crées-->
	<p>Nombre de personnabes créés : <?= $manager->count()?></p>

<?php
if (isset($_POST['create']) && isset($_POST['name'])){
	
	$char = new Char(['name'=>$_POST['name']]); // On crée une instance de perso

	if($manager->exists($_POST['name'])){ // On vérifie si le nom existe
		$message = 'Le nom choisi est pris!'; // Si oui message d'erreur
		unset($char);
	}
	else{
		$manager->add($char); // Si non, ajout du peros dans la bdd
	}
}

elseif (isset($_POST['use']) && isset($_POST['name'])){
	if($manager->exists($_POST['name'])){
		$char = $manager->get($_POST['name']);
	}
	else{
		$message = 'Ce personnage n\'existe pas !';
	}
}
elseif (isset($_GET['hit'])){

	if (!isset($char)){
		$message = 'Merci de créer un personnage ou de vous identifier.';
	}

	if(!$manager->exists((int) $_GET['hit'])){
		$message = '';
	}
	else{
		$charToHit = $manager->get((int) $_GET['hit']);

		$return = $char->hit($charToHit); // On stock dans $return les éventuelles erreurs ou messages que renvoie la method hit

		switch ($return) {
			case Char::MOI:
			$message = 'Pourquoi tu veux te frapper toi-même ?';
				break;

			case Char::PERSO_FRAPPE:
			$message = 'Le personnage a bien été frappé !';
			$manager->update($char);
			$manager->update($charToHit);
				break;

			case Char::PERSO_TUE:
			$message = 'Vous avez tué ce personnage!';

			$manager->update($char);
			$manager->delete($charToHit);
				break;

			case Char::PLUS_DE_FRAPPE:
			$message = 'Plus de frappe avant demain !';
			// Ajouter le countdown jusqu'a la prochaine frappe
				break;
		}
	}
}

if (isset($char)){
	?>
	<fieldset>
		<legend>Infos</legend>
		<p>
			<?php 
			$datetime = date("Y-m-d H:i:s");
			$lastminute = (date("H")-1);

			echo $datetime. '<br/>';
			echo $lastminute. '<br/>';

			?>
			Nom : <?=ucfirst(htmlspecialchars($char->name()))?><br/>
			Level : <strong><?= 0 + $char->level()?></strong><br/>
			Exp : <em><?= 0 + $char->exp()?>/100</em><br/>
			Force : <?= $char->dps()?><br/>
			Frappes disponibles : <strong><?= 3 - $char->hitcount()?></strong><br/>
			Dernière frappe : <?= $char->lasthit()?><br/>
			<?php
				$hp = $char->damages();
			?>
			Hp : <?= $hp?>
		</p>
	</fieldset>
	<fieldset>
		<legend>Qui frapper ?</legend>
		<p>
			<?php
				$chars = $manager->getList($char->name());
				if (empty($chars)){
					echo 'Personne a frapper !';
				}
				else{
					foreach ($chars as $otherChar) {

						$hp = 100 - $otherChar->damages();
						echo '<a href=?hit=', $otherChar->id(),'"><button>Frapper</button></a> ', ucfirst(htmlspecialchars($otherChar->name())),' -----
						 HP : <strong>' .$hp.'</strong><em>/100</em> 
						 - Level : <strong>',$otherChar->level(),'</strong> - exp : <strong>',$otherChar->exp(),'</strong><em>/100</em>
						 - Force : <strong>',$otherChar->dps(),'</strong>
						 - Frappes disponibles : <strong>', 3-$otherChar->hitcount(),'</strong>
						 - Dernière frappe : ',$otherChar->lasthit(),'<br/>';
					}
				}
				?>
			</p>
		</fieldset>
		<?php
}
else{
// FORMULAIRE
?>
    <form action="" method="post">
      <p>
        Nom : <input type="text" name="name" maxlength="50" />
        <input type="submit" value="Utiliser ce personnage" name="use" />
        <input type="submit" value="Créer ce personnage" name="create" />
        <br/>
        
      </p>
    </form>
<?php
}

if (isset($message)){
	echo $message ;
}

?>

  </body>
</html>

<?php
if (isset($char)){ // Si on a crée un perso, on le stock dans une variable
	$_SESSION['char'] = $char ;
}
