<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRecords extends Model
{
    /**
     * Exécuter toutes les règles pour mettre à jour automatiquement les catégories des transactions
     */
    static function doQueries()
    {
        $messages = array();
        
        try {
            // Récupérer toutes les règles actives, ordonnées par priorité
            $rules = Rule::active()
                        ->byPriority()
                        ->get();

            Log::info("[UpdateRecords] - Début de l'application des règles. Nombre de règles: " . $rules->count());

            if ($rules->isEmpty()) {
                $messages[] = "Aucune règle active trouvée. Veuillez créer des règles avant d'importer.";
                Log::warning("[UpdateRecords] - Aucune règle active trouvée");
                return $messages;
            }

            // Pour chaque règle, appliquer la mise à jour
            foreach ($rules as $rule) {
                $affectedCount = self::applyRule($rule);
                if ($affectedCount > 0) {
                    $messages[] = $affectedCount . ' transaction(s) mise(s) à jour pour la règle: ' . $rule->name;
                    Log::info("[UpdateRecords] - Règle appliquée: " . $rule->name . " (" . $affectedCount . " transactions)");
                }
            }

            if (empty($messages)) {
                $messages[] = "Aucune transaction ne correspondait aux règles actives.";
            }

        } catch (\Exception $e) {
            Log::error("[UpdateRecords] - Erreur lors de l'application des règles: " . $e->getMessage());
            $messages[] = "Erreur lors de l'application des règles: " . $e->getMessage();
        }

        return $messages;
    }

    /**
     * Appliquer une règle spécifique à toutes les transactions non validées
     */
    private static function applyRule(Rule $rule)
    {
        try {
            // Récupérer toutes les transactions non validées
            $records = Record::where('validate', 0)
                            ->where('deleted', 0)
                            ->get();

            Log::info("[UpdateRecords] Traitement de la règle '{$rule->name}' pour " . $records->count() . " transactions");

            $affectedCount = 0;

            foreach ($records as $record) {
                // Le pattern peut être vide - skip silencieusement
                if (empty($rule->match_pattern)) {
                    Log::debug("[UpdateRecords] Règle '{$rule->name}' a un pattern vide, ignorée");
                    continue;
                }

                $details = $record->details ?? '';
                
                // Vérifier si la transaction correspond à la règle
                if ($rule->matches($details)) {
                    // Formater le libellé
                    $libelle = $rule->formatLibelle($record->date ? \Carbon\Carbon::parse($record->date) : now());

                    Log::debug("[UpdateRecords] Transaction " . $record->id . " correspond à la règle '{$rule->name}'");

                    // Mettre à jour la transaction
                    $record->update([
                        'libelle' => $libelle,
                        'fk_id_categorie' => $rule->category_id,
                        'validate' => 1,
                    ]);

                    $affectedCount++;
                } else {
                    // Log DEBUG pour voir pourquoi ça ne correspond pas
                    if ($affectedCount === 0) {
                        Log::debug(
                            "[UpdateRecords] Transaction " . $record->id . " NE correspond PAS à la règle '{$rule->name}'. " .
                            "Pattern: '{$rule->match_pattern}' vs Details: '" . substr($details, 0, 100) . "'"
                        );
                    }
                }
            }

            return $affectedCount;

        } catch (\Exception $e) {
            Log::error("[UpdateRecords] - Erreur lors de l'application de la règle " . $rule->name . ": " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Ancienne méthode (conservée pour compatibilité)
     * À utiliser uniquement si les règles ne sont pas disponibles
     */
    static function UpdateCategory($category, $query)
    {
        $affected = DB::update($query);
        return $affected . ' records ont &eacute;t&eacute; mis &agrave; jour pour la cat&eacute;gorie ' . $category;
    }
}