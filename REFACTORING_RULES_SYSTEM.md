# Refactorisation MyFinance - Système de Règles Dynamiques

## Vue d'ensemble

Le système de gestion des transactions financières a été refactorisé pour passer d'un système de requêtes SQL statiques à un système de **règles dynamiques modifiables via l'interface d'administration**. Cela permet d'améliorer continuellement la catégorisation des transactions sans modifier le code.

## Changements Majeurs

### 1. Nouvelles Tables et Migrations

#### Migration: `2026_06_23_000000_update_categories_table.php`

- Ajoute la colonne `group` (string, indexed) pour classifier les catégories par groupe
- Ajoute la colonne `color` (string, nullable) pour stocker la couleur de la catégorie

#### Groupes Disponibles

- `daily_life` - Dépenses quotidiennes (alimentation, loisirs, etc.)
- `transport` - Transports (carburant, auto, assurances auto)
- `housing` - Habitation (charges, entretien, cadastre)
- `health` - Santé (médecin, pharmacie, mutuelle)
- `income` - Revenus et épargne (salaire, pension, épargne)
- `transfert` - Transferts et emprunts
- `tax` - Impôts et taxes

### 2. Changements de Catégories

Les catégories suivantes ont été renommées:

- `BOUFFE` → `ALIMENTATION`
- `SOINS` → `SANTE`
- `ACHAT` → `DIVERS`
- `EMPRUNT PAPA/MAMAN` → `EPARGNE`

Une nouvelle catégorie a été ajoutée:

- `LOISIR`

Toutes les catégories ont reçu un groupe et une couleur.

### 3. Nouveaux Modèles Eloquent

#### Modèle `Rule`

```php
// Propriétés principales
- name: string - Nom descriptif de la règle
- match_pattern: text - Mots-clés séparés par | (pipe)
- category_id: bigint - Catégorie assignée
- libelle_template: string - Modèle de libellé (ex: "Carburant {month}-{year}")
- priority: int - Ordre d'application (0-1000, faible = prioritaire)
- active: boolean - État d'activation
- created_at, updated_at
```

Relations:

- `belongsTo(Categorie)` - La catégorie associée

Méthodes utiles:

- `matches($details)` - Vérifie si une description correspond à la règle
- `formatLibelle($date)` - Formate le libellé avec les variables du template
- `scopeActive($query)` - Filtre les règles actives
- `scopeByPriority($query)` - Ordonne par priorité

#### Modèle `Categorie` (mise à jour)

- Ajoute une relation `hasMany(Rule)`
- Ajoute la méthode `getColor()` qui utilise la colonne `color` si disponible
- Conservation de la compatibilité avec les couleurs pré-calculées

### 4. Nouvelle Table de Règles

La migration existante `2026_06_05_175647_create_rules_table.php` a été mise à jour pour:

- Ajouter la colonne `libelle_template`
- Ajouter une clé étrangère vers `categories`

### 5. Seeder: `RulesSeeder`

Crée automatiquement toutes les règles (58 règles) avec:

- Les motifs extraits du fichier `UpdateRecords.php` original
- Les priorités appropriées
- Les templates de libellé
- L'activation par défaut

### 6. Interface d'Administration

#### Routes (sous `/admin/finance/rules`)

- `GET /` - Liste toutes les règles
- `GET /create` - Formulaire de création
- `POST /` - Créer une règle
- `GET /{rule}/edit` - Formulaire d'édition
- `PUT /{rule}` - Mettre à jour une règle
- `DELETE /{rule}` - Supprimer une règle
- `POST /{rule}/toggle` - Activer/Désactiver une règle

#### Views

- `backend/finance/rules/index.blade.php` - Liste avec pagination
- `backend/finance/rules/form.blade.php` - Formulaire de création/édition

#### Menu

Un nouveau lien "Rules" a été ajouté à la section "MyFinance" du menu latéral.

### 7. Refactorisation `UpdateRecords.php`

Le fichier `app/Models/UpdateRecords.php` a été complètement refactorisé:

- L'ancienne méthode `doQueries()` est supprimée
- Nouvelle approche: lecture des règles actives depuis la DB
- Parcours chaque règle et applique le matching
- Mise à jour atomique pour chaque transaction matching

Avantages:

- ✅ Pas besoin de redéployer le code pour ajouter/modifier une règle
- ✅ Les règles sont testables et modifiables en temps réel
- ✅ Meilleur logging et traçabilité
- ✅ Gestion d'erreurs robuste
- ✅ Support des priorités pour l'ordre d'application

