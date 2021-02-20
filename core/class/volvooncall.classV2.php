<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../3rdparty/vocapi.class.php';

define("CARS_FILES_DIR", "/../../data/");

class volvooncall extends eqLogic
{
  /*     * *************************Attributs****************************** */

  /*
   * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
   * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
	public static $_widgetPossibility = array();
   */

  /*     * ***********************Methode static*************************** */

  /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {
      }
     */

  /*
     * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
      public static function cron5() {
      }
     */

  /*
     * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
      public static function cron10() {
      }
     */

  /*
     * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
      public static function cron15() {
      }
     */

  /*
     * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
      public static function cron30() {
      }
     */

  /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {
      }
     */

  /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {
      }
     */



  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement 
  public function preInsert()
  {
  }

  // Fonction exécutée automatiquement après la création de l'équipement 
  public function postInsert()
  {
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement 
  public function preUpdate()
  {
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement 
  public function postUpdate()
  {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement 
  public function preSave()
  {
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
  public function postSave()
  {
		$type = $this->getCmd(null, 'fuelType');
    if (!is_object($type)) {
      $type = new volvooncallCmd();
      $type->setName('Type de véhicule');
    }
		$type->setLogicalId('fuelType');
		$type->setEqLogic_id($this->getId());
		$type->setType('info');
		$type->setSubType('string');
		$type->save();	

    $modele = $this->getCmd(null, 'vehicleType');
    if (!is_object($modele)) {
      $modele = new volvooncallCmd();
      $modele->setName('Modèle');
    }
		$modele->setLogicalId('vehicleType');
		$modele->setEqLogic_id($this->getId());
		$modele->setType('info');
		$modele->setSubType('string');
		$modele->save();	

    $kilometrage = $this->getCmd(null, 'odometer');
    if (!is_object($kilometrage)) {
      $kilometrage = new volvooncallCmd();
      $kilometrage->setName('Kilométrage');
    }
		$kilometrage->setLogicalId('odometer');
		$kilometrage->setEqLogic_id($this->getId());
		$kilometrage->setType('info');
		$kilometrage->setSubType('numeric');
    $kilometrage->setUnite('km');
		$kilometrage->save();

    $refresh = $this->getCmd(null, 'refresh');
    if (!is_object($refresh)) {
      $refresh = new volvooncallCmd();
      $refresh->setName('Rafraichir');
    }
    $refresh->setEqLogic_id($this->getId());
    $refresh->setLogicalId('refresh');
    $refresh->setType('action');
    $refresh->setSubType('other');
    $refresh->save();
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement 
  public function preRemove()
  {
  }


  // Fonction exécutée automatiquement après la suppression de l'équipement 
  public function postRemove()
  {
  }

  /*     * **********************Getteur Setteur*************************** */
  // Lecture des statut du vehicule connecté
  public function periodic_state()
  {
    // Appel API pour le statut courant du vehicule
    $session_volvooncall = new vocapi();
    $vin = $session_volvooncall->getVin();

    $retA = $session_volvooncall->getAttributes($vin);
    $retS = $session_volvooncall->getStatus($vin);
    
    if ($this->getIsEnable()) {
      $cmd = $this->getCmd(null, "fuelType");
      $fuelType = $retA["fuelType"];
      $cmd->event($fuelType);

      $cmd = $this->getCmd(null, "vehicleType");
      $vehicleType = $retA["vehicleType"];
      $cmd->event($vehicleType);

      $cmd = $this->getCmd(null, "odometer");
      $odometer = $retS["odometer"];
      $cmd->event($odometer);

 

    }
  }
}

class volvooncallCmd extends cmd
{
  // Exécution d'une commande  
  public function execute($_options = array())
  {
    if ($this->getLogicalId() == 'refresh') {
        log::add('volvooncall','info',"Refresh data");
          foreach (eqLogic::byType('volvooncall') as $eqLogic) {
            $eqLogic->periodic_state(1);
          }
      }
  }
  public function execute($_options = array()) {
		$eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
		switch ($this->getLogicalId()) {	//vérifie le logicalid de la commande 			
			case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm . 
				$info = $eqlogic->randomVdm(); 	//On lance la fonction randomVdm() pour récupérer une vdm et on la stocke dans la variable $info
				$eqlogic->checkAndUpdateCmd('story', $info); // on met à jour la commande avec le LogicalId "story"  de l'eqlogic 
				break;
		}
    }

  /*     * **********************Getteur Setteur*************************** */
}
