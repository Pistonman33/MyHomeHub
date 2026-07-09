<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Rule;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RuleController extends Controller
{
    /**
     * Afficher la liste des règles
     */
    public function index(Request $request)
    {
        $selectedCategoryId = $request->input('category');

        $rules = Rule::with('category')
                    ->when($selectedCategoryId, function ($query) use ($selectedCategoryId) {
                        return $query->where('category_id', $selectedCategoryId);
                    })
                    ->byPriority()
                    ->paginate(20)
                    ->appends($request->only('category'));

        $categories = Categorie::whereHas('rules')
            ->orderBy('nom')
            ->get();

        return view('backend.finance.rules.index', [
            'rules' => $rules,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
        ]);
    }

    /**
     * Formulaire de création d'une nouvelle règle
     */
    public function create()
    {
        $categories = Categorie::orderBy('nom')->get();

        return view('backend.finance.rules.form', [
            'rule' => null,
            'categories' => $categories,
        ]);
    }

    /**
     * Stocker une nouvelle règle en DB
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'match_pattern' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'libelle_template' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|min:0|max:1000',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            Rule::create([
                'name' => $request->name,
                'match_pattern' => $request->match_pattern,
                'category_id' => $request->category_id,
                'libelle_template' => $request->libelle_template ?? $request->name,
                'priority' => $request->priority ?? 100,
                'active' => $request->active ?? true,
            ]);

            Log::info("[RuleController] - Nouvelle règle créée: " . $request->name);
            return redirect()->route('admin.finance.rules.index')->with('success', 'Règle créée avec succès');
        } catch (\Exception $e) {
            Log::error("[RuleController] - Erreur lors de la création de la règle: " . $e->getMessage());
            return back()->withErrors(['general' => 'Erreur lors de la création de la règle'])->withInput();
        }
    }

    /**
     * Formulaire d'édition d'une règle
     */
    public function edit(Rule $rule)
    {
        $categories = Categorie::orderBy('nom')->get();

        return view('backend.finance.rules.form', [
            'rule' => $rule,
            'categories' => $categories,
        ]);
    }

    /**
     * Mettre à jour une règle
     */
    public function update(Request $request, Rule $rule)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'match_pattern' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'libelle_template' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|min:0|max:1000',
            'active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $rule->update([
                'name' => $request->name,
                'match_pattern' => $request->match_pattern,
                'category_id' => $request->category_id,
                'libelle_template' => $request->libelle_template ?? $request->name,
                'priority' => $request->priority ?? 100,
                'active' => $request->active ?? true,
            ]);

            Log::info("[RuleController] - Règle mise à jour: " . $rule->name);
            return redirect()->route('admin.finance.rules.index')->with('success', 'Règle mise à jour avec succès');
        } catch (\Exception $e) {
            Log::error("[RuleController] - Erreur lors de la mise à jour de la règle: " . $e->getMessage());
            return back()->withErrors(['general' => 'Erreur lors de la mise à jour de la règle'])->withInput();
        }
    }

    /**
     * Supprimer une règle
     */
    public function destroy(Rule $rule)
    {
        try {
            $ruleName = $rule->name;
            $rule->delete();
            Log::info("[RuleController] - Règle supprimée: " . $ruleName);
            return back()->with('success', 'Règle supprimée avec succès');
        } catch (\Exception $e) {
            Log::error("[RuleController] - Erreur lors de la suppression de la règle: " . $e->getMessage());
            return back()->withErrors(['general' => 'Erreur lors de la suppression de la règle']);
        }
    }

    /**
     * Activer/Désactiver une règle
     */
    public function toggleActive(Rule $rule)
    {
        try {
            $rule->active = !$rule->active;
            $rule->save();
            Log::info("[RuleController] - Règle " . ($rule->active ? 'activée' : 'désactivée') . ": " . $rule->name);
            return back()->with('success', 'Règle ' . ($rule->active ? 'activée' : 'désactivée') . ' avec succès');
        } catch (\Exception $e) {
            Log::error("[RuleController] - Erreur lors de l'activation/désactivation de la règle: " . $e->getMessage());
            return back()->withErrors(['general' => 'Erreur lors de l\'activation/désactivation de la règle']);
        }
    }

    /**
     * Afficher les détails d'une règle
     */
    public function show(Rule $rule)
    {
        return view('backend.finance.rules.show', [
            'rule' => $rule->load('category'),
        ]);
    }
}
