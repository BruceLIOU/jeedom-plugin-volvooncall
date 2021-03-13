
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
$( ".in_datepicker" ).datepicker({
  dateFormat: 'dd-mm-yy'
});
moment.locale('fr'); 

/* Partie à adapter avec l'ID de vos commandes */
var vin = 5046;
var model = 4882;
var model_annee = 4883;
var capacite_reservoir = 4885;
var immatriculation = 4884;
var stateKilometrage = 4895;
var stateVitesseMoyenne = 4887;
var stateConsoMoyenne = 4886;
var stateTM = 4898;
var stateTA = 4899;
/* Saisir la date de 1ère mise en circulation */
var dateCircul = '27/08/2020'; //au format jj/mm/aaaa


var seizeMille = 16000;
var dateseizeMille = moment(dateCircul, 'DD/MM/YYYY').add(1, 'y');
var trenteDeuxMille = 32000;
var datetrenteDeuxMille = moment(dateseizeMille, 'DD/MM/YYYY');
var quaranteHuitMille = 48000;
var datequaranteHuitMille = moment(datetrenteDeuxMille, 'DD/MM/YYYY'); 
var soixanteQuatreMille = 64000;
var datesoixanteQuatreMille = moment(datequaranteHuitMille, 'DD/MM/YYYY');
var quatreVingtMille = 80000;
var datequatreVingtMille = moment(datesoixanteQuatreMille, 'DD/MM/YYYY');

var distanceRevisionKm = 0;

var today = moment().format('L');
var jourRevision = moment().endOf('day').fromNow();

$('#model')[0].setAttribute('data-cmd_id', model);
jeedom.cmd.update[model] = function (_options) {
  jeedom.cmd.execute({ // Récupération de la valeur  
      id: model,
      success: function (valeur_courante) {
          //      alert(valeur_courante);
          $('.model').empty().append(valeur_courante);
      }
  })
};
jeedom.cmd.update[model]();

$('#model_annee')[0].setAttribute('data-cmd_id', model_annee);
jeedom.cmd.update[model_annee] = function (_options) {
  jeedom.cmd.execute({ // Récupération de la valeur  
      id: model_annee,
      success: function (valeur_courante) {
          //      alert(valeur_courante);
          $('.model_annee').empty().append(valeur_courante);
      }
  })
};
jeedom.cmd.update[model_annee]();

$('#capacite_reservoir')[0].setAttribute('data-cmd_id', capacite_reservoir);
jeedom.cmd.update[capacite_reservoir] = function (_options) {
  jeedom.cmd.execute({ // Récupération de la valeur  
      id: capacite_reservoir,
      success: function (valeur_courante) {
          //      alert(valeur_courante);
          $('.capacite_reservoir').empty().append(valeur_courante);
      }
  })
};
jeedom.cmd.update[capacite_reservoir]();

$('#immatriculation')[0].setAttribute('data-cmd_id', immatriculation);
jeedom.cmd.update[immatriculation] = function (_options) {
  jeedom.cmd.execute({ // Récupération de la valeur  
      id: immatriculation,
      success: function (valeur_courante) {
          //      alert(valeur_courante);
          $('.immatriculation').empty().append(valeur_courante);
      }
  })
};
jeedom.cmd.update[immatriculation]();

$('#moyenneVitesse')[0].setAttribute('data-cmd_id', stateVitesseMoyenne);
jeedom.cmd.update[stateVitesseMoyenne] = function (_options) {
  jeedom.cmd.execute({ // Récupération de la valeur  
      id: stateVitesseMoyenne,
      success: function (valeur_courante) {
          //      alert(valeur_courante);
          $('.stateMoyenneVitesse').empty().append(valeur_courante);
      }
  })
};
jeedom.cmd.update[stateVitesseMoyenne]();

$('#moyenneConso')[0].setAttribute('data-cmd_id', stateConsoMoyenne);
jeedom.cmd.update[stateConsoMoyenne] = function (_options) {
  jeedom.cmd.execute({ // Récupération de la valeur  
      id: stateConsoMoyenne,
      success: function (valeur_courante) {
          //      alert(valeur_courante);
          $('.stateConsoMoyenne').empty().append(valeur_courante);
      }
  })
};
jeedom.cmd.update[stateConsoMoyenne]();

$('#TM')[0].setAttribute('data-cmd_id', stateTM);
jeedom.cmd.update[stateTM] = function (_options) {
  jeedom.cmd.execute({ // Récupération de la valeur  
      id: stateTM,
      success: function (valeur_courante) {
          //      alert(valeur_courante);
          $('.stateTM').empty().append(valeur_courante);
      }
  })
};
jeedom.cmd.update[stateTM]();

