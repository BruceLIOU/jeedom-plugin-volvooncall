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
require_once dirname(__FILE__) . '/../../3rdparty/volvooncall_api.class.php';

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
    $this->updateData();
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement 
  public function preSave()
  {
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
  public function postSave()
  {
    /* Informations générales sur le véhicule */
    /******************************************/
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

    /************** Attributes ****************/
    // Creation info VIN de véhicule
    $info = $this->getCmd(null, 'vin');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('VIN', __FILE__));
    }
    $info->setLogicalId('vin');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Type de véhicule
    $info = $this->getCmd(null, 'fuelType');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Type de véhicule', __FILE__));
    }
    $info->setLogicalId('fuelType');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Modèle du véhicule
    $info = $this->getCmd(null, 'vehicleType');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Modèle du véhicule', __FILE__));
    }
    $info->setLogicalId('vehicleType');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Année du véhicule
    $info = $this->getCmd(null, 'modelYear');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Année du véhicule', __FILE__));
    }
    $info->setLogicalId('modelYear');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->save();

    // Creation info Immatriculation du véhicule
    $info = $this->getCmd(null, 'registrationNumber');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Immatriculation du véhicule', __FILE__));
    }
    $info->setLogicalId('registrationNumber');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::immatriculation');
    $info->setTemplate('mobile', 'volvooncall::immatriculation');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Volume du réservoir
    $info = $this->getCmd(null, 'fuelTankVolume');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Volume du réservoir', __FILE__));
    }
    $info->setLogicalId('fuelTankVolume');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('l');
    $info->save();

    /* Informations sur le véhicule */
    /********************************/
    /************ Status ************/
    // Creation info Consommation moyenne de carburant
    $info = $this->getCmd(null, 'averageFuelConsumption');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Consommation moyenne de carburant', __FILE__));
    }
    $info->setLogicalId('averageFuelConsumption');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::number');
    $info->setTemplate('mobile', 'volvooncall::number');
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('l/100km');
    $info->save();

    // Creation info Vitesse moyenne
    $info = $this->getCmd(null, 'averageSpeed');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Vitesse moyenne', __FILE__));
    }
    $info->setLogicalId('averageSpeed');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('km/h');
    $info->save();

    // Creation info Lave-glace
    /* @return Normal */
    $info = $this->getCmd(null, 'brakeFluid');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Lave-glace', __FILE__));
    }
    $info->setLogicalId('brakeFluid');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::normal');
    $info->setTemplate('mobile', 'volvooncall::normal');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Vérrouillage du véhicule
    /* @return bool */
    $info = $this->getCmd(null, 'carLocked');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Véhicule vérrouillé', __FILE__));
    }
    $info->setLogicalId('carLocked');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::normal');
    $info->setTemplate('mobile', 'volvooncall::normal');
    $info->setType('info');
    $info->setSubType('binary');
    $info->save();

    // Creation info Status de connection
    /* @return ConnectedWithPower */
    $info = $this->getCmd(null, 'connectionStatus');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Status de connection', __FILE__));
    }
    $info->setLogicalId('connectionStatus');
    $info->setIsVisible(0);
    $info->setEqLogic_id($this->getId());
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Autonomie carburant
    $info = $this->getCmd(null, 'distanceToEmpty');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Autonomie carburant', __FILE__));
    }
    $info->setLogicalId('distanceToEmpty');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::number');
    $info->setTemplate('mobile', 'volvooncall::number');
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('km');
    $info->save();

    // Creation info Moteur en marche
    /* @return bool */
    $info = $this->getCmd(null, 'engineRunning');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Moteur en marche', __FILE__));
    }
    $info->setLogicalId('engineRunning');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('binary');
    $info->save();

    $info = $this->getCmd(null, 'record_period');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Période enregistrement', __FILE__));
    }
    $info->setLogicalId('record_period');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->save();

    // Creation info Carburant restant
    $info = $this->getCmd(null, 'fuelAmount');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Carburant restant', __FILE__));
    }
    $info->setLogicalId('fuelAmount');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('l');
    $info->save();

    // Creation info Carburant restant
    $info = $this->getCmd(null, 'fuelAmountLevel');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Niveau carburant restant', __FILE__));
    }
    $info->setLogicalId('fuelAmountLevel');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('%');
    $info->save();

    // Creation info Kilométrage
    $info = $this->getCmd(null, 'odometer');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Kilométrage', __FILE__));
    }
    $info->setLogicalId('odometer');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::number');
    $info->setTemplate('mobile', 'volvooncall::number');
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('km');
    $info->save();

    // Creation info Status de la climatisation à distance
    /* @return Charging */
    $info = $this->getCmd(null, 'remoteClimatizationStatus');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Status de la climatisation à distance', __FILE__));
    }
    $info->setLogicalId('remoteClimatizationStatus');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Status d'entretien
    /* @return Normal */
    $info = $this->getCmd(null, 'serviceWarningStatus');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Status d\'entretien', __FILE__));
    }
    $info->setLogicalId('serviceWarningStatus');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::normal');
    $info->setTemplate('mobile', 'volvooncall::normal');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info TM
    $info = $this->getCmd(null, 'tripMeter1');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('TM', __FILE__));
    }
    $info->setLogicalId('tripMeter1');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('km');
    $info->save();

    // Creation info TA
    $info = $this->getCmd(null, 'tripMeter2');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('TA', __FILE__));
    }
    $info->setLogicalId('tripMeter2');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('km');
    $info->save();

    // Creation info Niveau de lave-glace
    /* @return Normal */
    $info = $this->getCmd(null, 'washerFluidLevel');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Niveau de lave-glace', __FILE__));
    }
    $info->setLogicalId('washerFluidLevel');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::normal');
    $info->setTemplate('mobile', 'volvooncall::normal');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Portes ouvertes
    /* @return array */
    $info = $this->getCmd(null, 'doorsAll');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Portes ouvertes', __FILE__));
    }
    $info->setLogicalId('doorsAll');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::etat');
    $info->setTemplate('mobile', 'volvooncall::etat');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'frontRightDoorOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Porte avant droite', __FILE__));
    }
    $info->setLogicalId('frontRightDoorOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'frontLeftDoorOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Porte avant gauche', __FILE__));
    }
    $info->setLogicalId('frontLeftDoorOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'rearRightDoorOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Porte arrière droite', __FILE__));
    }
    $info->setLogicalId('rearRightDoorOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'rearLeftDoorOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Porte arrière gauche', __FILE__));
    }
    $info->setLogicalId('rearLeftDoorOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'tailgateOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Coffre', __FILE__));
    }
    $info->setLogicalId('tailgateOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'hoodOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Capot', __FILE__));
    }
    $info->setLogicalId('hoodOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Status de la climatisation
    /* @return array */
    /* @ return [] status -> off */
    $info = $this->getCmd(null, 'heater_status');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Status de la climatisation', __FILE__));
    }
    $info->setLogicalId('heater_status');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Status du cable de chargement
    /* @return CablePluggedInCar_FullyCharged */
    $info = $this->getCmd(null, 'hvBatteryChargeStatusDerived');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Status du cable de chargement', __FILE__));
    }
    $info->setLogicalId('hvBatteryChargeStatusDerived');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::plugged');
    $info->setTemplate('mobile', 'volvooncall::plugged');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Status de la charge
    /* @return ChargeEnd */
    $info = $this->getCmd(null, 'hvBatteryChargeStatus');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Status de la charge', __FILE__));
    }
    $info->setLogicalId('hvBatteryChargeStatus');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::chargement');
    $info->setTemplate('mobile', 'volvooncall::chargement');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Niveau de la batterie
    $info = $this->getCmd(null, 'hvBatteryLevel');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Niveau de la batterie', __FILE__));
    }
    $info->setLogicalId('hvBatteryLevel');
    $info->setEqLogic_id($this->getId());
    $info->setIsHistorized(0);
    $info->setIsVisible(1);
    $info->setTemplate('dashboard', 'volvooncall::battery_status');
    $info->setTemplate('mobile', 'volvooncall::battery_status');
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('%');
    $info->save();

    // Creation info Autonomie de la batterie
    $info = $this->getCmd(null, 'distanceToHVBatteryEmpty');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Autonomie de la batterie', __FILE__));
    }
    $info->setLogicalId('distanceToHVBatteryEmpty');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->setUnite('km');
    $info->save();

    // Creation info Heure de fin de chargement
    $info = $this->getCmd(null, 'timeToHVBatteryFullyCharged');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Heure de fin de chargement', __FILE__));
    }
    $info->setLogicalId('timeToHVBatteryFullyCharged');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::heure-chargement');
    $info->setTemplate('mobile', 'volvooncall::heure-chargement');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Pression des pneus
    /* @return Normal */
    $info = $this->getCmd(null, 'tyrePressureAll');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Pression des pneus', __FILE__));
    }
    $info->setLogicalId('tyrePressureAll');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'frontLeftTyrePressure');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Pression pneu avant gauche', __FILE__));
    }
    $info->setLogicalId('frontLeftTyrePressure');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'frontRightTyrePressure');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Pression pneu avant droit', __FILE__));
    }
    $info->setLogicalId('frontRightTyrePressure');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'rearLeftTyrePressure');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Pression pneu arrière gauche', __FILE__));
    }
    $info->setLogicalId('rearLeftTyrePressure');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'rearRightTyrePressure');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Pression pneu arrière droit', __FILE__));
    }
    $info->setLogicalId('rearRightTyrePressure');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Fenêtres ouvertes
    /* @return Bool */
    $info = $this->getCmd(null, 'windowsAll');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Fenêtres ouvertes', __FILE__));
    }
    $info->setLogicalId('windowsAll');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::etat');
    $info->setTemplate('mobile', 'volvooncall::etat');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'frontLeftWindowOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Fenêtre avant gauche', __FILE__));
    }
    $info->setLogicalId('frontLeftWindowOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'frontRightWindowOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Fenêtre avant droite', __FILE__));
    }
    $info->setLogicalId('frontRightWindowOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'rearLeftWindowOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Fenêtre arrière gauche', __FILE__));
    }
    $info->setLogicalId('rearLeftWindowOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'rearRightWindowOpen');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Fenêtre arrière droite', __FILE__));
    }
    $info->setLogicalId('rearRightWindowOpen');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::ouvertes_fermees');
    $info->setTemplate('mobile', 'volvooncall::ouvertes_fermees');
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    /************ Position ************/
    // Creation info Position GPS
    $info = $this->getCmd(null, 'position_gps');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Position GPS', __FILE__));
    }
    $info->setLogicalId('position_gps');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(1);
    $info->setIsHistorized(0);
    $info->setTemplate('dashboard', 'volvooncall::opensmap');
    $info->setTemplate('mobile', 'volvooncall::opensmap');
    $info->setConfiguration('trip_start_ts', 0);
    $info->setConfiguration('trip_start_mileage',  0);
    $info->setConfiguration('trip_start_battlevel', 0);
    $info->setConfiguration('trip_in_progress', 0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'position_gps_lat');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Position GPS LAT', __FILE__));
    }
    $info->setLogicalId('position_gps_lat');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    $info = $this->getCmd(null, 'position_gps_lon');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Position GPS LON', __FILE__));
    }
    $info->setLogicalId('position_gps_lon');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('string');
    $info->save();

    // Creation info Position GPS(lat)
    $info = $this->getCmd(null, 'latitude');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Position GPS(lat)', __FILE__));
    }
    $info->setLogicalId('latitude');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->save();

    // Creation info Position GPS(lon)
    $info = $this->getCmd(null, 'longitude');
    if (!is_object($info)) {
      $info = new volvooncallCmd();
      $info->setName(__('Position GPS(lon)', __FILE__));
    }
    $info->setLogicalId('longitude');
    $info->setEqLogic_id($this->getId());
    $info->setIsVisible(0);
    $info->setIsVisible(0);
    $info->setIsHistorized(0);
    $info->setType('info');
    $info->setSubType('numeric');
    $info->save();
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement 
  public function preRemove()
  {
  }

  // Fonction appelée au rythme de 1 mn (recupeartion des informations courantes de la voiture)
  // ==========================================================================================
  public static function pull()
  {
    log::add('volvooncall', 'debug', 'Funcion pull');

    log::add('volvooncall', 'debug', 'Mise à jour périodique');
    foreach (self::byType('volvooncall') as $eqLogic) {
      $eqLogic->updateData();
      $eqLogic->record_pts_gps();
    }
    $eqLogic->trips();
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement 
  public function postRemove()
  {
  }

  public function trips()
  {
    $session_volvooncall = new volvooncall_api();

    $login = $session_volvooncall->login($this->getConfiguration('VocUsername'), $this->getConfiguration('VocPassword'));

    //Vérification des identifiants
    if ($login != true) {
      log::add('volvooncall', 'error', "Erreur Login");
      return;  // Erreur de login API VOLVO
    }

    $vin = $session_volvooncall->getVin();
    log::add('volvooncall', 'debug', 'Trips VIN : ' . $vin);

    $vin_dir = dirname(__FILE__) . CARS_FILES_DIR . $vin;
    if (!file_exists($vin_dir)) {
      mkdir($vin_dir, 0777);
    }

    // Appel de l'API
    $retT = $session_volvooncall->getTrips($vin);

    $jsonFile = json_encode($retT, JSON_PRETTY_PRINT);

    /*
    echo '<pre>';
    var_dump($retT['trips']);
    echo '</pre>';
    */

    file_put_contents($vin_dir . '/trips.json', $jsonFile);
  }

  public function updateData()
  {
    /*Récupération des informations du véhicule */

    $session_volvooncall = new volvooncall_api();

    $login = $session_volvooncall->login($this->getConfiguration('VocUsername'), $this->getConfiguration('VocPassword'));

    //Vérification des identifiants
    if ($login != true) {
      log::add('volvooncall', 'error', "Erreur Login");
      return;  // Erreur de login API VOLVO
    }

    $vin = $session_volvooncall->getVin();

    log::add('volvooncall', 'debug', 'VIN : ' . $vin);

    // Appel de l'API
    $retA = $session_volvooncall->getAttributes($vin);
    $retS = $session_volvooncall->getStatus($vin);
    $retP = $session_volvooncall->getPosition($vin, null);

    log::add('volvooncall', 'debug', 'URL : ' . $session_volvooncall->getTestUrl());

    $doorsFR    = $retS["doors"]["frontRightDoorOpen"];
    if ($doorsFR != true) {
      $doorsFRO = 1;
    } else {
      $doorsFRO = 0;
    }
    $doorsFL    = $retS["doors"]["frontLeftDoorOpen"];
    if ($doorsFL != true) {
      $doorsFLO = 1;
    } else {
      $doorsFLO = 0;
    }
    $doorsRR    = $retS["doors"]["rearRightDoorOpen"];
    if ($doorsRR != true) {
      $doorsRRO = 1;
    } else {
      $doorsRRO = 0;
    }
    $doorsRL    = $retS["doors"]["rearLeftDoorOpen"];
    if ($doorsRL != true) {
      $doorsRLO = 1;
    } else {
      $doorsRLO = 0;
    }
    $doorsTG    = $retS["doors"]["tailgateOpen"];
    if ($doorsTG != true) {
      $doorsTGO = 1;
    } else {
      $doorsTGO = 0;
    }
    $doorsH     = $retS["doors"]["hoodOpen"];
    if ($doorsH != true) {
      $doorsHO = 1;
    } else {
      $doorsHO = 0;
    }

    if (($doorsFRO + $doorsFLO + $doorsRRO + $doorsRLO + $doorsTGO + $doorsHO) == 6) {
      $doorsAllState = "Toutes les portes sont fermées";
    } else {
      $doorsAllState = "Toutes les portes ne sont pas fermées";
    }

    $windowsFR    = $retS["windows"]["frontRightWindowOpen"];
    if ($windowsFR != true) {
      $windowsFRO = 1;
    } else {
      $windowsFRO = 0;
    }
    $windowsFL    = $retS["windows"]["frontLeftWindowOpen"];
    if ($windowsFL != true) {
      $windowsFLO = 1;
    } else {
      $windowsFLO = 0;
    }
    $windowsRR    = $retS["windows"]["rearRightWindowOpen"];
    if ($windowsRR != true) {
      $windowsRRO = 1;
    } else {
      $windowsRRO = 0;
    }
    $windowsRL    = $retS["windows"]["rearLeftWindowOpen"];
    if ($windowsRL != true) {
      $windowsRLO = 1;
    } else {
      $windowsRLO = 0;
    }

    if (($windowsFRO + $windowsFLO + $windowsRRO + $windowsRLO) == 4) {
      $windowsAllState = "Toutes les fenêtres sont fermées";
    } else {
      $windowsAllState = "Toutes les fenêtres ne sont pas fermées";
    }

    $tyrePressureFR    = $retS["tyrePressure"]["frontRightTyrePressure"];
    if ($tyrePressureFR == "Normal") {
      $tyrePressureFRO = 1;
    } else {
      $tyrePressureFRO = 0;
    }
    $tyrePressureFL    = $retS["tyrePressure"]["frontLeftTyrePressure"];
    if ($tyrePressureFL == "Normal") {
      $tyrePressureFLO = 1;
    } else {
      $tyrePressureFLO = 0;
    }
    $tyrePressureRR    = $retS["tyrePressure"]["rearRightTyrePressure"];
    if ($tyrePressureRR == "Normal") {
      $tyrePressureRRO = 1;
    } else {
      $tyrePressureRRO = 0;
    }
    $tyrePressureRL    = $retS["tyrePressure"]["rearLeftTyrePressure"];
    if ($tyrePressureRL == "Normal") {
      $tyrePressureRLO = 1;
    } else {
      $tyrePressureRLO = 0;
    }

    if (($tyrePressureFRO + $tyrePressureFLO + $tyrePressureRRO + $tyrePressureRLO) == 4) {
      $tyrePressureAllState = "La pression des 4 pneus est bonne";
    } else {
      $tyrePressureAllState = "La pression de l'un des pneus n'est pas bonne";
    }
    $positionGPS = $retP["position"]["latitude"] . "," . $retP["position"]["longitude"];
    try {
      $this->checkAndUpdateCmd('VIN', $vin);
      log::add('volvooncall', 'debug', 'key : VIN valeur : ' . $vin);
      $this->checkAndUpdateCmd('fuelType', $retA["fuelType"]);
      log::add('volvooncall', 'debug', 'key : fuelType valeur : ' . $retA["fuelType"]);
      $this->checkAndUpdateCmd('vehicleType', $retA["vehicleType"]);
      log::add('volvooncall', 'debug', 'key : vehicleType valeur : ' . $retA["vehicleType"]);
      $this->checkAndUpdateCmd('modelYear', $retA["modelYear"]);
      log::add('volvooncall', 'debug', 'key : modelYear valeur : ' . $retA["modelYear"]);
      $this->checkAndUpdateCmd('registrationNumber', $retA["registrationNumber"]);
      log::add('volvooncall', 'debug', 'key : registrationNumber valeur : ' . $retA["registrationNumber"]);
      $this->checkAndUpdateCmd('fuelTankVolume', $retA["fuelTankVolume"]);
      log::add('volvooncall', 'debug', 'key : fuelTankVolume valeur : ' . $retA["fuelTankVolume"]);
      $this->checkAndUpdateCmd('averageFuelConsumption', ($retS["averageFuelConsumption"] / 10));
      log::add('volvooncall', 'debug', 'key : averageFuelConsumption valeur : ' . ($retS["averageFuelConsumption"] / 10));
      $this->checkAndUpdateCmd('averageSpeed', $retS["averageSpeed"]);
      log::add('volvooncall', 'debug', 'key : averageSpeed valeur : ' . $retS["averageSpeed"]);
      $this->checkAndUpdateCmd('brakeFluid', $retS["brakeFluid"]);
      log::add('volvooncall', 'debug', 'key : brakeFluid valeur : ' . $retS["brakeFluid"]);
      $this->checkAndUpdateCmd('carLocked', $retS["carLocked"]);
      log::add('volvooncall', 'debug', 'key : carLocked valeur : ' . $retS["carLocked"]);
      $this->checkAndUpdateCmd('connectionStatus', $retS["connectionStatus"]);
      log::add('volvooncall', 'debug', 'key : connectionStatus valeur : ' . $retS["connectionStatus"]);
      $this->checkAndUpdateCmd('distanceToEmpty', $retS["distanceToEmpty"]);
      log::add('volvooncall', 'debug', 'key : distanceToEmpty valeur : ' . $retS["distanceToEmpty"]);
      $this->checkAndUpdateCmd('engineRunning', $retS["engineRunning"]);
      log::add('volvooncall', 'debug', 'key : engineRunning valeur : ' . $retS["engineRunning"]);
      //$this->checkAndUpdateCmd('record_period', $record_period);
      //log::add('volvooncall', 'debug', 'key : record_period valeur : ' . $record_period);
      $this->checkAndUpdateCmd('fuelAmount', $retS["fuelAmount"]);
      log::add('volvooncall', 'debug', 'key : fuelAmount valeur : ' . $retS["fuelAmount"]);
      $this->checkAndUpdateCmd('fuelAmountLevel', $retS["fuelAmountLevel"]);
      log::add('volvooncall', 'debug', 'key : fuelAmountLevel valeur : ' . $retS["fuelAmountLevel"]);
      $this->checkAndUpdateCmd('odometer', ($retS["odometer"] / 1000));
      log::add('volvooncall', 'debug', 'key : odometer valeur : ' . ($retS["odometer"] / 1000));
      $this->checkAndUpdateCmd('remoteClimatizationStatus', $retS["remoteClimatizationStatus"]);
      log::add('volvooncall', 'debug', 'key : remoteClimatizationStatus valeur : ' . $retS["remoteClimatizationStatus"]);
      $this->checkAndUpdateCmd('serviceWarningStatus', $retS["serviceWarningStatus"]);
      log::add('volvooncall', 'debug', 'key : serviceWarningStatus valeur : ' . $retS["serviceWarningStatus"]);
      $this->checkAndUpdateCmd('tripMeter1', ($retS["tripMeter1"] / 1000));
      log::add('volvooncall', 'debug', 'key : tripMeter1 valeur : ' . ($retS["tripMeter1"] / 1000));
      $this->checkAndUpdateCmd('tripMeter2', ($retS["tripMeter2"] / 1000));
      log::add('volvooncall', 'debug', 'key : tripMeter2 valeur : ' . ($retS["tripMeter2"] / 1000));
      $this->checkAndUpdateCmd('washerFluidLevel', $retS["washerFluidLevel"]);
      log::add('volvooncall', 'debug', 'key : washerFluidLevel valeur : ' . $retS["washerFluidLevel"]);
      $this->checkAndUpdateCmd('heater_status', $retS["heater"]["status"]);
      log::add('volvooncall', 'debug', 'key : heater_status valeur : ' . $retS["heater"]["status"]);
      $this->checkAndUpdateCmd('hvBatteryChargeStatusDerived', $retS["hvBattery"]["hvBatteryChargeStatusDerived"]);
      log::add('volvooncall', 'debug', 'key : hvBatteryChargeStatusDerived valeur : ' . $retS["hvBattery"]["hvBatteryChargeStatusDerived"]);
      $this->checkAndUpdateCmd('hvBatteryChargeStatus', $retS["hvBattery"]["hvBatteryChargeStatus"]);
      log::add('volvooncall', 'debug', 'key : hvBatteryChargeStatus valeur : ' . $retS["hvBattery"]["hvBatteryChargeStatus"]);
      $this->checkAndUpdateCmd('hvBatteryLevel', $retS["hvBattery"]["hvBatteryLevel"]);
      log::add('volvooncall', 'debug', 'key : hvBatteryLevel valeur : ' . $retS["hvBattery"]["hvBatteryLevel"]);
      $this->checkAndUpdateCmd('distanceToHVBatteryEmpty', $retS["hvBattery"]["distanceToHVBatteryEmpty"]);
      log::add('volvooncall', 'debug', 'key : distanceToHVBatteryEmpty valeur : ' . $retS["hvBattery"]["distanceToHVBatteryEmpty"]);
      $this->checkAndUpdateCmd('timeToHVBatteryFullyCharged', $retS["hvBattery"]["timeToHVBatteryFullyCharged"]);
      log::add('volvooncall', 'debug', 'key : timeToHVBatteryFullyCharged valeur : ' . $retS["hvBattery"]["timeToHVBatteryFullyCharged"]);
      $this->checkAndUpdateCmd('doorsAll', $doorsAllState);
      log::add('volvooncall', 'debug', 'key : doorsAll valeur : ' . $doorsAllState);
      $this->checkAndUpdateCmd('frontRightDoorOpen', $doorsFRO);
      log::add('volvooncall', 'debug', 'key : frontRightDoorOpen valeur : ' . $doorsFRO);
      $this->checkAndUpdateCmd('frontLeftDoorOpen', $doorsFLO);
      log::add('volvooncall', 'debug', 'key : frontLeftDoorOpen valeur : ' . $doorsFLO);
      $this->checkAndUpdateCmd('rearRightDoorOpen', $doorsRRO);
      log::add('volvooncall', 'debug', 'key : rearRightDoorOpen valeur : ' . $doorsRRO);
      $this->checkAndUpdateCmd('rearLeftDoorOpen', $doorsRLO);
      log::add('volvooncall', 'debug', 'key : rearLeftDoorOpen valeur : ' . $doorsRLO);
      $this->checkAndUpdateCmd('tailgateOpen', $doorsTGO);
      log::add('volvooncall', 'debug', 'key : tailgateOpen valeur : ' . $doorsTGO);
      $this->checkAndUpdateCmd('hoodOpen', $doorsHO);
      log::add('volvooncall', 'debug', 'key : hoodOpen valeur : ' . $doorsHO);
      $this->checkAndUpdateCmd('windowsAll', $windowsAllState);
      log::add('volvooncall', 'debug', 'key : windowsAll valeur : ' . $windowsAllState);
      $this->checkAndUpdateCmd('frontLeftWindowOpen', $windowsFLO);
      log::add('volvooncall', 'debug', 'key : frontLeftWindowOpen valeur : ' . $windowsFLO);
      $this->checkAndUpdateCmd('frontRightWindowOpen', $windowsFRO);
      log::add('volvooncall', 'debug', 'key : frontRightWindowOpen valeur : ' . $windowsFRO);
      $this->checkAndUpdateCmd('rearLeftWindowOpen', $windowsRLO);
      log::add('volvooncall', 'debug', 'key : rearLeftWindowOpen valeur : ' . $windowsRLO);
      $this->checkAndUpdateCmd('rearRightWindowOpen', $windowsRRO);
      log::add('volvooncall', 'debug', 'key : rearRightWindowOpen valeur : ' . $windowsRRO);
      $this->checkAndUpdateCmd('tyrePressureAll', $tyrePressureAllState);
      log::add('volvooncall', 'debug', 'key : tyrePressureAll valeur : ' . $tyrePressureAllState);
      $this->checkAndUpdateCmd('frontLeftTyrePressure', $tyrePressureFLO);
      log::add('volvooncall', 'debug', 'key : frontLeftTyrePressure valeur : ' . $tyrePressureFLO);
      $this->checkAndUpdateCmd('frontRightTyrePressure', $tyrePressureFRO);
      log::add('volvooncall', 'debug', 'key : frontRightTyrePressure valeur : ' . $tyrePressureFRO);
      $this->checkAndUpdateCmd('rearLeftTyrePressure', $tyrePressureRLO);
      log::add('volvooncall', 'debug', 'key : rearLeftTyrePressure valeur : ' . $tyrePressureRLO);
      $this->checkAndUpdateCmd('rearRightTyrePressure', $tyrePressureRRO);
      log::add('volvooncall', 'debug', 'key : rearRightTyrePressure valeur : ' . $tyrePressureRRO);
      $this->checkAndUpdateCmd('position_gps', $positionGPS);
      log::add('volvooncall', 'debug', 'key : position_gps valeur : ' . $positionGPS);
      $this->checkAndUpdateCmd('latitude', $retP["position"]["latitude"]);
      log::add('volvooncall', 'debug', 'key : latitude valeur : ' . $retP["position"]["latitude"]);
      $this->checkAndUpdateCmd('longitude', $retP["position"]["longitude"]);
      log::add('volvooncall', 'debug', 'key : longitude valeur : ' . $retP["position"]["longitude"]);
    } catch (Exception $e) {
      $key = $cmd->getLogicalId();
      foreach ($this->getCmd('info') as $cmd) {
        log::add('volvoncall', 'error', 'Impossible de mettre à jour le champs ' . $key);
      }
    }
  }

  public function record_pts_gps() {
    $session_volvooncall = new volvooncall_api();

    $login = $session_volvooncall->login($this->getConfiguration('VocUsername'), $this->getConfiguration('VocPassword'));

    //Vérification des identifiants
    if ($login != true) {
      log::add('volvooncall', 'error', "Erreur Login");
      return;  // Erreur de login API VOLVO
    }

    $vin = $session_volvooncall->getVin();

    log::add('volvooncall','debug',"Record GPS : vin = ".$vin);

    $retS = $session_volvooncall->getStatus($vin);
    $retP = $session_volvooncall->getPosition($vin, null);
    $retT = $session_volvooncall->getTrips($vin);

    $tripPoints = $retT["trips"];
    $tripPosition = $retP["position"];

    $fn_car_gps   = dirname(__FILE__).CARS_FILES_DIR.$vin.'/gps.json';

    $engineRunning = $retS["engineRunning"];

    if ($engineRunning == true) {
      $is_running = 1;
    }
    else {
      $is_running = 0;
    }

    //$arrayTrips = array( 'pts_gps' => array() );
    $arrayTrips = array( 'pts_gps' => array('idTrip' => array()) );

    if ($this->getIsEnable()) {
      if ($is_running == 1) {
        for ($i = 1; $i < 21; $i++) {
          sleep(300); //en secondes => 5 minutes : 60*5 = 300
          $arrayTrips['pts_gps']['idTrip'] = $tripPoints[0]["id"];
          //$arrayTrips['pts_gps']['id'] = $i;
          $arrayTrips['pts_gps']['coords'][$i]['idGps'] = $i;
          $arrayTrips['pts_gps']['coords'][$i]['latitude'] = $tripPosition["latitude"];
          $arrayTrips['pts_gps']['coords'][$i]['longitude'] = $tripPosition["longitude"];
        }


        $jsonGPS = json_encode($arrayTrips, JSON_PRETTY_PRINT);
        file_put_contents($fn_car_gps, $jsonGPS, FILE_APPEND | LOCK_EX);
      }
    }
  }

}

class volvooncallCmd extends cmd
{
  // Exécution d'une commande  
  public function execute($_options = array())
  {
    if ($this->getLogicalId() == 'refresh') {
      log::add('volvooncall', 'info', "Refresh data");
      foreach (eqLogic::byType('volvooncall') as $eqLogic) {
        $eqLogic->updateData();
        $eqLogic->record_pts_gps();
      }
      $eqLogic->trips();
    }
  }

  /*     * **********************Getteur Setteur*************************** */
}
