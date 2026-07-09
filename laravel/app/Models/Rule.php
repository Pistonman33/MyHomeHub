<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $table = 'rules';
    protected $fillable = ['name', 'match_pattern', 'category_id', 'active', 'priority', 'libelle_template'];

    /**
     * Relation avec Categorie
     */
    public function category()
    {
        return $this->belongsTo(Categorie::class, 'category_id');
    }

    /**
     * Scope pour les règles actives
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope pour ordonner par priorité
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Vérifier si une description correspond à cette règle
     * Utilise des patterns simples (keywords avec LIKE)
     */
    public function matches($details)
    {
        if (empty($this->match_pattern)) {
            return false;
        }

        // Gérer les détails vides/null
        if (empty($details)) {
            return false;
        }

        $matchType = $this->match_type ?? 'KEYWORD';

        switch ($matchType) {
            case 'REGEX':
                // Pattern regex
                try {
                    return preg_match('/' . $this->match_pattern . '/i', $details) === 1;
                } catch (\Exception $e) {
                    \Log::warning("[Rule] Invalid regex pattern: " . $this->match_pattern);
                    return false;
                }

            case 'EXACT':
                // Correspondance exacte (case-insensitive)
                return strtolower($details) === strtolower(trim($this->match_pattern));

            case 'KEYWORD':
            default:
                // Le match_pattern contient des mots-clés séparés par |
                // Chaque mot-clé est testé avec une recherche LIKE (insensible à la casse)
                $patterns = array_map('trim', explode('|', $this->match_pattern));

                foreach ($patterns as $pattern) {
                    if (!empty($pattern) && stripos($details, $pattern) !== false) {
                        return true;
                    }
                }
                return false;
        }
    }

    /**
     * Formater le libellé en fonction du template
     */
    public function formatLibelle($date = null)
    {
        $template = $this->libelle_template ?? $this->name;

        if ($date === null) {
            $date = now();
        }

        // Remplacer les variables du template
        $template = str_replace(['{month}', '{month_text}', '{year}'], [
            sprintf('%02d', $date->month),
            $date->monthName,
            $date->year
        ], $template);

        return $template;
    }
}