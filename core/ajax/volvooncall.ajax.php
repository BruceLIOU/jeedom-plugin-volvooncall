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

require_once dirname(__FILE__) . '/../../3rdparty/vocapi.class.php';

define("MYP_FILES_DIR",  "/../../data/MyVolvo/");
define("CARS_FILES_DIR", "/../../data/");

global $cars_dt;
global $cars_dt_gps;
global $report;
global $car_infos;

// =====================================================
// Fonction de lecture de tous les trajets d'une voiture
// =====================================================
function get_car_trips_gps($vin, $ts_start, $ts_end)
{
  global $cars_dt;
  
  // Lecture des trajets
  // -------------------
  // ouverture du fichier de log: trajets
  $fn_car = dirname(__FILE__).CARS_FILES_DIR.$vin.'/trips.log';
  $fcar = fopen($fn_car, "r");

  // lecture des donnees
  $line = 0;
  $cars_dt["trips"] = [];
  $first_ts = time();
  $last_ts  = 0;
  if ($fcar) {
    while (($buffer = fgets($fcar, 4096)) !== false) {
      // extrait les timestamps debut et fin du trajet
      list($tr_tss, $tr_tse, $tr_ds, $tr_batt) = explode(",", $buffer);
      $tsi_s = intval($tr_tss);
      $tsi_e = intval($tr_tse);
      // selectionne les trajets selon leur date depart&arrive
      if (($tsi_s>=$ts_start) && ($tsi_s<=$ts_end)) {
        $cars_dt["trips"][$line] = $buffer;
        $line = $line + 1;
        // Recherche des ts mini et maxi pour les trajets retenus
        if ($tsi_s<$first_ts)
          $first_ts = $tsi_s;
        if ($tsi_e>$last_ts)
          $last_ts = $tsi_e;
      }
    }
  }
  fclose($fcar);

  // Lecture des points GPS pour ces trajets
  // ---------------------------------------
  // ouverture du fichier de log: points GPS
  $fn_car = dirname(__FILE__).CARS_FILES_DIR.$vin.'/gps.log';
  $fcar = fopen($fn_car, "r");

  // lecture des donnees
  $line = 0;
  $cars_dt["gps"] = [];
  if ($fcar) {
    while (($buffer = fgets($fcar, 4096)) !== false) {
      // extrait les timestamps debut et fin du trajet
      list($pts_ts, $pts_lat, $pts_lon, $pts_alt, $pts_batt, $pts_mlg, $pts_moving) = explode(",", $buffer);
      $pts_tsi = intval($pts_ts);
      // selectionne les trajets selon leur date depart&arrive
      if (($pts_tsi>=$first_ts) && ($pts_tsi<=$last_ts)) {
        $cars_dt["gps"][$line] = $buffer;
        $line = $line + 1;
      }
    }
  }
  fclose($fcar);
  // Ajoute les coordonnées du domicile pour utilisation par javascript
  $latitute=config::byKey("info::latitude");
  $longitude=config::byKey("info::longitude");
  $cars_dt["home"] = $latitute.",".$longitude;

  //log::add('peugeotcars', 'debug', 'Ajax:get_car_trips:nb_lines'.$line);
  return;
}

// ===========================================================
// Fourniture des informations sur le véhicule (selon son VIN)
// ===========================================================
function get_car_infos($vin)
{
  $session_volvooncall = new vocapi();
  $session_volvooncall->login(config::byKey('VocUsername', 'volvooncall'), config::byKey('VocPassword', 'volvooncall'));
  $login_ctr = $session_volvooncall->getAccount();   // Authentification
  $info = [];
  if ($login_ctr) {
    // Recuperation de l'info type du vehicule
    // $eqLogic = eqLogic::byLogicalId($vin,'peugeotcars');
    // if (is_object($eqLogic)) {
      // $cmd = $eqLogic->getCmd(null, "veh_type");
      // if (is_object($cmd)) {
        // $veh_type = $cmd->execCmd();
      // }
    // }

    // Section caractéristiques véhicule
    $vin = $session_volvooncall->getVin();
    $ret = $session_volvooncall->getAttributes($vin);
    if ($ret) {
      log::add('volvooncall','debug','get_car_infos:success='.$ret);
      $info["vin"] = $vin;
      $info["short_label"] = $ret["vehicleType"];
      $info["veh_type"] = $ret['fuelType'];
    }
    else {
      log::add('volvooncall','error',"get_car_infos:Erreur d'accès à l'API pour informations sur le véhicule");
    }
  }
  else {
    log::add('volvooncall','error',"get_car_infos:Erreur login API pour informations sur le véhicule");
  }
  
  // Section version logiciels
  //log::add('volvooncall','debug','get_car_infos:liste_logiciel='.$liste_logiciel);

  return $info;
}

// =====================================
// Gestion des commandes recues par AJAX
// =====================================
try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

	ajax::init();

  if (init('action') == 'getTripData') {
    log::add('volvooncall', 'info', 'Ajax:getTripData');
    $vin = init('eqLogic_id');
    $ts_start = init('param')[0];
    $ts_end   = init('param')[1];
    log::add('volvooncall', 'debug', 'vin   :'.$vin);
    log::add('volvooncall', 'debug', 'param0:'.$ts_start);
    log::add('volvooncall', 'debug', 'param1:'.$ts_end);
    // Param 0 et 1 sont les timestamp de debut et fin de la periode de log demandée
    get_car_trips_gps($vin, intval ($ts_start), intval ($ts_end));
    $ret_json = json_encode ($cars_dt);
    ajax::success($ret_json);
    }

  else if (init('action') == 'getVehInfos') {
    log::add('volvooncall', 'info', 'Ajax:getVehInfos');
    $vin = init('eqLogic_id');
    $car_infos = get_car_infos($vin);
    $ret_json = json_encode ($car_infos);
    ajax::success($ret_json);
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
