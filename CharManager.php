<?php
class CharManager
{
  private $_db; // Instance de PDO
  
  public function __construct($db)
  {
    $this->setDb($db);
  }
 
  public function setDb(PDO $db)
  {
    $this->_db = $db;
  }
  
  public function add(Char $char)
  {
    // Préparation de la requête d'insertion.
    $req = $this->_db->prepare('INSERT INTO characters(name) VALUES(:name)');
    // Assignation des valeurs pour le nom du personnage.
    $req->bindValue(':name', $char->name());
    // Exécution de la requête.
    $req->execute();
    
    // Hydratation du personnage passé en paramètre avec assignation de son identifiant et des dégâts initiaux (= 0).
    $char->hydrate([
    	'id' => $this->_db->lastInsertId(),
    	'damages' => 0,
      'exp' => 0,
      'level' => 1
    ]);
  }
 

  public function count()
  {
    // Exécute une requête COUNT() et retourne le nombre de résultats retourné.
    return $this->_db->query('SELECT COUNT(*) FROM characters')->fetchColumn();
  }
  
  public function delete(Char $char)
  {
    // Exécute une requête de type DELETE.
    $this->_db->exec('DELETE FROM characters WHERE id ='.$char->id());

  }
  
  public function exists($info)
  {
    // Si le paramètre est un entier, c'est qu'on a fourni un identifiant.
    if (is_int($info)){ // On veut voir si tel perso ayant pour id $info existe
      // On exécute alors une requête COUNT() avec une clause WHERE, et on retourne un boolean.
    	return (bool) $this->_db->query('SELECT COUNT(*) FROM characters WHERE id ='.$info)->fetchColumn();
    }
    // Sinon c'est qu'on veut faire ce test avec le nom
    $req = $this->_db->prepare('SELECT COUNT(*) FROM characters WHERE name = :name');
    $req->bindValue(':name', $info, PDO::PARAM_STR);
    $req->execute();
    return (bool) $req->fetchColumn();
  }
  
  public function get($info)
  {
    // Si le paramètre est un entier, on veut récupérer le personnage avec son identifiant.
    if (is_int($info)){
      // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
    	$req = $this->_db->query('SELECT id, name, damages, exp, level FROM characters WHERE id =' .$info);
    	$data = $req->fetch(PDO::FETCH_ASSOC);

    	return new Char($data);
    }
    // Sinon, on veut récupérer le personnage avec son nom.
    else{
    	// Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
    	$req = $this->_db->prepare('SELECT id, name, damages, exp, level FROM characters WHERE name = :name');
    	$req->bindValue(':name',$info);
    	$req->execute();

    	$data = $req->fetch(PDO::FETCH_ASSOC);

    	return new Char($data);
    }
  }
  
  public function getList($name)
  {
    // Retourne la liste des personnages dont le nom n'est pas $nom.
    $req = $this->_db->prepare('SELECT * FROM characters WHERE name <> :name ORDER BY name');
    $req->bindValue(':name',$name);
    $req->execute();
    // Le résultat sera un tableau d'instances de Personnage.
    while ($data = $req->fetch(PDO::FETCH_ASSOC)){
    	$char[] = new Char($data);
    }

    return $char;

  }
  
  public function update(Char $char)
  {
    // Prépare une requête de type UPDATE.
    $req = $this->_db->prepare('UPDATE characters SET
      damages = :damages,
      exp = :exp,
      level = :level 
      WHERE id = :id');
    // Assignation des valeurs à la requête.
    $req->bindValue(':damages',$char->damages(), PDO::PARAM_INT);
    $req->bindValue(':id',$char->id(), PDO::PARAM_INT);
    $req->bindValue(':exp',$char->exp(), PDO::PARAM_INT);
    $req->bindValue(':level',$char->level(), PDO::PARAM_INT);

    $req->execute();
    // Exécution de la requête.
    $req->execute();
  }
 
 
}