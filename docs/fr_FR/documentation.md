# Documentation du plugin volvooncall
<hr>
## Notes importantes
--
L'API utilisée est celle de Wirelesscar et n'a pas de documentation !
Je rejète donc toutes responsabilités !

PLUGIN NON FONCTIONNEL POUR LE MOMENT
--
VOLVO a tout d'abord opté pour ce choix pour un "clé en main" mais sont en train de développer leur propre API.
Vous pouvez la retrouver [ici](https://developer.volvocars.com/volvo-api).
Elle est assez prometteuse mais ne permet pas, pour le moment, de faire autant de chose que sa concurrente.

Un script python existe [ici](https://github.com/molobrakos/volvooncall) et je m'en suis largement inspiré pour le module.

Initialement j'avais intégrer cette solution avec broker MQTT, etc ... (source sur le forum et sur Internet).
Mais je me suis dis qu'il serait bien que les propriétaires de VOLVO puissent en profiter aussi.

Ce plugin est largement inspiré de celui développé par Lelas pour les voitures Peugeot.
Merci à lui !
<hr>
## Fonctions

Ce plugin permet d'accèder aux informations de votre voiture connectée Volvo :
- Quasiment tous les capteurs (ampoules, lave-glace, liquide de freins, statistiques, pression des penus, etc...)
Il permet également d'accéder au trajets des 100 derniers jours avec les détails :
- départ/arrivée
- consommation
- moyenne de vitesse
Il permet aussi, si votre voiture le supporte, quelques actions distantes :
- Moteur : couper et mettre en route
- Climatisation : préconditionnement du véhicule avec gestion de Timers
- Vérrouilage : portes et fenêtres

Les informations disponibles dans le widgets sont:
* Charge de la batterie, autonomie et kilométrage de la voiture
* Information sur le chargement de la batterie (Prise connectée, fin de chargement, etc...)
* Nombre de jours et kilomètres jusqu'au prochain entretien du véhicule
* Situation du véhicule sur une carte (Position GPS), distance au domicile.

<p align="left">
  <img src="../images/widget.png" width="400" title="Widget dashboard">
</p>

## Installation
Par source Github:
* Aller dans Jeedom menu Plugins / Gestion des plugins
* Sélectionner le symbole + (Ajouter un plugin)
* Sélectionner le type de source Github (Il faut l'avoir autorisé au préalable dans le menu Réglages / Système / Configuration => Mise à jour/Market)
* Remplir les champs:
  * ID logique du plugin : volvooncall
  * Utilisateur ou organisation du dépôt : BruceLIOU
  * Nom du dépôt :  jeedom-plugin-volvooncall
  * Branche : master
* Aller dans le menu "plugins/objets connectés/Volvo On Call" de jeedom pour installer le nouveau plugin.

Sur la page configuration du plugin, saisir vos identifiants de compte Volvo On Call et votre région, et cochez la case :"Afficher le panneau desktop". Cela donne accès à la page du "panel" de l'équipement.

## Configuration
Une fois l'installation effectuée:
Sur l'onglet "**Equipement**", choisissez l'objet parent et rendez le actif et visible.
<p align="left">
  <img src="../images/config_equipement.png" width="700" title="Configuration équipement">
</p>

La page du panel à besoin de connaitre les coordonnées GPS de stationnement habituel de la voiture afin de centrer la carte pour l’affichage des trajets. <br>
Pour cela, il faut renseigner ces coordonnées GPS dans la page de configuration de jeedom. <br>
Règlages => Système => Configuration => Informations <br>
<p align="left">
  <img src="../images/config_informations.png" width="500" title="Configuration informations">
</p>

## Widget
Le widget est configuré automatiquement par le plugin lors de la création de l'équipement.
Il est possible d'agencer les éléments dans le widgets par la fonction d'édition du dashboard.<br>
Je propose l'agencement suivant comme exemple, en utilisant la présentation en tableau dans Configuration Avancée=>Disposition (voir ci dessous) <br>
Lorsque l'on clique sur la photo, on bascule sur la page "Panel" du plugin associée au véhicule.
<p align="left">
  <img src="../images/config_widget.png" width="700" title="Widget dashboard">
</p>

## Panel
Une page de type "panel" est disponible pour le plugin dans le menu Acceuil de jeedom. <br>
Cette page permet de consulter les informations suivantes sur 4 onglets différents:
* Liste des trajets effectués par le véhicule
* Statistiques sur l'utilisation et la consommation du véhicule. 
* Quelques informations sur le véhicule
* Informations sur les visites d'entretien du véhicule recommandées par Volvo

**Affichage des trajets:**
Il est possible de définir une période soit par 2 dates, soit par des racourcis ('Aujourd'hui', 'hier', 'les 7 derniers jours' ou 'tout'), puis d'afficher l'ensemble des trajets sur cette période.<br>
La suite de la page est mise à jour avec l'affichage des trajets sélectionnés, en tableau et en affichage sur une carte. (Openstreet map) <br>
On peut sélectionner les trajets 1 par 1 dans le tableau pour afficher un seul trajet dans la liste. <br>
Un résumé sur l'ensemble des trajets sélectionnés et donné également sur cette page.
<p align="left">
  <img src="../images/panel1.png" width="600" title="Panel1">
</p>

**Statistiques:**
Présentations sous forme de graphe de quelques statistiques d'utilisation du véhicule, basées sur l'historique des trajets mémorisés.

* Distances parcourues 
* Consommation du véhicule au 100 km
* Energie consommée et coût estimé


**Informations sur le véhicule:**
Quelques informations sont données sur le véhicule
En particulier la dernière version du logiciel disponible
<p align="left">
  <img src="../images/panel2.png" width="500" title="Panel2">
</p>

**Visites d'entretien:**
Liste des 3 prochaines opérations d'entretien du véhicule, avec leur date ou kilométrage prévisionels
Les opérations principales d'entretion sont données également. : plus fonctionnel pour le moment
<p align="left">
  <img src="../images/panel3.png" width="600" title="Panel3">
</p>

**Bugs connus:**
Cette version 1.0 est encore draft. Il y a quelques bugs connus mais non pénalisants
* Javascript erreur : "ReferenceError: L is not defined" (affichée dans la barre de titre de jeedom) <br>
  => Corrigé
* Affichage des trajets sur le pannel: On ne peut pas toujours sélectionner un trajet pour affichage sur la carte.
  => Corrigé

**Suite prévue pour ce plugin:**
* Implémentation API Volvo (quand elle sera plus complète)
* Ajouter le pilotage du préconditionnement du véhicule par Timers
* Envoyer des trajets au véhicule
* Exporter les trajets (pdf ou xls) pour des notes de frais par exemple
