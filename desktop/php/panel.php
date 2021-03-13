<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

include_file('3rdparty', 'DataTables/DataTables-1.10.22/js/jquery.dataTables.min', 'js', 'volvooncall');
include_file('3rdparty', 'DataTables/DataTables-1.10.22/css/jquery.dataTables.min', 'css', 'volvooncall');
include_file('3rdparty', 'leaflet_v1.7.1/leaflet', 'js', 'volvooncall');
include_file('3rdparty', 'leaflet_v1.7.1/leaflet', 'css', 'volvooncall');
include_file('3rdparty', 'js/moment', 'js', 'volvooncall');
include_file('3rdparty', 'js/moment-with-locales', 'js', 'volvooncall');
include_file('core', 'template/css/volvooncall.css', 'template', 'volvooncall');

include '/plugins/volvooncall/core/class/volvooncall.class.php';


$date = array(
    'start' => date('d-m-Y', strtotime(config::byKey('history::defautShowPeriod') . ' ' . date('d-m-Y'))),
    'end' => date('d-m-Y'),
);
sendVarToJS('eqType', 'volvooncall');
sendVarToJs('object_id', init('object_id'));
$eqLogics = volvooncall::byType('volvooncall');

foreach ($eqLogics as $eqLogic) {
    $VocUsername = $eqLogic->getConfiguration('VocUsername');
    $VocPassword = $eqLogic->getConfiguration('VocPassword');
}

$session_volvooncall = new volvooncall_api();

$login = $session_volvooncall->login($VocUsername, $VocPassword);

//Vérification des identifiants
if ($login != true) {
  log::add('volvooncall', 'error', "Erreur Login");
  return;  // Erreur de login API VOLVO
}

$vin = $session_volvooncall->getVin();

log::add('volvooncall', 'debug', 'Pannel: VIN:'.$vin);

$retT = $session_volvooncall->getTrips($vin);

$jsonFile = __DIR__ . '/../../data/'.$vin.'trips.json';

$fn_car = file_get_contents($jsonFile);
$jsonTrips = json_decode($fn_car, TRUE);

$dist = 0;
$elec = 0;
$fuel = 0;

$total = count($retT["trips"]);
foreach ( $retT["trips"] as $trips )
{
    $dist += $trips['tripDetails'][0]['distance']/1000;
    $elec += $trips['tripDetails'][0]['electricalConsumption']/1000;
    $fuel += $trips['tripDetails'][0]['fuelConsumption']/100;
}

?>

