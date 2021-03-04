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

require_once dirname(__FILE__) . '/../../3rdparty/volvooncall_api.class.php';

define("CARS_FILES_DIR", "/../../data/");

global $cars_dt;
global $cars_dt_gps;
global $report;
global $car_infos;

// =====================================================
// Fonction de lecture de tous les trajets d'une voiture
// =====================================================
function get_car_trips($vin, $ts_start, $ts_end)
{
  $session_volvooncall = new volvooncall_api();
  $login = $session_volvooncall->login($this->getConfiguration('VocUsername'), $this->getConfiguration('VocPassword'));

  $info_trip = [];
  if ($login) {
    // Section caractéristiques véhicule
    $vin = $session_volvooncall->getVin();
    $retT = $session_volvooncall->getTrips($vin);

    if ($retT) {
      log::add('volvooncall','debug','get_car_trips:success='.$retT);
      $info_trip["dist"]                     = 0;
      $info_trip["elec"]                     = 0;
      $info_trip["fuel"]                     = 0;
    
      $info_trip["total"]                    = count($retT['trips']);

      foreach ($retT['trips'] as $trips)
      {
        $info_trip["dist"]                   += $trips['tripDetails'][0]['distance']/1000;
        $info_trip["elec"]                   += $trips['tripDetails'][0]['electricalConsumption']/1000;
        $info_trip["fuel"]                   += $trips['tripDetails'][0]['fuelConsumption']/1000;

        $info_trip["id"]                     = $trips['id'];
        $info_trip["startTime"]              = $trips['tripDetails'][0]['startTime'];
        $info_trip["startTimeF"]             = date_create($info_trip["startTime"]);
        $info_trip["endTime"]                = $trips['tripDetails'][0]['endTime'];
        $info_trip["endTimeF"]               = date_create($info_trip["endTime"] );
        $info_trip["duree"]                  = date_diff($info_trip["startTimeF"], $info_trip["endTimeF"]);
        $info_trip["dureeF"]                 = $info_trip["duree"]->format('%H:%I');

        $info_trip["villes"]                 = $trips['tripDetails'][0]['startPosition']['city'].'/'.$trips['tripDetails'][0]['endPosition']['city'];
        $info_trip["distance"]               = $trips['tripDetails'][0]['distance']/1000;
        $info_trip["electricalConsumption"]  = $trips['tripDetails'][0]['electricalConsumption']/1000;
        $info_trip["fuelConsumtion"]         = $trips['tripDetails'][0]['fuelConsumption']/1000;
        $info_trip["startpositionLat"]       = $trips['tripDetails'][0]['startPosition']['latitude'];
        $info_trip["startpositionLon"]       = $trips['tripDetails'][0]['startPosition']['longitude'];
        $info_trip["endPositionLat"]         = $trips['tripDetails'][0]['endPosition']['latitude'];
        $info_trip["endPositionLon"]         = $trips['tripDetails'][0]['endPosition']['longitude'];
      }

    }
    else {
      log::add('volvooncall','error',"get_car_trips:Erreur d'accès à l'API pour informations sur le véhicule");
    }
  }
  else {
    log::add('volvooncall','error',"get_car_trips:Erreur login API pour informations sur le véhicule");
  }

  return $info_trip;



}

// ===========================================================
// Fourniture des informations sur le véhicule (selon son VIN)
// ===========================================================
function get_car_infos($vin)
{
  $session_volvooncall = new volvooncall_api();
  $login = $session_volvooncall->login($this->getConfiguration('VocUsername'), $this->getConfiguration('VocPassword'));

  $info = [];
  if ($login) {
    // Section caractéristiques véhicule
    $vin = $session_volvooncall->getVin();
    $retA = $session_volvooncall->getAttributes($vin);
    $retS = $session_volvooncall->getStatus($vin);

    if ($retA && $retS) {
      log::add('volvooncall','debug','get_car_infos:success='.$retA);
      $info["vin"]                    = $vin;
      $info["vehicleType"]            = $retA["vehicleType"];
      $info["fuelType"]               = $retA['fuelType'];
      $info["modelYear"]              = $retA['modelYear'];
      $info["fuelTankVolume"]         = $retA['fuelTankVolume'];
      $info["registrationNumber"]     = $retA['registrationNumber'];

      $info["averageSpeed"]           = $retS['averageSpeed'];
      $info["averageFuelConsumption"] = $retS['averageFuelConsumption']/10;
      $info["tripMeter1"]             = $retS['tripMeter1']/1000;
      $info["tripMeter2"]             = $retS['tripMeter2']/1000;
    }
    else {
      log::add('volvooncall','error',"get_car_infos:Erreur d'accès à l'API pour informations sur le véhicule");
    }
  }
  else {
    log::add('volvooncall','error',"get_car_infos:Erreur login API pour informations sur le véhicule");
  }

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
    get_car_trips($vin, intval ($ts_start), intval ($ts_end));
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
