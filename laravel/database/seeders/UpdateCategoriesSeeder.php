<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCategoriesSeeder extends Seeder
{
    // Couleurs existantes du système
    private static $colors = [
        "#ECDB54","#E94B3C","#6F9FD8","#944743","#DBB1CD","#EC9787","#00A591","#6B5B95","#6C4F3D",
        "#BC70A4","#BFD641","#2E4A62","#B4B7BA","#C0AB8E","#92B558","#DC4C46","#672E3B","#C48F65",
        "#223A5E","#898E8C","#005960","#9C9A40","#4F84C4","#D2691E","#578CA9","#F6D155","#004B8D",
        "#F2552C","#95DEE3","#EDCDC2","#CE3175","#5A7247","#CFB095","#4C6A92","#92B6D5","#838487",
        "#B93A32","#AF9483","#AD5D5D","#006E51","#D8AE47","#9E4624","#B76BA3"
    ];

    /**
     * Mapping des catégories existantes avec leurs groupes
     */
    private $categoryGroups = [
        16 => 'income',           // ID 16
        17 => 'housing',          // ASSURANCE MAISON
        18 => 'housing',          // ASSURANCE SOLDE RESTANT DU
        19 => 'transport',        // ASSURANCE AUTO
        20 => 'transport',        // AUTO
        21 => 'transport',        // CARBURANT
        22 => 'income',           // SALAIRE
        23 => 'tax',              // IMPOT
        24 => 'daily_life',       // WEBSITE/NOM DE DOMAINE
        25 => 'housing',          // CHARGE ELEC/GAZ
        26 => 'housing',          // CHARGE EAU
        27 => 'daily_life',       // ACHAT -> DIVERS
        28 => 'daily_life',       // GSM
        29 => 'transfert',        // EMPRUNT
        30 => 'income',           // PENSION / EPARGNE
        31 => 'daily_life',       // BOUFFE -> ALIMENTATION
        33 => 'income',           // PROVISION
        34 => 'income',           // EMPRUNT PAPA/MAMAN -> EPARGNE
        36 => 'daily_life',       // TV/INTERNET
        37 => 'tax',              // BANQUE FRAIS
        38 => 'health',           // MUTUALITE
        40 => 'housing',          // MAISON ENTRETIEN
        41 => 'housing',          // CADASTRE
        42 => 'income',           // EPARGNE
        43 => 'income',           // ALLOCATION FAMILIALE
        44 => 'health',           // SOINS -> SANTE
        45 => 'health',           // GARDE MALADE
        46 => 'daily_life',       // ECOLE
        47 => 'daily_life',       // VACANCE
        48 => 'transfert',        // TITRE SERVICE
        49 => 'daily_life',       // ENFANTS
    ];

    /**
     * Renaming des catégories
     */
    private $categoryRenames = [
        'BOUFFE' => 'ALIMENTATION',
        'SOINS' => 'SANTE',
        'ACHAT' => 'DIVERS',
        'EMPRUNT PAPA/MAMAN' => 'EPARGNE',
    ];

    public function run(): void
    {
        // 1. Renommer les catégories existantes
        foreach ($this->categoryRenames as $oldName => $newName) {
            DB::table('categories')
                ->where('nom', $oldName)
                ->update(['nom' => $newName]);
        }

        // 2. Ajouter la nouvelle catégorie LOISIR si elle n'existe pas
        if (!DB::table('categories')->where('nom', 'LOISIR')->exists()) {
            DB::table('categories')->insert([
                'nom' => 'LOISIR',
            ]);
        }

        // 3. Mettre à jour les groupes pour chaque catégorie
        foreach ($this->categoryGroups as $categoryId => $group) {
            // Récupérer la catégorie pour obtenir son nouvel ID si elle a un nouveau nom
            $category = DB::table('categories')->find($categoryId);
            if ($category) {
                $color = $this->getColorForCategory($categoryId);
                DB::table('categories')
                    ->where('id', $categoryId)
                    ->update([
                        'group' => $group,
                        'color' => $color,
                    ]);
            }
        }

        // 4. Assigner aussi le groupe LOISIR à la nouvelle catégorie
        DB::table('categories')
            ->where('nom', 'LOISIR')
            ->update([
                'group' => 'daily_life',
                'color' => $this->getColorForCategory(50), // Prochaine couleur
            ]);
    }

    /**
     * Obtenir la couleur pour une catégorie
     */
    private function getColorForCategory($categoryId)
    {
        // Les couleurs sont indexées à partir de l'ID de catégorie - 16
        $colorIndex = $categoryId - 16;
        if ($colorIndex >= 0 && $colorIndex < count(self::$colors)) {
            return self::$colors[$colorIndex];
        }
        // Si l'index est hors limites, utiliser une couleur par défaut ou recycler
        return self::$colors[($categoryId - 1) % count(self::$colors)];
    }
}