$('#TA')[0].setAttribute('data-cmd_id', stateTA);
jeedom.cmd.update[stateTA] = function (_options) {
  jeedom.cmd.execute({ // Récupération de la valeur  
      id: stateTA,
      success: function (valeur_courante) {
          //      alert(valeur_courante);
          $('.stateTA').empty().append(valeur_courante);
      }
  })
};
jeedom.cmd.update[stateTA]();

$('#kilometrage')[0].setAttribute('data-cmd_id', stateKilometrage);
jeedom.cmd.update[stateKilometrage] = function (_options) {
  jeedom.cmd.execute({ // Récupération de la valeur  
      id: stateKilometrage,
      success: function (valeur_courante) {
          //      alert(valeur_courante);
          $('.stateKilometrage').empty().append(Math.round(valeur_courante * 100) / 100);
          if (valeur_courante < seizeMille) {
              $('#16k').addClass('active');
              $('#div_16k').addClass('active');
              distanceRevisionKm = seizeMille - valeur_courante;
              var dateRevision = moment(dateseizeMille).format('L'); 
              var jourRevision = " (" + moment(dateseizeMille).endOf('day').fromNow() + ")";
              $('.stateDistanceRevision').empty().append(Math.round(distanceRevisionKm * 100) / 100);
              $('.stateDateRevision').empty().append(dateRevision);
              $('.stateJoursRevision').empty().append(jourRevision);
          }
          else if (valeur_courante > seizeMille && valeur_courante < trenteDeuxMille) {
              $('#32k').addClass('active');
              $('#div_32k').addClass('active');
              distanceRevisionKm = trenteDeuxMille - valeur_courante;
              var dateRevision = moment(datetrenteDeuxMille).format('L'); 
              var jourRevision = " (" + moment(datetrenteDeuxMille).endOf('day').fromNow() + ")";
              $('.stateDistanceRevision').empty().append(Math.round(distanceRevisionKm * 100) / 100);
              $('.stateDateRevision').empty().append(dateRevision);
              $('.stateJoursRevision').empty().append(jourRevision);
          }
          else if (valeur_courante > trenteDeuxMille && valeur_courante < quaranteHuitMille) {
              $('#48k').addClass('active');
              $('#div_48k').addClass('active');
              distanceRevisionKm = quaranteHuitMille - valeur_courante;
              var dateRevision = moment(datequaranteHuitMille).format('L'); 
              var jourRevision = " (" + moment(datequaranteHuitMille).endOf('day').fromNow() + ")";
              $('.stateDistanceRevision').empty().append(Math.round(distanceRevisionKm * 100) / 100);
              $('.stateDateRevision').empty().append(dateRevision);
              $('.stateJoursRevision').empty().append(jourRevision);
          }
          else if (valeur_courante > quaranteHuitMille && valeur_courante < soixanteQuatreMille) {
              $('#64k').addClass('active');
              $('#div_64k').addClass('active');
              distanceRevisionKm = soixanteQuatreMille - valeur_courante;
              var dateRevision = moment(datesoixanteQuatreMille).format('L'); 
              var jourRevision = " (" + moment(datesoixanteQuatreMille).endOf('day').fromNow() + ")";
              $('.stateDistanceRevision').empty().append(Math.round(distanceRevisionKm * 100) / 100);
              $('.stateDateRevision').empty().append(dateRevision);
              $('.stateJoursRevision').empty().append(jourRevision);
          }
          else if (valeur_courante > soixanteQuatreMille && valeur_courante < quatreVingtMille) {
              $('#80k').addClass('active');
              $('#div_80k').addClass('active');
              distanceRevisionKm = quatreVingtMille - valeur_courante;
              var dateRevision = moment(datequatreVingtMille).format('L'); 
              var jourRevision = " (" + moment(datequatreVingtMille).endOf('day').fromNow() + ")";
              $('.stateDistanceRevision').empty().append(Math.round(distanceRevisionKm * 100) / 100);
              $('.stateDateRevision').empty().append(dateRevision);
              $('.stateJoursRevision').empty().append(jourRevision);
          }
          else if (valeur_courante > quatreVingtMille) {
              $('#96k').addClass('active');
              $('#div_96k').addClass('active');
          }
      }
  })
};

jeedom.cmd.update[stateKilometrage]();