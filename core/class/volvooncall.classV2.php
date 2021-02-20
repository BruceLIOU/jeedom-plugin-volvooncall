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

  private function getListeDefaultCommandes()
  {
    return array(
      "fuelType"          => array('Type véhicule',       'info',  'string',     "", 0, 1, "GENERIC_INFO",   'core::badge', 'core::badge'),    "vehicleType"          => array('Modèle',       'info',  'string',     "", 0, 1, "GENERIC_INFO",   'core::badge', 'core::badge'),
      "odometer"             => array('Kilometrage',         'info',  'numeric',  "km", 1, 1, "GENERIC_INFO",   'core::badge', 'core::badge'),
      "hvBatteryLevel"        => array('Niveau batterie',     'info',  'numeric',   "%", 1, 1, "GENERIC_INFO",   'volvooncall::battery_status_mmi', 'volvooncall::battery_status_mmi'),
      "distanceToHVBatteryEmpty"     => array('Autonomie',           'info',  'numeric',  "km", 1, 1, "GENERIC_INFO",   'core::badge', 'core::badge'),
      "gps_position"         => array('Position GPS',        'info',  'string',     "", 0, 1, "GENERIC_INFO",   'volvooncall::opensmap',   'volvooncall::opensmap'),
      "gps_position_lat"     => array('Position GPS Lat.',   'info',  'string',     "", 0, 0, "GENERIC_INFO",   'core::badge', 'core::badge'),
      "gps_position_lon"     => array('Position GPS Lon.',   'info',  'string',     "", 0, 0, "GENERIC_INFO",   'core::badge', 'core::badge'),
      "gps_dist_home"        => array('Distance maison',     'info',  'numeric',  "km", 1, 1, "GENERIC_INFO",   'core::line', 'core::line'),
      "connectionStatus"           => array('Niveau connection',   'info',  'numeric',    "", 1, 1, "GENERIC_INFO",   'volvooncall::con_level',  'volvooncall::con_level'),
      "record_period"        => array('Période enregistrement', 'info', 'numeric',    "", 1, 0, "GENERIC_INFO",   'core::badge', 'core::badge'),
      "hvBatteryChargeStatusDerived"     => array('Prise connectée',     'info',  'binary',     "", 1, 1, "GENERIC_INFO",   'volvooncall::plugged', 'volvooncall::plugged'),
      "hvBatteryChargeStatus" => array('Statut charge',       'info',  'string',     "", 1, 1, "GENERIC_INFO",   'core::badge', 'core::badge'),
      "timeToHVBatteryFullyCharged"    => array('Fin de charge',       'info',  'string',     "", 0, 1, "GENERIC_INFO",   'core::badge', 'core::badge'),
      "preclimatizationSupported"       => array('Etat climatisation',  'info',  'binary',     "", 1, 1, "GENERIC_INFO",   'volvooncall::clim', 'volvooncall::clim'),
      // Informations complémentaires pour vehicule hybride                               
      "fuelAmountLevel"      => array('Niveau carburant',    'info',  'numeric',   "%", 1, 1, "GENERIC_INFO",   'core::badge', 'core::badge'),
      "distanceToEmpty"       => array('Autonomie carburant', 'info',  'numeric',  "km", 1, 1, "GENERIC_INFO",   'core::badge', 'core::badge'),
    );
  }
  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
  public function postSave()
  {
    // Login API
    $VocUsername = config::byKey('VocUsername', 'volvooncall');
    $VocPassword = config::byKey('VocPassword', 'volvooncall');
    $VocRegion = config::byKey('VocRegion', 'volvooncall');

    $session_volvooncall = new vocapi();

    $account = $session_volvooncall->getAccount();   // Authentification

    if (!$account) {
      log::add('volvooncall', 'error', "Erreur Login");
      return;  // Erreur de login API VOLVO
    }

    $vin = $session_volvooncall->getVin();
    if (!$vin) {
      log::add('volvooncall', 'error', "Erreur VIN");
      return;  // Erreur de VIN API VOLVO
    }

    // creation de la liste des commandes / infos
    foreach ($this->getListeDefaultCommandes() as $id => $data) {
      list($name, $type, $subtype, $unit, $hist, $visible, $generic_type, $template_dashboard, $template_mobile) = $data;
      $cmd = $this->getCmd(null, $id);
      if (!is_object($cmd)) {
        $cmd = new volvooncallCmd();
        $cmd->setName($name);
        $cmd->setEqLogic_id($this->getId());
        $cmd->setType($type);
        if ($type == "info") {
          $cmd->setDisplay("showStatsOndashboard", 0);
          $cmd->setDisplay("showStatsOnmobile", 0);
        }
        $cmd->setSubType($subtype);
        $cmd->setUnite($unit);
        $cmd->setLogicalId($id);
        $cmd->setIsHistorized($hist);
        $cmd->setIsVisible($visible);
        $cmd->setDisplay('generic_type', $generic_type);
        $cmd->setTemplate('dashboard', $template_dashboard);
        $cmd->setTemplate('mobile', $template_mobile);
        if ($id == "gps_position") {
          // Création des parametres de suivi des trajets
          $cmd->setConfiguration('trip_start_ts', 0);
          $cmd->setConfiguration('trip_start_mileage',  0);
          $cmd->setConfiguration('trip_start_battlevel', 0);
          $cmd->setConfiguration('trip_in_progress', 0);
          $cmd->save();
        } else {
          $cmd->save();
        }
      } else {
        $cmd->setType($type);
        if ($type == "info") {
          $cmd->setDisplay("showStatsOndashboard", 0);
          $cmd->setDisplay("showStatsOnmobile", 0);
        }
        $cmd->setSubType($subtype);
        $cmd->setUnite($unit);
        $cmd->setIsHistorized($hist);
        $cmd->setIsVisible($visible);
        $cmd->setDisplay('generic_type', $generic_type);
        if ($id == "gps_position") {
          // Création des parametres de suivi des trajets
          $cmd->setConfiguration('trip_start_ts', 0);
          $cmd->setConfiguration('trip_start_mileage',  0);
          $cmd->setConfiguration('trip_start_battlevel', 0);
          $cmd->setConfiguration('trip_in_progress', 0);
          $cmd->save();
        } else {
          $cmd->save();
        }
      }
    }

    // ajout de la commande refresh data
    $refresh = $this->getCmd(null, 'refresh');
    if (!is_object($refresh)) {
      $refresh = new volvooncallCmd();
      $refresh->setName(__('Rafraichir', __FILE__));
    }
    $refresh->setEqLogic_id($this->getId());
    $refresh->setLogicalId('refresh');
    $refresh->setType('action');
    $refresh->setSubType('other');
    $refresh->save();
    log::add('volvooncall', 'debug', 'postSave:Ajout ou Mise à jour véhicule:' . $vin);

    $vin_dir = dirname(__FILE__) . CARS_FILES_DIR . $vin;
    if (!file_exists($vin_dir)) {
      mkdir($vin_dir, 0777);
    }
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement 
  public function preRemove()
  {
  }

  // Fonction appelée au rythme de 1 mn (recupeartion des informations courantes de la voiture)
  // ==========================================================================================
  public static function pull()
  {
    log::add('volvooncall_map', 'debug', 'Funcion pull');
    if (config::byKey('VocUsername', 'volvooncall') != "" || config::byKey('VocPassword', 'volvooncall') != "") {
      log::add('volvooncall', 'debug', 'Mise à jour périodique');
      foreach (self::byType('volvooncall') as $eqLogic) {
        $eqLogic->periodic_state(0);
      }
    }
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement 
  public function postRemove()
  {
  }

  /*
     * Non obligatoire : permet de modifier l'affichage du widget (également utilisable par les commandes)
      public function toHtml($_version = 'dashboard') {

      }
     */

  /*
     * Non obligatoire : permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

  /*
     * Non obligatoire : permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

  /*     * **********************Getteur Setteur*************************** */
  // Lecture des statut du vehicule connecté
  public function periodic_state($rfh)
  {
    // V1 : API Connected car V3
    $minute = intval(date("i"));
    $heure  = intval(date("G"));
    // Appel API pour le statut courant du vehicule
    $session_volvooncall = new vocapi();
    $vin = $session_volvooncall->getVin();

    $fn_car_gps   = dirname(__FILE__) . CARS_FILES_DIR . $vin . '/gps.log';
    $fn_car_trips = dirname(__FILE__) . CARS_FILES_DIR . $vin . '/trips.log';

    if ($this->getIsEnable()) {
      $cmd_record_period = $this->getCmd(null, "record_period");
      $record_period = $cmd_record_period->execCmd();
      if ($record_period == NULL)
        $record_period = 0;
      //log::add('volvooncall','debug',"record_period:".$record_period);

      if ((($record_period == 0) && ($minute % 5 == 0)) || ($record_period > 0) || ($rfh == 1)) {
        if ($rfh == 1)

        // Capture du statut du vehicule
        $retA = $session_volvooncall->getAttributes($vin);
        $retS = $session_volvooncall->getStatus($vin);
        $retP = $session_volvooncall->getPosition($vin, null);

        $veh_type = $retA["fuelType"];
        log::add('volvooncall', 'debug', "MAJ statut du véhicule:" . $vin);
        $cmd_mlg = $this->getCmd(null, "odometer");
        $mileage = $retS["odometer"];
        $previous_mileage = $cmd_mlg->execCmd();
        $previous_ts = $cmd_mlg->getConfiguration('prev_ctime');
        $cmd_mlg->event($mileage);
        $cmd = $this->getCmd(null, "hvBatteryLevel");
        $batt_level = $retS["hvBatteryLevel"];
        $previous_batt_level = $cmd->execCmd();
        $cmd->event($batt_level);
        $cmd = $this->getCmd(null, "distanceToHVBatteryEmpty");
        $batt_auto = $retS["distanceToHVBatteryEmpty"];
        $cmd->event($batt_auto);
        // infos complementaires pour vehicule hybride
        if ($veh_type == "HEV") {
          $cmd = $this->getCmd(null, "fuelAmountLevel");
          $fuel_level = intval($retS["fuelAmountLevel"]);
          if ($fuel_level != 0) {
            $cmd->event($fuel_level);
          }
          $cmd = $this->getCmd(null, "distanceToEmpty");
          $fuel_auto = $ret["distanceToEmpty"];
          $cmd->event($fuel_auto);
        }
        // Etat courant du trajet
        $cmd_gps = $this->getCmd(null, "gps_position");
        $trip_start_ts       = $cmd_gps->getConfiguration('trip_start_ts');
        $trip_start_mileage  = $cmd_gps->getConfiguration('trip_start_mileage');
        $trip_start_battlevel = $cmd_gps->getConfiguration('trip_start_battlevel');
        $trip_in_progress    = $cmd_gps->getConfiguration('trip_in_progress');
        if (($retP["position"]["latitude"] == 0) && ($ret["position"]["longitude"] == 0))
          $gps_pts_ok = false; // points GPS non valide
        else
          $gps_pts_ok = true;
        if ($gps_pts_ok == true) {
          $gps_position = $retP["position"]["latitude"] . "," . $retP["position"]["longitude"];
          $previous_gps_position = $cmd_gps->execCmd();
          //log::add('volvooncall','debug',"Refresh log previous_gps_position=".$previous_gps_position);
          $cmd_gps->event($gps_position);
          $cmd_gpslat = $this->getCmd(null, "gps_position_lat");
          $cmd_gpslat->event($retP["position"]["latitude"]);
          $cmd_gpslon = $this->getCmd(null, "gps_position_lon");
          $cmd_gpslon->event($retP["position"]["longitude"]);
          // Calcul distance maison
          $lat_home = deg2rad(floatval(config::byKey("info::latitude")));
          $lon_home = deg2rad(floatval(config::byKey("info::longitude")));
          $lat_veh = deg2rad(floatval($retP["position"]["latitude"]));
          $lon_veh = deg2rad(floatval($retP["position"]["longitude"]));
          $dist = 6371.01 * acos(sin($lat_home) * sin($lat_veh) + cos($lat_home) * cos($lat_veh) * cos($lon_home - $lon_veh)); // calcul de la distance
          $dist = number_format($dist, 3, '.', ''); //formatage 3 décimales
          $cmd_dis_home = $this->getCmd(null, "gps_dist_home");
          $cmd_dis_home->event($dist);
        }
        // Autres infos
        $cmd = $this->getCmd(null, "connectionStatus");
        $conn_level = $retS["connectionStatus"];
        $cmd->event($conn_level);
        $cmd = $this->getCmd(null, "engineRunning");
        $kinetic_moving = intval($retS["engineRunning"], 10);
        $cmd->event($kinetic_moving);
        // Analyse debut et fin de trajet
        $ctime = time();
        $trip_event = 0;
        if ($trip_in_progress == 0) {
          // Pas de trajet en cours
          if ($kinetic_moving == 1) {
            // debut de trajet
            $trip_start_ts       = $previous_ts;
            $trip_start_mileage  = $previous_mileage;
            $trip_start_battlevel = $previous_batt_level;
            $trip_in_progress    = 1;
            $trip_event = 1;
            $cmd_gps->setConfiguration('trip_start_ts', $trip_start_ts);
            $cmd_gps->setConfiguration('trip_start_mileage', $trip_start_mileage);
            $cmd_gps->setConfiguration('trip_start_battlevel', $trip_start_battlevel);
            $cmd_gps->setConfiguration('trip_in_progress', $trip_in_progress);
            $cmd_gps->save();
          }
        } else {
          // Un trajet est en cours
          if (($kinetic_moving == 0) && ($record_period == 1)) {
            // fin de trajet
            $trip_end_ts       = $ctime;
            $trip_end_mileage  = $mileage;
            $trip_end_battlevel = $batt_level;
            $trip_in_progress  = 0;
            $trip_event = 1;
            // enregistrement d'un trajet
            $trip_distance = $trip_end_mileage - $trip_start_mileage;
            $trip_batt_diff = $trip_start_battlevel - $trip_end_battlevel;
            $trip_log_dt = $trip_start_ts . "," . $trip_end_ts . "," . $trip_distance . "," . $trip_batt_diff . "\n";
            log::add('volvooncall', 'debug', "Refresh->recording Trip_dt=" . $trip_log_dt);
            file_put_contents($fn_car_trips, $trip_log_dt, FILE_APPEND | LOCK_EX);
            $cmd_gps->setConfiguration('trip_in_progress', $trip_in_progress);
            $cmd_gps->save();
          }
        }
        // Log position courante vers GPS log file
        //          if (($gps_position !== $previous_gps_position) || ($trip_event == 1)) {
        if ($gps_pts_ok == true) {
          $gps_log_dt = $ctime . "," . $gps_position . "," . $batt_level . "," . $mileage . "," . $kinetic_moving . "\n";
          log::add('volvooncall', 'debug', "Refresh->recording Gps_dt=" . $gps_log_dt);
          file_put_contents($fn_car_gps, $gps_log_dt, FILE_APPEND | LOCK_EX);
        }
        // enregistre le ts du point courant
        $cmd_mlg->setConfiguration('prev_ctime', $ctime);
        $cmd_mlg->save();
        //          }

        // Si le vehicule est en mouvement, passage en record toute les minutes, et au moins pour 5 mn
        if ($kinetic_moving == 1) {
          $record_period = 5;
        } else {
          if ($record_period > 0) {
            $record_period = $record_period - 1;
          }
        }
        $cmd_record_period->event($record_period);
        // Chargement batterie
        $cmd = $this->getCmd(null, "hvBatteryChargeStatusDerived");
        $charging_plugged = $retS["hvBattery"]["hvBatteryChargeStatusDerived"];
        $cmd->event($charging_plugged);
        $cmd = $this->getCmd(null, "hvBatteryChargeStatus");
        $charging_status = $retS["hvBattery"]["hvBatteryChargeStatus"];
        $cmd->event($charging_status);
        $cmd_et = $this->getCmd(null, "timeToHVBatteryFullyCharged");
        $charging_end_time = $retS["hvBattery"]["timeToHVBatteryFullyCharged"];
        if ((strtolower($charging_status) != "PlugRemoved")) {
          $cmd_et->event($charging_end_time);
        }
        $cmd = $this->getCmd(null, "preclimatizationSupported");
        $precond_status = ($retS["heater"]["status"] == "Off") ? 1 : 0;
        $cmd->event($precond_status);
      }
    }
  }
}

class volvooncallCmd extends cmd
{
  /*     * *************************Attributs****************************** */

  /*
      public static $_widgetPossibility = array();
    */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

  // Exécution d'une commande  
  public function execute($_options = array())
  {
    if ($this->getLogicalId() == 'refresh') {
        log::add('volvooncall','info',"Refresh data");
        if (config::byKey('VocUsername', 'volvooncall') != "" || config::byKey('VocPassword', 'volvooncall') != "" ) {
          foreach (eqLogic::byType('volvooncall') as $eqLogic) {
            $eqLogic->periodic_state(1);
          }
        }
      }
  }

  /*     * **********************Getteur Setteur*************************** */
}