## Installation et Utilisation

### 1. Exécuter les Migrations

```bash
php artisan migrate
```

Cela va:

1. Créer les colonnes `group` et `color` sur la table `categories`
2. Mettre à jour la table `rules` (si elle n'existe pas déjà)

### 2. Exécuter les Seeders

```bash
# Mettre à jour les catégories
php artisan db:seed --class=UpdateCategoriesSeeder

# Créer les règles initiales
php artisan db:seed --class=RulesSeeder
```

### 3. Accéder à l'Interface

1. Allez sur `/admin/finance/rules`
2. Vous verrez la liste des 58 règles initiales
3. Vous pouvez:
   - **Éditer** une règle existante pour modifier ses motifs
   - **Créer** une nouvelle règle
   - **Activer/Désactiver** une règle sans la supprimer
   - **Supprimer** une règle

### 4. Tester l'Import de Transactions

Lors de l'import de transactions:

1. Le système lit automatiquement les règles actives
2. Pour chaque transaction, les règles sont appliquées par priorité
3. La première règle correspondante est utilisée
4. La transaction est automatiquement catégorisée et validée

## Syntaxe des Motifs

### Format des Mots-clés

```
mot1|mot2|mot3
```

Chaque mot-clé est séparé par un pipe `|`.

### Caractéristiques

- ✅ Recherche **insensible à la casse**
- ✅ Support des espaces (importants pour la précision)
- ✅ Pas de regex (recherche simple avec `stripos`)

### Exemples

```
// Carburant
SHELL|DATS 24|ESSO

// Alimentation
DELH|RESTAURANT ZEN|COLRUYT|LIDL|QUICK

// Belgacom
BELGACOM|Proximus
```

## Modèle de Libellé

### Variables Disponibles

- `{month}` - Mois en chiffres (01-12)
- `{month_text}` - Mois texte (janvier, février...)
- `{year}` - Année complète (2026)

### Exemples

```
// Résultat: "Carburant 06-2026"
Carburant {month}-{year}

// Résultat: "ASSURANCE AUTO juin-2026"
ASSURANCE AUTO {month_text}-{year}

// Résultat: "IMPOT 2026"
IMPOT {year}
```

## Priorité des Règles

Les règles sont appliquées par ordre de priorité croissant (0 = très prioritaire, 1000+ = moins prioritaire).

### Recommandations

- **0-20**: Règles très spécifiques (ex: code bancaire unique)
- **20-50**: Règles spécifiques (ex: fournisseur connu)
- **50-100**: Règles générales courantes
- **100+**: Règles de fallback / catch-all

## Compatibilité

- ✅ Toutes les données existantes sont préservées
- ✅ Les anciennes transactions restent intactes
- ✅ La méthode `UpdateCategory($category, $query)` reste disponible pour compatibilité
- ✅ Les couleurs pré-calculées restent compatibles

## Idées d'Amélioration Future

1. **Regex Support**: Permettre des motifs regex plus complexes
2. **Webhooks**: Notifier quand une règle matche une transaction pour vérification
3. **Templates Avancés**: Support de conditions (montant, compte source, etc.)
4. **Analytics**: Dashboard montrant l'efficacité de chaque règle
5. **Bulk Operations**: Activer/désactiver/supprimer plusieurs règles à la fois
6. **Rule Testing**: Interface pour tester les règles avant d'les appliquer
7. **Import/Export**: Sauvegarder et restaurer les configurations de règles
8. **Suggestions Automatiques**: Proposer des règles basées sur les transactions non catégorisées

## Dépannage

### Les règles ne s'appliquent pas

1. Vérifiez que les règles sont **actives**
2. Vérifiez les motifs (attention à la casse des espaces)
3. Vérifiez les priorités
4. Regardez les logs: `storage/logs/laravel.log`

### Une transaction a reçu la mauvaise catégorie

1. Vérifiez les motifs avec l'interface
2. Ajustez la priorité pour que votre règle soit appliquée avant
3. Créez une règle plus spécifique
4. Testez avec l'interface de maintenance

### Performance: l'import est lent

- Vérifiez le nombre de transactions
- Réduisez le nombre de règles actives inutiles
- Les priorités haute ralentissent l'application

## Support

Pour plus d'informations ou un dépannage avancé:

- Consultez les logs: `storage/logs/laravel.log`
- Vérifiez la base de données directement: `select * from rules where active = 1 order by priority`
- Les modèles Eloquent incluent des commentaires de code expliquant chaque méthode
