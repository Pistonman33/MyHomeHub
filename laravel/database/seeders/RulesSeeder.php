<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RulesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rules')->truncate();

        $rules = [
            // =========================
            // SALAIRES
            // =========================
            [
                'name' => 'EASI SA',
                'category_id' => 22,
                'match_pattern' => 'EASI SA',
                'libelle_template' => 'EASI SA {month}-{year}',
                'priority' => 10,
            ],
            [
                'name' => 'ARKAOS SA',
                'category_id' => 22,
                'match_pattern' => 'ARKAOS|INMUSIC EUROPE LIMITED',
                'libelle_template' => 'ARKAOS SA {month}-{year}',
                'priority' => 10,
            ],

            // =========================
            // ALLOCATIONS FAMILIALES
            // =========================
            [
                'name' => 'ALLOCATIONS FAMILIALES',
                'category_id' => 43,
                'match_pattern' => 'CAF SECUREX|CAISSE D ALLOCATIONS FAMIL|PARTENA - CAISSE DECOMPENS|PARENTIA',
                'libelle_template' => 'ALLOCATION FAMILIALE {month}-{year}',
                'priority' => 10,
            ],

            // =========================
            // ASSURANCES
            // =========================
            [
                'name' => 'ASSURANCE MAISON',
                'category_id' => 17,
                'match_pattern' => 'AG INSURANCE',
                'libelle_template' => 'ASS. MAISON {month}-{year}',
                'priority' => 20,
            ],
            [
                'name' => 'ASSURANCE SOLDE RESTANT',
                'category_id' => 18,
                'match_pattern' => 'SOLDE RESTANT DU|Assurance sol de restant du',
                'libelle_template' => 'ASSURANCE SOLDE RESTANT DU {year}',
                'priority' => 20,
            ],
            [
                'name' => 'ASSURANCE AUTO',
                'category_id' => 19,
                'match_pattern' => 'ING Auto',
                'libelle_template' => 'ASSURANCE AUTO {month}-{year}',
                'priority' => 20,
            ],

            // =========================
            // AUTO / TRANSPORT
            // =========================
            [
                'name' => 'ENTRETIEN AUTO',
                'category_id' => 20,
                'match_pattern' => 'MECATECHNIC|contribution auto|MECATECNIC|MARVINCE|Wash One Waterloo|AUTO 5',
                'libelle_template' => 'AUTO',
                'priority' => 15,
            ],
            [
                'name' => 'CARBURANT',
                'category_id' => 21,
                'match_pattern' => 'DATS 24|SHELL',
                'libelle_template' => 'CARBURANT {month}-{year}',
                'priority' => 15,
            ],

            // =========================
            // CHARGES HABITATION
            // =========================
            [
                'name' => 'CHARGE ELEC/GAZ',
                'category_id' => 25,
                'match_pattern' => 'ELECTRABEL|LAMPIRIS|Mega|ORES|Engie',
                'libelle_template' => 'CHARGE GAZ/ELEC {month}-{year}',
                'priority' => 20,
            ],
            [
                'name' => 'CHARGE EAU',
                'category_id' => 26,
                'match_pattern' => 'IECBW|I.E.C.B.W',
                'libelle_template' => 'CHARGE EAU {month}-{year}',
                'priority' => 20,
            ],

            // =========================
            // ARGENT LIQUIDE
            // =========================
            [
                'name' => 'ARGENT DE POCHE',
                'category_id' => 27,
                'match_pattern' => 'Retrait Self Bank|ING WATERLOO PARK|Retrait Self\'Bank|Retrait d\'espèces Bancontact',
                'libelle_template' => 'ARGENT DE POCHE',
                'priority' => 30,
            ],

            // =========================
            // TELEPHONIE / INTERNET
            // =========================
            [
                'name' => 'GSM / MOBILE',
                'category_id' => 28,
                'match_pattern' => 'KPN group belgium|PROXIMUS BE31435411161155|BASE SHOP|BASE - BE42310134758954|BASE Telenet|TELENET GROUP',
                'libelle_template' => 'GSM {month}-{year}',
                'priority' => 18,
            ],
            [
                'name' => 'BELGACOM / PROXIMUS',
                'category_id' => 36,
                'match_pattern' => 'BELGACOM|Proximus',
                'libelle_template' => 'BELGACOM {month}-{year}',
                'priority' => 18,
            ],
            [
                'name' => 'NETFLIX',
                'category_id' => 36,
                'match_pattern' => 'Netflix',
                'libelle_template' => 'NETFLIX {month}-{year}',
                'priority' => 18,
            ],
            [
                'name' => 'ORANGE',
                'category_id' => 36,
                'match_pattern' => 'ORANGE',
                'libelle_template' => 'ORANGE INTERNET/GSM/TV {month}-{year}',
                'priority' => 18,
            ],

            // =========================
            // EMPRUNTS / CREDITS
            // =========================
            [
                'name' => 'CREDIT HYPOTHECAIRE',
                'category_id' => 29,
                'match_pattern' => 'CREDIT HYPOTHECAIRE 145484',
                'libelle_template' => 'CREDIT HYPOTHECAIRE {month}-{year}',
                'priority' => 10,
            ],
            [
                'name' => 'EMPRUNT PAPA/MAMAN',
                'category_id' => 34,
                'match_pattern' => 'remboursement pret maison|Remboursement pret papa maman',
                'libelle_template' => 'EPARGNE {month}-{year}',
                'priority' => 10,
            ],

            // =========================
            // EPARGNE / PENSION
            // =========================
            [
                'name' => 'PENSION MICHAEL',
                'category_id' => 30,
                'match_pattern' => 'B PENSION FOUND',
                'libelle_template' => 'EPARGNE PENSION MICHAEL {month}-{year}',
                'priority' => 10,
            ],
            [
                'name' => 'PENSION AURELIE',
                'category_id' => 30,
                'match_pattern' => 'PENSION INVEST PLAN',
                'libelle_template' => 'EPARGNE PENSION AURELIE {month}-{year}',
                'priority' => 10,
            ],
            [
                'name' => 'EPARGNE GENERALE',
                'category_id' => 42,
                'match_pattern' => 'Epargne',
                'libelle_template' => 'EPARGNE',
                'priority' => 100,
            ],
            [
                'name' => 'EPARGNE BENJAMIN',
                'category_id' => 42,
                'match_pattern' => 'BENJAMIN THIEBAULT - BE84001507851559',
                'libelle_template' => 'EPARGNE BENJAMIN',
                'priority' => 20,
            ],
            [
                'name' => 'EPARGNE ELISE',
                'category_id' => 42,
                'match_pattern' => 'Elise Thiebault - BE95001507851458',
                'libelle_template' => 'EPARGNE ELISE',
                'priority' => 20,
            ],

            // =========================
            // PROVISION
            // =========================
            [
                'name' => 'PROVISION - MOT-CLE',
                'category_id' => 33,
                'match_pattern' => 'provision',
                'libelle_template' => 'PROVISION {month}-{year}',
                'priority' => 100,
            ],

            // =========================
            // ALIMENTATION / BOUFFE
            // =========================
            [
                'name' => 'ALIMENTATION - RESTAURANTS',
                'category_id' => 31,
                'match_pattern' => 'DELH|RESTAURANT ZEN|COLRUYT|DELITRAIT|MATCH BRAINE|TOM CO|LIDL|QUICK|MATCH WOLUWE|DELITRAITEUR|MM COMME A LA MAISON|WAPI SNACK|LONBOIS|MC DONALD|MARKET WATERLOO|DDMM|DE GELAS BRUNO|DAVID LAURENT BRAINE|WA ROSES SPRL|FOODIE\'S MARKET|FRITUUR SMULPLEZIER|SWEETS BRAINE|BRAINE FOOD|BURGER KING|LE PITOU|ARTISANALE DE|AD MERBRAINE|MA-LINE|Ad Braine Braine|CRF MKT WATERLO|Ctt buvette|La Cafet|TURKUAZ|THB6 1050 - BRUXELLES|Brothers Invest Group|O KEBAB|les amis du 179|Boucherie Ardennaise|Le Mediteraneen|MC Sweet\'s 1420|Les Amis du 17|Friends Zone|ST Business 1420|Delitreteur Braine|THE DONUT FACTOR|MM COMME A LA MAI|Qcash ping pong|Maison des Arts Suc|Ad Braine|M-JOY PRODUCTIONS|ROTISSERIE CA ROULE|IMOLAC 1410|LB 7791 WATERLOO 1410|MCDONALDS|Monsieur patate 1420|La Table du Parc 1400|LA MAISON DEMARET|CO&GO WATERLOO|PANOS|BRASSERIE ALLIANCE|Dunkin|LE BON PAIN|NYLA ROSE 1420|Accent Catalan|NIVELLES ICE CREAM|Takeaway.com|LES DELICES DE L IMPER|COFEO SERVICES|PANIER A PAIN WA|INTERMARCHE|LUNCH GARDEN|Pizza Hut|YIHENG 1420|Kameha Poke|Rotisserie Au P\'tit Poyon',
                'libelle_template' => 'ALIMENTATION {month}-{year}',
                'priority' => 25,
            ],
            [
                'name' => 'BOULANGERIE',
                'category_id' => 31,
                'match_pattern' => 'BOULANGERIE|MAISON THIRION|EVENT JABRAS',
                'libelle_template' => 'BOULANGERIE {month}-{year}',
                'priority' => 25,
            ],

            // =========================
            // SANTE / MEDICAL
            // =========================
            [
                'name' => 'MUTUALITE',
                'category_id' => 38,
                'match_pattern' => 'MUTUALITE|CARITAS|ASSURE HOSPI|Ass. Hospi|MC ASSURE|Dento +',
                'libelle_template' => 'MUTUALITE',
                'priority' => 20,
            ],
            [
                'name' => 'SOINS MEDICAL',
                'category_id' => 44,
                'match_pattern' => 'ELIOT-MAISIN|PHARM|HOP BRAINE|PH SERVAIS|PHAR|PH VALLEE BAIL|DENT-ART|HOPITAL DE BRAINE|ALSEMPHARMA|PHARMACIE DE L|SERVAIS BRAINE|CHIREC HBW|CABINET DENTAIRE|LE SOURIRE BY MARIE|Dokter Lannoy|PRATTE LAETITIA|Docteur Hublet Alexand|Ortho Medical Service|Iris sud|FAMILIA 46 WATERLOO 1410|Biologie lahulpe|Chirec|ORTHEIS|AZ DAMIAAN OOSTENDE|Jerome Hasselmans kine|Ambulance Ostende|Clinique Notre Dame de Grace|Servais Woo Centre 1410|Cinique notre dame de grace',
                'libelle_template' => 'SOINS',
                'priority' => 20,
            ],
            [
                'name' => 'GARDE MALADE',
                'category_id' => 45,
                'match_pattern' => 'BE28097000875020',
                'libelle_template' => 'GARDE MALADE {month}-{year}',
                'priority' => 20,
            ],

            // =========================
            // ENFANTS / EDUCATION
            // =========================
            [
                'name' => 'ECOLE ENFANT',
                'category_id' => 46,
                'match_pattern' => 'BE40096075994063|Ecole Vallee Bailly|Ecole Elise Vallee bailly|CFS etude Elise',
                'libelle_template' => 'ECOLE Enfant {month}-{year}',
                'priority' => 20,
            ],
            [
                'name' => 'ENFANTS - LOISIRS',
                'category_id' => 49,
                'match_pattern' => 'LUXUS|ORCHESTRA|CESAM NATURE|LES ARSOUILLES|OKAIDI|ZEEMAN|Roc events|Aquabla|Logiscool|Aqua club braine|Logischool|PISCINE NAUSICAA|Zinzolin|Sakura|CCM stages|Les Poulains de Colipa|Club gym loisir|Colipain',
                'libelle_template' => 'ENFANTS',
                'priority' => 22,
            ],

            // =========================
            // TITRE SERVICE
            // =========================
            [
                'name' => 'TITRE SERVICE',
                'category_id' => 48,
                'match_pattern' => 'Sodexo - titre service',
                'libelle_template' => 'TITRE SERVICE {month}-{year}',
                'priority' => 20,
            ],

            // =========================
            // MAISON / ENTRETIEN
            // =========================
            [
                'name' => 'BRICO',
                'category_id' => 40,
                'match_pattern' => 'BRICO',
                'libelle_template' => 'BRICO',
                'priority' => 30,
            ],
            [
                'name' => 'HUBO',
                'category_id' => 40,
                'match_pattern' => 'HUBO',
                'libelle_template' => 'HUBO',
                'priority' => 30,
            ],
            [
                'name' => 'VOLTIS',
                'category_id' => 40,
                'match_pattern' => 'VOLTIS',
                'libelle_template' => 'VOLTIS',
                'priority' => 30,
            ],
            [
                'name' => 'CHAUDIERE POELAERT',
                'category_id' => 40,
                'match_pattern' => 'POELAERT',
                'libelle_template' => 'CHAUDIERE POELAERT',
                'priority' => 30,
            ],
            [
                'name' => 'ENTRETIEN ROBOT TONDEUSE',
                'category_id' => 40,
                'match_pattern' => 'Chansay Olivier|Horti Tech',
                'libelle_template' => 'ENTRETIEN ROBOT TONDEUSE',
                'priority' => 30,
            ],
            [
                'name' => 'IKEA',
                'category_id' => 40,
                'match_pattern' => 'IKEA',
                'libelle_template' => 'MAISON IKEA',
                'priority' => 30,
            ],
            [
                'name' => 'MAISON TRAVAUX',
                'category_id' => 40,
                'match_pattern' => 'Mauvisin|GL pro|Induscabel',
                'libelle_template' => 'MAISON TRAVAUX',
                'priority' => 30,
            ],
            [
                'name' => 'TAXE COMMUNALE',
                'category_id' => 40,
                'match_pattern' => 'Administration communale',
                'libelle_template' => 'TAXE COMMUNALE',
                'priority' => 30,
            ],

            // =========================
            // CADASTRE
            // =========================
            [
                'name' => 'CADASTRE',
                'category_id' => 41,
                'match_pattern' => 'Precompte immobilier - BE09091215032457',
                'libelle_template' => 'CADASTRE {year}',
                'priority' => 20,
            ],

            // =========================
            // IMPOTS / BANQUE
            // =========================
            [
                'name' => 'IMPOT',
                'category_id' => 23,
                'match_pattern' => 'SPF Finances',
                'libelle_template' => 'IMPOT',
                'priority' => 10,
            ],
            [
                'name' => 'FRAIS BANQUE',
                'category_id' => 37,
                'match_pattern' => 'Décompte de frais|Intérêts-Frais',
                'libelle_template' => 'BANQUE FRAIS',
                'priority' => 40,
            ],

            // =========================
            // ACHATS DIVERS / SHOPPING
            // =========================
            [
                'name' => 'DREAMLAND',
                'category_id' => 27,
                'match_pattern' => 'DREAMLAND',
                'libelle_template' => 'DREAMLAND',
                'priority' => 35,
            ],
            [
                'name' => 'REMBOURSEMENT MASTERCARD',
                'category_id' => 27,
                'match_pattern' => 'MASTERCARD ING|ING CARD Carte 5206',
                'libelle_template' => 'REMBOURSEMENT MASTERCARD',
                'priority' => 35,
            ],
            [
                'name' => 'MULTIMEDIASHOP',
                'category_id' => 27,
                'match_pattern' => 'D-CONCEPT',
                'libelle_template' => 'MULTIMEDIASHOP',
                'priority' => 35,
            ],
            [
                'name' => 'MAISONS DU MONDE',
                'category_id' => 27,
                'match_pattern' => 'MAISONS DU MONDE',
                'libelle_template' => 'MAISONS DU MONDE',
                'priority' => 35,
            ],
            [
                'name' => 'ACHAT VETEMENTS',
                'category_id' => 27,
                'match_pattern' => 'AUDREY B|C&A|ELLE COUSINE|BESTSELLER RETAIL BEL 1400|NEW MANO|AMERICA TODAY|TAO 1410|Chaussea|PRONTI|Celio|SERGENT MAJOR|KIABI',
                'libelle_template' => 'ACHAT VETEMENTS',
                'priority' => 35,
            ],
            [
                'name' => 'ACHAT LIBRAIRIE',
                'category_id' => 27,
                'match_pattern' => 'ANGYCOPAS',
                'libelle_template' => 'ACHAT LIBRAIRIE',
                'priority' => 35,
            ],
            [
                'name' => 'ACHAT SPORT',
                'category_id' => 27,
                'match_pattern' => 'INTERSPORT|DECATHLON|Ctt braine|MAXIME WAUTHOZ',
                'libelle_template' => 'ACHAT SPORT',
                'priority' => 35,
            ],
            [
                'name' => 'ACHAT ACTIVITES',
                'category_id' => 27,
                'match_pattern' => 'Technopolis|GDM Organisation SPRL|Villers la Ville 1495|ALWAYS IN MOTION|Parc des Expositions d 1020|BattleKart Belgium|BOWL FACTORY|CINES WELLINGTON|Le monde de julie|GameForce|BECS 1020|NAUTISPORT|MONDIAL TISSUS|EXYPE|KINDERPLANEET|KINEPOLIS BRAINE 1420|Kinepolis|Kojump',
                'libelle_template' => 'ACHAT ACTIVITES',
                'priority' => 35,
            ],
            [
                'name' => 'ACHAT DIVERS',
                'category_id' => 27,
                'match_pattern' => 'MISTER MINIT|BRUSEL|LE ZEBRE A POIS|LCDN 1410|CARREFOUR MONT-S|FLEURS MARTINE|Tacosystems|KING JOUET|BE19310189608212|PAPETERIE WATERLOO|Action 2509|CLUB 117|MAXI TOYS BRAINE|LE BAOBAB LIVRES|CRF HYP MONT ST 1410|CLUB 141 BRAINE|VERITAS|JARDIN DES OLIVIERS LASNE|TRAFIC|HEMA|AVA BRAINE|KOOB|VANDEN BORRE|AQUATRE|CARREFOUR WATERL',
                'libelle_template' => 'ACHAT DIVERS',
                'priority' => 40,
            ],

            // =========================
            // SPORT / LOISIRS
            // =========================
            [
                'name' => 'SPORT COTISATION PING-PONG',
                'category_id' => 27,
                'match_pattern' => 'CTT braine - BE51034224070062|CTT braine cotisation - BE69088296605278',
                'libelle_template' => 'SPORT COTISATION PING-PONG',
                'priority' => 35,
            ],
            [
                'name' => 'SPORT COTISATION VOLLEY',
                'category_id' => 27,
                'match_pattern' => 'NRJ cotisation|Volley NRJ1 - BE17068907482921',
                'libelle_template' => 'SPORT COTISATION VOLLEY',
                'priority' => 35,
            ],

            // =========================
            // VACANCES
            // =========================
            [
                'name' => 'VACANCES',
                'category_id' => 47,
                'match_pattern' => 'Voyages olivier|VAB - BE62410032877161|De Haan|AUTOROUTES|BARJAC|BAGNOLS|Esf ski|PUY ST VINCEN|PUY-SAINT-VIN|GOUDARGUES|CORNILLON|RETHYMNO|W&L Company 1790|BRUGGE|TIGNES|NIMES|NIGLOLAND|CHATEL',
                'libelle_template' => 'VACANCE',
                'priority' => 35,
            ],

            // =========================
            // WEBSITE
            // =========================
            [
                'name' => 'NOM DE DOMAINE',
                'category_id' => 24,
                'match_pattern' => 'Belgates - BE62928094696661',
                'libelle_template' => 'NOM DE DOMAINE',
                'priority' => 40,
            ],
        ];

        foreach ($rules as $rule) {
            DB::table('rules')->insert([
                'name' => $rule['name'],
                'category_id' => $rule['category_id'],
                'match_pattern' => $rule['match_pattern'],
                'libelle_template' => $rule['libelle_template'] ?? $rule['name'],
                'priority' => $rule['priority'] ?? 100,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}