<div id="exTab3" class="container container_volvooncall">
    <ul class="nav nav-pills">
        <li class="active">
            <a href="#car_trips_tab" data-toggle="tab">Trajets</a>
        </li>
        <li>
            <a href="#car_stat_tab" data-toggle="tab">Caractéristiques</a>
        </li>
        <li>
            <a href="#car_cmd_tab" data-toggle="tab">Commandes</a>
        </li>
        <li>
            <a href="#car_maint_tab" data-toggle="tab">Entretien</a>
        </li>
    </ul>

    <div class="tab-content clearfix content_volcooncall">
        <div class="tab-pane active" id="car_trips_tab">
            <form class="form-horizontal">
                <fieldset style="border: 1px solid #e5e5e5; border-radius: 5px 5px 5px 5px;">
                    <div style="min-height: 10px;"></div>
                    <div style="min-height:40px;font-size: 1.5em;">
                        <i style="font-size: initial;"></i> Période analysée
                    </div>
                    <?php
                        echo '<input type="hidden" value="' . $vin . '">';
                    ?>
                    <div style="min-height:30px;">
                        <div class="pull-left" style="font-size: 1.3em;"> Début:
                            <input id="gps_startDate" class="pull-right form-control input-sm in_datepicker" style="display : inline-block; width: 87px;" value="<?php echo $date['start']?>" />
                        </div>
                        <div class="pull-left" style="font-size: 1.3em;">Fin:
                            <input id="gps_endDate" class="pull-right form-control input-sm in_datepicker" style="display : inline-block; width: 87px;" value="<?php echo $date['end']?>" />
                        </div>
                        <a style="margin-left:5px" class="pull-left btn btn-primary btn-sm tooltips" id='btgps_validChangeDate' title="Mise à jour des données sur la période">Mise à jour période</a><br>
                    </div>
                    <div style="min-height:50px;">
                        <div style="padding-top:10px;font-size: 1.5em;">
                            <a style="margin-right:5px;" class="pull-left btn btn-success btn-sm tooltips" id='btgps_per_today'>Aujourd'hui</a>
                            <a style="margin-right:5px;" class="pull-left btn btn-success btn-sm tooltips" id='btgps_per_yesterday'>Hier</a>
                            <a style="margin-right:5px;" class="pull-left btn btn-success btn-sm tooltips" id='btgps_per_this_week'>Cette semaine</a>
                            <a style="margin-right:5px;" class="pull-left btn btn-success btn-sm tooltips" id='btgps_per_last_week'>Les 7 derniers jours</a>
                            <a style="margin-right:5px;" class="pull-left btn btn-success btn-sm tooltips" id='btgps_per_all'>Tout</a>
                        </div>
                    </div>
                </fieldset>
            </form>
            <div style="min-height: 10px;"></div>
            <div>
                <form class="form-horizontal">
                    <fieldset style="border: 1px solid #e5e5e5; border-radius: 5px 5px 5px 5px;">
                        <div style="padding-top:10px;padding-left:24px;padding-bottom:10px;color: #333;font-size: 1.5em;">
                            <i style="font-size: initial;"></i> Historique des trajets réalisés sur cette période
                            <div style="padding-top:8px;color: #fff;font-size: 0.6em;">
                                Nombre de trajet : <span id="nombre_trajets"><?php echo $total?></span><br>
                                Distance totale : <span id="distance_totale"></span><?php echo round($dist, 2);?> km<br>
                                Consommation carburant : <span id="conso_carburant_totale"><?php echo round($fuel, 2);?></span> l<br>
                                Consommation électrique : <span id="conso_batterie_totale"><?php echo round($elec, 2);?></span> kWh<br>
                            </div>
                        </div>
                        <div id='trips_info' style="font-size: 1.2em;"></div>
                        <div></div>
                        <br>
                    </fieldset>
                </form>
            </div>
            <div style="min-height: 10px;"></div>
            <div>
                <div id="trips_list" style="float:left;width:45%">
                    <div id='div_hist_liste' style="font-size: 1.2em;"></div>
                    <div id='div_hist_liste2' style="font-size: 1.2em;">
                        <table id="trip_liste" class="display compact" width="100%"></table>
                    </div>
                    <div id="trips_separ" style="margin-left:45%;width:1%"></div>
                    <div id="trips_map" style="margin-left:46%;width:54%"></div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="car_stat_tab">
            <h3>Caractéristiques du véhicule</h3>
          	<h4><a href="https://s.volvocars.com/AqgdNKlUpeOEl742" target="_blank">Voir sur le site de Volvo</a></h4>
          	<p id="model">Modèle : <span class="model"></span></p>
          	<p id="model_annee">Année du modèle : <span class="model_annee"></span></p>
          	<p id="capacite_reservoir">Capacité réservoir : <span class="capacite_reservoir"></span>L</p>
            <p id="immatriculation">Immatriculation : <span class="immatriculation"></span></p>
          	<hr>
          	<p id="moyenneVitesse">Vitesse moyenne : <span class="stateMoyenneVitesse"></span> km/h</p>
          	<p id="moyenneConso">Consommation moyenne : <span class="stateConsoMoyenne"></span> l/km</p>
          	<p id="TM">TM : <span class="stateTM"></span> km</p>
          	<p id="TA">TA : <span class="stateTA"></span> km</p>
        </div>
        <div class="tab-pane" id="car_cmd_tab">
            <h3>Toutes les commandes</h3>
            <p><a class="btn">Démarrer le moteur</a></p>
            <p><a class="btn">Allumer la climatisation</a></p>
            <p><a class="btn">Programmer la climatisation</a></p>
            <p><a class="btn">Vérrouiller le véhicule</a></p>
        </div>
        <div class="tab-pane" id="car_maint_tab">
            <h3>Maintenance du véhicule</h3>
            <p id="kilometrage">Kilométrage : <span class="stateKilometrage"></span> km</p>
            <p id="dateRevision">Date prochaine révision : <span class="stateDateRevision"></span><span class="stateJoursRevision"></span></p>
            <p id="distanceRevision">Distance révision : <span class="stateDistanceRevision"></span> km</p>

            <div classs="entretien">
                <h1>Programme d'entretien</h1>
                <ul class="nav nav-pills">
                    <li id="16k">
                        <a href="#div_16k" data-toggle="tab">16 K</a>
                    </li>
                    <li id="32k">
                        <a href="#div_32k" data-toggle="tab">32 K</a>
                    </li>
                    <li id="48k">
                        <a href="#div_48k" data-toggle="tab">48 K</a>
                    </li>
                    <li id="64k">
                        <a href="#div_64k" data-toggle="tab">64 K</a>
                    </li>
                    <li id="80k">
                        <a href="#div_80k" data-toggle="tab">80 K</a>
                    </li>
                    <li id="96k">
                        <a href="#div_96k" data-toggle="tab">96 K</a>
                    </li>
                </ul>
                <div class="tab-content clearfix content_volcooncall">
                    <div class="tab-pane" id="div_16k">
                        <ul>
                            <li>Changement de l’huile et des filtres (appoint avec huile synthétique)</li>
                            <li>Inspection du châssis</li>
                            <li>Mise à jour du logiciel technique</li>
                            <li>Nettoyage du pare-brise devant la caméra IntelliSafe</li>
                        </ul>
                    </div>
                    <div class="tab-pane" id="div_32k">
                        <ul>
                            <li>Changement de l’huile et des filtres (appoint avec huile synthétique)</li>
                            <li>Inspection du châssis et de l’usure</li>
                            <li>Remplacement du filtre à air de l’habitacle</li>
                            <li>Mise à jour du logiciel technique</li>
                            <li>Nettoyage du pare-brise devant la caméra IntelliSafe</li>
                        </ul>
                    </div>
                    <div class="tab-pane" id="div_48k">
                        <ul>
                            <li>Changement de l’huile et des filtres (appoint avec huile synthétique)</li>
                            <li>Inspection du châssis</li>
                            <li>Mise à jour du logiciel technique</li>
                            <li>Nettoyage du pare-brise devant la caméra IntelliSafe</li>
                        </ul>
                    </div>
                    <div class="tab-pane" id="div_64k">
                        <ul>
                            <li>Changement de l’huile et des filtres (appoint avec huile synthétique)</li>
                            <li>Inspection du châssis</li>
                            <li>Inspection du groupe motopropulseur et de l’usure</li>
                            <li>Remplacement du filtre de l’habitacle, du filtre à air du moteur</li>
                            <li>Remplacement du liquide de freins</li>
                            <li>Mise à jour du logiciel technique</li>
                            <li>Nettoyage du pare-brise devant la caméra IntelliSafe</li>
                        </ul>
                    </div>
                    <div class="tab-pane" id="div_80k">
                        <ul>
                            <li>Changement de l’huile et des filtres (appoint avec huile synthétique)</li>
                            <li>Inspection du châssis</li>
                            <li>Mise à jour du logiciel technique</li>
                            <li>Nettoyage du pare-brise devant la caméra IntelliSafe</li>
                        </ul>
                    </div>
                    <div class="tab-pane" id="div_96k">
                        <ul>
                            <li>Changement de l’huile et des filtres (appoint avec huile synthétique, selon la
                                disponibilité)</li>
                            <li>Inspection du châssis et de l’usure</li>
                            <li>Remplacement du filtre à air de l’habitacle et des bougies </li>
                            <li>Mise à jour du logiciel technique</li>
                            <li>Nettoyage du pare-brise devant la caméra IntelliSafe</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_file('desktop', 'panel', 'js', 'volvooncall');?>