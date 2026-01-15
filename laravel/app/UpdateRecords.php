<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UpdateRecords extends Model
{
    static function doQueries(){
      $messages = array();

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('EASI SA ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 22,validate=1
  WHERE validate = 0 AND retrait = 0 and fk_id_compte = 1 AND details like '%EASI SA%'";
      $messages[] = UpdateRecords::UpdateCategory("SALAIRE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('ARKAOS SA ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 22,validate=1
  WHERE validate = 0 AND retrait = 0 and fk_id_compte = 1 AND (details like '%ARKAOS%' or details like '%INMUSIC EUROPE LIMITED%')";
      $messages[] = UpdateRecords::UpdateCategory("SALAIRE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = CONCAT('ALLOCATION FAMILIALE ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 43,validate=1
  WHERE validate = 0 AND retrait = 0 and fk_id_compte = 2 AND (details like '%CAF%SECUREX%' OR details like '%CAISSE D%ALLOCATIONS FAMIL%' OR details like '%PARTENA - CAISSE DECOMPENS%' OR details like '%PARENTIA%' )";
      $messages[] = UpdateRecords::UpdateCategory("ALLOCATION FAMILIALE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = CONCAT('ASS. MAISON ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 17,validate=1
  WHERE validate = 0 AND details like '%AG%INSURANCE%'";
      $messages[] = UpdateRecords::UpdateCategory("ASSURANCE MAISON",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = CONCAT('ASSURANCE SOLDE RESTANT DU ',DATE_FORMAT(date,'%Y')),fk_id_categorie = 18,validate=1
  WHERE validate = 0 AND (details like '%SOLDE%RESTANT%DU%' or details like '%Assurance%sol%de%restant du%')";
      $messages[] = UpdateRecords::UpdateCategory("ASSURANCE SOLDE RESTANT DU",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = CONCAT('ASSURANCE AUTO ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 19,validate=1
  WHERE validate = 0 AND details like '%ING%Auto%'";
      $messages[] = UpdateRecords::UpdateCategory("ASSURANCE AUTO",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = '',fk_id_categorie = 20,validate=1
  WHERE validate = 0 AND (details like '%MECATECHNIC%' OR details like '%contribution%auto%' OR details like '%MECATECNIC%'
  OR details like '%MARVINCE%' OR details like '%Wash One Waterloo%' or details like '%AUTO 5%')";
      $messages[] = UpdateRecords::UpdateCategory("AUTO",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('CARBURANT ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 21,validate=1
  WHERE validate = 0 AND (details like '%DATS%24%' OR details like '%SHELL%')";
      $messages[] = UpdateRecords::UpdateCategory("CARBURANT",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('CHARGE GAZ/ELEC ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 25,validate=1
  WHERE validate = 0 AND (details like '%ELECTRABEL%' OR details like '%LAMPIRIS%' OR details like '%Mega%' OR details like '%ORES%' OR details like '%Engie%')";
      $messages[] = UpdateRecords::UpdateCategory("CHARGE ELEC/GAZ",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('CHARGE EAU ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 26,validate=1
  WHERE validate = 0 AND (details like '%IECBW%' OR details like '%I.E.C.B.W%')";
      $messages[] = UpdateRecords::UpdateCategory("CHARGE EAU",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('GSM ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 28,validate=1
  WHERE validate = 0 AND (details like '%KPN%group%belgium%' OR details like '%PROXIMUS%BE31435411161155%' OR details like '%BASE SHOP%' OR details like '%BASE - BE42310134758954%' 
  OR details like '%BASE Telenet%' or details like '%TELENET GROUP%')";
      $messages[] = UpdateRecords::UpdateCategory("GSM",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('CREDIT HYPOTHECAIRE ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 29,validate=1
  WHERE validate = 0 AND details like '%CREDIT%HYPOTHECAIRE%145484%'";
      $messages[] = UpdateRecords::UpdateCategory("EMPRUNT",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('EPARGNE PENSION MICHAEL ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 30,validate=1
  WHERE validate = 0 AND details like '%B%PENSION%FOUND%'";
      $messages[] = UpdateRecords::UpdateCategory("PENSION MICHAEL",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('EPARGNE PENSION AURELIE ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 30,validate=1
  WHERE validate = 0 AND details like '%PENSION%INVEST%PLAN%'";
      $messages[] = UpdateRecords::UpdateCategory("PENSION AURELIE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records  SET libelle =  CONCAT('BOUFFE ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 31,validate=1
  WHERE validate = 0 AND (details like '%DELH%' OR details like '%RESTAURANT ZEN%' OR details like '%COLRUYT%' OR details like '%DELITRAIT%' OR details like '%MATCH%BRAINE%' OR details like '%TOM%CO%' OR details like '%LIDL%' OR details like '%QUICK%' OR details like '%MATCH%WOLUWE%' OR details like '%DELITRAITEUR%' OR details like '%MM COMME A LA MAISON%'
  OR details like '%WAPI SNACK%' OR details like '%LONBOIS%' OR details like '%MC DONALD%'  OR details like '%MARKET WATERLOO %' OR details like '%DDMM%' OR details like '%DE GELAS BRUNO%' OR details like '%DAVID LAURENT BRAINE%' OR details like '%WA ROSES SPRL%' OR details like '%FOODIE\'S MARKET%' OR details like '%FRITUUR SMULPLEZIER%' OR details like '%SWEETS BRAINE%' OR details like '%BRAINE FOOD%' OR details like '%BURGER KING%' OR details like '%LE PITOU%' OR details like '%ARTISANALE DE%' OR details like '%AD MERBRAINE%' OR details like '%MA-LINE%' OR details like '%Ad Braine Braine%' OR details like '%CRF MKT WATERLO%' 
  OR details like '%Ctt buvette%' or details like '%La Cafet%' or details like '%TURKUAZ%' or details like '%THB6 1050 - BRUXELLES%' or details like '%Brothers Invest Group%'
  OR details like '%O KEBAB%' or details like '%les amis du 179%' or details like '%Boucherie Ardennaise%' or details like '%Le Mediteraneen%' or details like '%MC Sweet\'s 1420%'
  or details like '%Les Amis du 17%' or details like '%Friends Zone%' or details like '%ST Business 1420%' or details like '%Delitreteur Braine%' or details like '%THE DONUT FACTOR%'
  or details like '%MM COMME A LA MAI%' or details like '%Qcash ping pong%' or details like '%Maison des Arts Suc%' or details like '%Ad Braine%' or details like '%M-JOY PRODUCTIONS%'
  or details like '%ROTISSERIE CA ROULE%' or details like '%IMOLAC 1410%' or details like '%LB 7791 WATERLOO 1410%' or details like '%MCDONALDS%' or details like '%Monsieur patate 1420%'
  or details like '%La Table du Parc 1400 - Nivelles%' or details like '%LA MAISON DEMARET%' or details like '%CO&GO WATERLOO%' or details like '%PANOS%' or details like '%BRASSERIE ALLIANCE%'
  or details like '%Dunkin%' or details like '%LE BON PAIN%' or details like '%NYLA ROSE 1420%' or details like '%Accent Catalan%' or details like '%NIVELLES ICE CREAM%'
  or details like '%Takeaway.com%' or details like '%MM COMME _ LA MAI%' or details like '%LES%DELICES%DE%L%IMPER%' or details like '%COFEO SERVICES%' or details like '%PANIER A PAIN WA%'
  or details like '%INTERMARCHE%' or details like '%LUNCH GARDEN%' or details like '%Pizza Hut%' or details like '%YIHENG 1420%' or details like '%Kameha Poke%'
  or details like '%Rotisserie Au P\'tit Poyon%')";
      $messages[] = UpdateRecords::UpdateCategory("BOUFFE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records  SET libelle =  CONCAT('BOULANGERIE ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 31,validate=1
  WHERE validate = 0 AND (details like '%BOULANGERIE%' OR details like '%MAISON THIRION%' OR details like '%EVENT JABRAS%')";
      $messages[] = UpdateRecords::UpdateCategory("BOUFFE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('PROVISION ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 33,validate=1
  WHERE validate = 0 AND details like '%provision%'";
      $messages[] = UpdateRecords::UpdateCategory("PROVISION",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('PROVISION ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 33,validate=1
  WHERE montant = '1200' AND validate = 0 AND details like '%Mois%'";
      $messages[] = UpdateRecords::UpdateCategory("PROVISION",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle =  CONCAT('EMPRUNT PAPA/MAMAN ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 34,validate=1
  WHERE validate = 0 AND (details like '%remboursement%pret%maison%' OR details like '%Remboursement%pret%papa%maman%')";
      $messages[] = UpdateRecords::UpdateCategory("EMPRUNT PAPA/MAMAN",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records  SET libelle =  CONCAT('BELGACOM ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 36,validate=1
  WHERE validate = 0 AND (details like '%BELGACOM%' or details like '%Proximus%')";
      $messages[] = UpdateRecords::UpdateCategory("TV/INTERNET",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records  SET libelle =  CONCAT('NETFLIX ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 36,validate=1
  WHERE validate = 0 AND details like '%Netflix%'";
      $messages[] = UpdateRecords::UpdateCategory("TV/INTERNET",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records  SET libelle =  CONCAT('ORANGE INTERNET/GSM/TV ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 36,validate=1
  WHERE validate = 0 AND details like '%ORANGE%'";
      $messages[] = UpdateRecords::UpdateCategory("TV/INTERNET",$query);      

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = '',fk_id_categorie = 38,validate=1 WHERE validate = 0 AND (details like '%MUTUALITE%' OR details like '%CARITAS%' OR details like '%ASSURE%HOSPI%' OR details like '%Ass.%Hospi%' OR details like '%MC ASSURE%' OR details like '%Dento + %')";
      $messages[] = UpdateRecords::UpdateCategory("MUTUALITE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = '',fk_id_categorie = 49,validate=1 WHERE validate = 0 
      AND (details like '%LUXUS%' OR details like '%ORCHESTRA%' OR details like '%CESAM NATURE%' OR details like '%LES ARSOUILLES%' OR details like '%OKAIDI%' 
      OR details like '%ZEEMAN%' OR details like '%Roc events%' OR details like '%Aquabla - FR7618829754160284210614460%' OR details like '%Logiscool - BE37732056063728%'
      or details like '%Aqua club braine - FR7618829754160284210614460%' or details like '%Logischool - BE37732056063728%' or details like '%PISCINE%NAUSICAA%'
      or details like '%Zinzolin%' or details like '%Sakura%' or details like '%CCM stages%' or details like '%Les Poulains de Colipa%' or details like '%Club gym loisir%'
      or details like '%Colipain%')";
      $messages[] = UpdateRecords::UpdateCategory("ENFANTS",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = '',fk_id_categorie = 44,validate=1
  WHERE validate = 0 AND (details like '%ELIOT-MAISIN%' OR details like '%PHARM.%' OR details like '%HOP.BRAINE%'
  OR details like '%PH.SERVAIS%' OR details like '%PHAR.%SERVAIS%' OR details like '%PHARMA%' OR details like '%PH.%VALLEE%BAIL%' OR details like '%DENT-ART%' OR details like '%HOPITAL%DE%BRAINE%'
  OR details like '%ALSEMPHARMA%' OR details like '%PHARMACIE DE L\'E%' OR details like '%SERVAIS BRAINE%' OR details like '%CHIREC HBW%' OR details like '%CABINET DENTAIRE%' OR details like '%LE SOURIRE BY MARIE%' OR details like '%Dokter Lannoy%' OR details like '%PHARMACIE DE L\'ALLIANC%'
  OR details like '%PRATTE LAETITIA%' or details like '%Docteur Hublet Alexand%' or details like '%Ortho Medical Service%' or details like '%Iris sud%'
  or details like '%FAMILIA 46 WATERLOO 1410%' or details like '%Biologie lahulpe%' or details like '%Chirec%' or details like '%ORTHEIS%' or details like '%AZ DAMIAAN OOSTENDE%'
  or details like '%Jerome Hasselmans kine%' or details like '%Ambulance Ostende%' or details like '%Clinique Notre Dame de Grace%' or details like '%Servais Woo Centre 1410%'
  or details like '%Cinique notre dame de grace%')";
      $messages[] = UpdateRecords::UpdateCategory("SOINS",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = CONCAT('GARDE MALADE ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 45,validate=1
  WHERE validate = 0 AND details like '%BE28097000875020%'";
      $messages[] = UpdateRecords::UpdateCategory("GARDE MALADE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = CONCAT('ECOLE Enfant ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 46,validate=1
  WHERE validate = 0 AND (details like '%BE40096075994063%' or details like '%Ecole Vallee Bailly%' or details like '%Ecole Elise Vallee bailly%'
  or details like '%CFS etude Elise%')";
      $messages[] = UpdateRecords::UpdateCategory("ECOLE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = CONCAT('TITRE SERVICE ',DATE_FORMAT(date,'%m-%Y')),fk_id_categorie = 48,validate=1
  WHERE validate = 0 AND details like '%Sodexo - titre service%'";
      $messages[] = UpdateRecords::UpdateCategory("TITRE SERVICE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'ARGENT DE POCHE',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND (details like 'Retrait%Self%Bank' or details like '%ING WATERLOO PARK%' or details like '%Retrait Self\'Bank%' 
  or details like '%Retrait d\'espèces Bancontact%')";
      $messages[] = UpdateRecords::UpdateCategory("ARGENT DE POCHE",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'BRICO:',fk_id_categorie = 40,validate=1
  WHERE validate = 0 AND details like '%BRICO%'";
      $messages[] = UpdateRecords::UpdateCategory("MAISON ENTRETIEN",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'HUBO:',fk_id_categorie = 40,validate=1
  WHERE validate = 0 AND details like '%HUBO%'";
      $messages[] = UpdateRecords::UpdateCategory("MAISON ENTRETIEN",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'VOLTIS:',fk_id_categorie = 40,validate=1
  WHERE validate = 0 AND details like '%VOLTIS%'";
      $messages[] = UpdateRecords::UpdateCategory("MAISON ENTRETIEN",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'CHAUDIERE: POELAERT',fk_id_categorie = 40,validate=1
  WHERE validate = 0 AND details like '%POELAERT%'";
      $messages[] = UpdateRecords::UpdateCategory("MAISON ENTRETIEN",$query);
      

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'ENTRETIEN ROBOT TONDEUSE',fk_id_categorie = 40,validate=1
  WHERE validate = 0 AND (details like '%Chansay Olivier%' or details like '%Horti Tech%')";
      $messages[] = UpdateRecords::UpdateCategory("MAISON ENTRETIEN",$query);       

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'TAXE COMMUNALE:',fk_id_categorie = 40,validate=1
  WHERE validate = 0 AND details like '%Administration communale%'";
      $messages[] = UpdateRecords::UpdateCategory("MAISON",$query);
      
      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'MAISON: TRAVAUX',fk_id_categorie = 40,validate=1
  WHERE validate = 0 AND (details like '%Mauvisin%' or details like '%GL pro%' or details like '%Induscabel%')";
      $messages[] = UpdateRecords::UpdateCategory("MAISON TRAVAUX",$query);

        $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'MAISON: IKEA',fk_id_categorie = 40,validate=1
      WHERE validate = 0 AND details like '%IKEA%'";
          $messages[] = UpdateRecords::UpdateCategory("MAISON ",$query);      
      

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'DREAMLAND:',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND details like '%DREAMLAND%'";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'REMBOURSEMENT MASTERCARD:',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND (details like '%MASTERCARD ING%' or details like '%ING CARD Carte 5206975497842140%')";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);      

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'MULTIMEDIASHOP:',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND details like '%D-CONCEPT%'";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);     

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'MAISONS DU MONDE:',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND details like '%MAISONS DU MONDE%'";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);     

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'ACHAT: VETEMENTS',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND (details like '%AUDREY B%' or details like '%C&A%' or details like '%ELLE%COUSINE%' OR details like '%BESTSELLER RETAIL BEL 1400%'
  or details like '%NEW MANO%' or details like '%AMERICA TODAY%' or details like '%TAO 1410%' or details like '%Chaussea%' or details like '%PRONTI%' 
  or details like '%Celio%' or details like '%SERGENT MAJOR%' or details like '%KIABI%')";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);   
      
      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'ACHAT: LIBRAIRIE',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND details like '%ANGYCOPAS%'";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);  
            
      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'ACHAT: SPORT',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND (details like '%INTERSPORT%' or details like '%DECATHLON%' or details like '%Ctt braine - BE64034339945252%' or details like '%MAXIME WAUTHOZ%')";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);   
      
      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'ACHAT: Activités',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND (details like '%Technopolis%' or details like '%GDM Organisation SPRL%' or details like '%Villers la Ville 1495%' or details like '%ALWAYS IN MOTION%'
  or details like '%Parc des Expositions d 1020%' or details like '%BattleKart Belgium%' or details like '%BOWL FACTORY%' or details like '%CINES WELLINGTON%' 
  or details like '%Le monde de julie %' or details like '%GameForce%' or details like '%BECS 1020 - Bruxelles%' or details like '%NAUTISPORT%' or details like '%MONDIAL TISSUS%'
  or details like '%EXYPE%' or details like '%KINDERPLANEET%' or details like '%KINEPOLIS BRAINE 1420%' or details like '%Kinepolis%' or details like '%Kojump%')";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);   

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'ACHAT: ',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND (details like '%MISTER MINIT%' or details like '%BRUSEL%' or details like '%LE ZEBRE A POIS%' or details like '%LCDN 1410%'
  or details like '%CARREFOUR MONT-S 1410%' or details like '%FLEURS MARTINE PAYS 1420%' or details like '%Tacosystems 1410%'
  or details like '%KING JOUET%' or details like '%BE19310189608212%' or details like '%PAPETERIE WATERLOO%' or details like '%Action 2509 1420%' or details like '%CLUB 117 WATERLOO%'
  or details like '%MAXI TOYS BRAINE%' or details like '%LE BAOBAB LIVRES%' or details like '%CRF%HYP%MONT%ST%1410%' or details like '%CARREFOUR MONT-S%' or details like '%CRF MKT BRAINE 1420%'
  or details like '%CLUB 141 BRAINE%' or details like '%VERITAS%' or details like '%JARDIN%DES%OLIVIERS%LASNE%' or details like '%TRAFIC%' or details like '%HEMA%'
  or details like '%AVA BRAINE%' or details like '%KOOB%' or details like '%VANDEN BORRE%' or details like '%AQUATRE%S.C.R.L.%1410%'
  or details like '%CARREFOUR%WATERL%WATERLOO%')";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);        


      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'BANQUE: FRAIS',fk_id_categorie = 37,validate=1
  WHERE validate = 0 AND (details like '%Décompte de frais%' or details like '%Intérêts\-Frais%')";
      $messages[] = UpdateRecords::UpdateCategory("BANQUE",$query);   
      
      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'EPARGNE:',fk_id_categorie = 42,validate=1
  WHERE validate = 0 AND details like '%Epargne%'";
      $messages[] = UpdateRecords::UpdateCategory("EPARGNE",$query);         
            
      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'EPARGNE: BENJAMIN',fk_id_categorie = 42,validate=1
  WHERE validate = 0 AND details like '%BENJAMIN THIEBAULT - BE84001507851559%'";
      $messages[] = UpdateRecords::UpdateCategory("EPARGNE",$query);         

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'EPARGNE: ELISE',fk_id_categorie = 42,validate=1
  WHERE validate = 0 AND details like '%Elise Thiebault - BE95001507851458%'";
      $messages[] = UpdateRecords::UpdateCategory("EPARGNE",$query);   
      
      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'IMPOT',fk_id_categorie = 23,validate=1
  WHERE validate = 0 AND details like '%SPF Finances%'";
      $messages[] = UpdateRecords::UpdateCategory("IMPOT",$query);   

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'VACANCE',fk_id_categorie = 47,validate=1
  WHERE validate = 0 AND (details like '%Voyages olivier%' or details like '%VAB - BE62410032877161%' or details like '%De Haan%'
  or details like '%AUTOROUTES%' or details like '%BARJAC%' or details like '%BAGNOLS%' or details like '%Esf ski%' or details like '%PUY ST VINCEN%'
  or details like '%PUY-SAINT-VIN%' or details like '%GOUDARGUES%' or details like '%CORNILLON%' or details like '%RETHYMNO%' or details like '%W&L Company 1790 - AFFLIGEM%'
  or details like '%BRUGGE%' or details like '%TIGNES%' or details like '%NIMES%' or details like '%NIGLOLAND%' or details like '%CHATEL%')";
      $messages[] = UpdateRecords::UpdateCategory("VACANCE",$query);   

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'SPORT: cotisation Ping-Pong',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND (details like '%CTT braine - BE51034224070062%' or details like '%CTT braine cotisation - BE69088296605278%')";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);   

      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'SPORT: cotisation Volley',fk_id_categorie = 27,validate=1
  WHERE validate = 0 AND (details like '%NRJ cotisation%' or details like '%Volley NRJ1 - BE17068907482921%')";
      $messages[] = UpdateRecords::UpdateCategory("ACHAT",$query);         
      
      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = CONCAT('CADASTRE: ',DATE_FORMAT(date,'%Y')),fk_id_categorie = 41,validate=1
  WHERE validate = 0 AND details like '%Precompte immobilier - BE09091215032457%'";
      $messages[] = UpdateRecords::UpdateCategory("CADASTRE",$query);  
      
      $query = "UPDATE ".env("DB_TABLE_PREFIX")."records SET libelle = 'NOM DE DOMAINE',fk_id_categorie = 24,validate=1
  WHERE validate = 0 AND details like '%Belgates - BE62928094696661%'";
      $messages[] = UpdateRecords::UpdateCategory("WEBSITE",$query);        

      return $messages;
    }

    static function UpdateCategory($category,$query){
      $affected = DB::update($query);
      return $affected.' records ont &eacute;t&eacute; mis &agrave; jour pour la cat&eacute;gorie '.$category;
    }
}
