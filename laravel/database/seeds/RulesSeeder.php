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
                'libelle_template' => "EASI SA {month}-{year}",
                'keywords' => json_encode([
                    'EASI SA'
                ]),
            ],
            [
                'name' => 'ARKAOS SA',
                'category_id' => 22,
                'libelle_template' => "ARKAOS SA {month}-{year}",
                'keywords' => json_encode([
                    'ARKAOS',
                    'INMUSIC EUROPE LIMITED'
                ]),
            ],

            // =========================
            // ALLOCATIONS FAMILIALES
            // =========================
            [
                'name' => 'ALLOCATIONS FAMILIALES',
                'category_id' => 43,
                'libelle_template' => "ALLOCATION FAMILIALE {month}-{year}",
                'keywords' => json_encode([
                    'CAF SECUREX',
                    'CAISSE D ALLOCATIONS FAMIL',
                    'PARTENA - CAISSE DECOMPENS',
                    'PARENTIA'
                ]),
            ],

            // =========================
            // ASSURANCES
            // =========================
            [
                'name' => 'ASSURANCE MAISON',
                'category_id' => 17,
                'libelle_template' => "ASS. MAISON {month}-{year}",
                'keywords' => json_encode([
                    'AG INSURANCE'
                ]),
            ],
            [
                'name' => 'ASSURANCE AUTO',
                'category_id' => 19,
                'libelle_template' => "ASSURANCE AUTO {month}-{year}",
                'keywords' => json_encode([
                    'ING Auto'
                ]),
            ],

            // =========================
            // BANQUE / FRAIS
            // =========================
            [
                'name' => 'FRAIS BANQUE',
                'category_id' => 37,
                'libelle_template' => "BANQUE: FRAIS",
                'keywords' => json_encode([
                    'Décompte de frais',
                    'Intérêts-Frais'
                ]),
            ],

            // =========================
            // IMPOTS
            // =========================
            [
                'name' => 'IMPOT',
                'category_id' => 23,
                'libelle_template' => "IMPOT",
                'keywords' => json_encode([
                    'SPF Finances'
                ]),
            ],

            // =========================
            // TV / INTERNET
            // =========================
            [
                'name' => 'TV INTERNET',
                'category_id' => 36,
                'libelle_template' => "BELGACOM {month}-{year}",
                'keywords' => json_encode([
                    'BELGACOM',
                    'Proximus'
                ]),
            ],
            [
                'name' => 'NETFLIX',
                'category_id' => 36,
                'libelle_template' => "NETFLIX {month}-{year}",
                'keywords' => json_encode([
                    'Netflix'
                ]),
            ],

            // =========================
            // CARBURANT
            // =========================
            [
                'name' => 'CARBURANT',
                'category_id' => 21,
                'libelle_template' => "CARBURANT {month}-{year}",
                'keywords' => json_encode([
                    'SHELL',
                    'DATS 24'
                ]),
            ],

            // =========================
            // MAISON / ENTRETIEN
            // =========================
            [
                'name' => 'BRICO',
                'category_id' => 40,
                'libelle_template' => "BRICO",
                'keywords' => json_encode([
                    'BRICO'
                ]),
            ],
            [
                'name' => 'HUBO',
                'category_id' => 40,
                'libelle_template' => "HUBO",
                'keywords' => json_encode([
                    'HUBO'
                ]),
            ],
            [
                'name' => 'IKEA',
                'category_id' => 40,
                'libelle_template' => "MAISON IKEA",
                'keywords' => json_encode([
                    'IKEA'
                ]),
            ],

            // =========================
            // 👉 COLLE TES 295 RÈGLES ICI
            // =========================
        ];

        foreach ($rules as $rule) {
            DB::table('rules')->insert([
                'name' => $rule['name'],
                'category_id' => $rule['category_id'],
                'libelle_template' => $rule['libelle_template'],
                'keywords' => $rule['keywords'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}