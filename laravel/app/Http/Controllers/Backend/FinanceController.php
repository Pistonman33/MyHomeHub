<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Record;
use App\Models\Categorie;
use App\Models\Compte;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Debug\Exception\FatalErrorException;
use App\Models\Display;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    private $messages =array();

    var $display_year = array();
    var $display_month = array();
    var $transactions;
    var $revenu_depense_charts;
    var $revenu_categories_charts;
    var $depenses_categories_charts;
    var $date_transaction;

    public function home(Request $request) {
        $this->home_manage_year_field($request);
        $this->home_manage_month_field($request);
        $this->date_transaction = $this->display_year['filter_year'].'-'.sprintf("%02d", $this->display_month['filter_month']);
        $this->home_manage_transactions();
        $this->home_manage_chart_revenue();
        $this->home_manage_chart_revenue_by_categories();
        $this->home_manage_chart_depenses_by_categories();

        return view('backend.finance.home')->with('year',$this->display_year)
                                    ->with('month',$this->display_month)
                                    ->with('revenu_depense_charts',$this->revenu_depense_charts)
                                    ->with('revenu_categories_charts',$this->revenu_categories_charts)
                                    ->with('depenses_categories_charts',$this->depenses_categories_charts)
                                    ->with('transactions',$this->transactions);
    }

    public function annualDashboard(Request $request)
    {
        $years = Record::where('validate', 1)
            ->where('deleted', 0)
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $year = (int) ($request->input('year') ?: ($years->first() ?: date('Y')));
        if (!$years->contains($year)) {
            $year = (int) ($years->first() ?: date('Y'));
        }

        $month = $request->filled('month') && (int) $request->input('month') >= 1 && (int) $request->input('month') <= 12
            ? (int) $request->input('month')
            : null;
        $dashboard = in_array($request->input('view'), ['menage', 'moi', 'evolution'], true)
            ? $request->input('view')
            : 'menage';

        $accounts = Compte::orderBy('nom')->get();
        $normalize = static function ($value) {
            return mb_strtoupper(trim((string) $value), 'UTF-8');
        };
        $commonAccountIds = $accounts->filter(function ($account) use ($normalize) {
            return $normalize($account->nom) === 'COMPTE COMMUN';
        })->pluck('compteid')->all();
        $personalAccountIds = $accounts->filter(function ($account) use ($normalize) {
            return $normalize($account->nom) === 'THIEBAULT MICHAEL';
        })->pluck('compteid')->all();

        $accountFilter = in_array($request->input('account'), ['common', 'personal', 'all'], true)
            ? $request->input('account')
            : ($dashboard === 'moi' ? 'personal' : 'common');
        if ($dashboard === 'moi') {
            $accountFilter = 'personal';
        }
        $accountIds = $accountFilter === 'common'
            ? $commonAccountIds
            : ($accountFilter === 'personal' ? $personalAccountIds : $accounts->pluck('compteid')->all());

        $categoriesById = Categorie::all()->keyBy('id');
        $categoryTypes = $categoriesById->mapWithKeys(function ($category) {
            return [$category->id => $this->dashboardCategoryType($category)];
        });
        $expenseCategories = $categoriesById->filter(function ($category) {
            return $this->dashboardCategoryType($category) === 'expense';
        });
        $requestedCategoryIds = collect($request->input('categories', []))
            ->filter(function ($id) {
                return filter_var($id, FILTER_VALIDATE_INT) !== false;
            })
            ->map(function ($id) {
                return (int) $id;
            });
        $selectedCategoryIds = $requestedCategoryIds->isNotEmpty()
            ? $expenseCategories->keys()->intersect($requestedCategoryIds)->values()
            : $expenseCategories->keys()->values();

        $currentStart = $month
            ? sprintf('%04d-%02d-01', $year, $month)
            : $year . '-01-01';
        $currentEnd = $month
            ? date('Y-m-d', strtotime($currentStart . ' +1 month'))
            : ($year + 1) . '-01-01';
        $transactions = Record::with('category')
            ->where('validate', 1)
            ->where('deleted', 0)
            ->where('date', '>=', $currentStart)
            ->where('date', '<', $currentEnd)
            ->whereIn('fk_id_compte', $accountIds)
            ->get();

        $summary = $this->summarizeDashboardTransactions($transactions, $categoryTypes, $selectedCategoryIds);
        $revenues = $summary['revenues'];
        $salary = $summary['salary'];
        $websiteRevenue = $summary['websiteRevenue'];
        $otherRevenues = $summary['otherRevenues'];
        $provisions = $summary['provisions'];
        $expenses = $summary['expenses'];
        $categories = $summary['categories'];

        $salaryCategoryIds = $categoryTypes->filter(function ($type) {
            return $type === 'salary';
        })->keys()->all();
        $websiteCategoryIds = $categoryTypes->filter(function ($type) {
            return $type === 'website';
        })->keys()->all();
        $historicalIncome = collect();
        $incomeCategoryIds = array_values(array_unique(array_merge($salaryCategoryIds, $websiteCategoryIds)));
        if ($incomeCategoryIds && $accountIds) {
            $historicalIncome = Record::query()
                ->join('categories', 'records.fk_id_categorie', '=', 'categories.id')
                ->where('records.validate', 1)
                ->where('records.deleted', 0)
                ->where('records.retrait', 0)
                ->whereIn('records.fk_id_compte', $accountIds)
                ->whereIn('records.fk_id_categorie', $incomeCategoryIds)
                ->selectRaw('YEAR(date) as year, MONTH(date) as month, fk_id_categorie, SUM(montant) as total')
                ->groupBy('year', 'month', 'records.fk_id_categorie')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        $salaryByYearMonth = [];
        $websiteByYearMonth = [];
        foreach ($historicalIncome as $row) {
            $target = in_array((int) $row->fk_id_categorie, $salaryCategoryIds, true)
                ? 'salary'
                : 'website';
            $key = (int) $row->year . '-' . (int) $row->month;
            if ($target === 'salary') {
                $salaryByYearMonth[$key] = ($salaryByYearMonth[$key] ?? 0) + (float) $row->total;
            } else {
                $websiteByYearMonth[$key] = ($websiteByYearMonth[$key] ?? 0) + (float) $row->total;
            }
        }

        $historicalYears = $historicalIncome
            ->filter(function ($row) use ($salaryCategoryIds) {
                return in_array((int) $row->fk_id_categorie, $salaryCategoryIds, true);
            })
            ->pluck('year')
            ->unique()
            ->sort()
            ->values();
        $salaryMonthlyHistory = collect(range(1, 12))->map(function ($monthNumber) use ($historicalYears, $salaryByYearMonth) {
            $values = [];
            foreach ($historicalYears as $historicalYear) {
                $values[$historicalYear] = $salaryByYearMonth[(int) $historicalYear . '-' . $monthNumber] ?? 0;
            }
            return ['month' => $monthNumber, 'values' => $values];
        });
        $salaryTimeline = $historicalIncome
            ->filter(function ($row) use ($salaryCategoryIds) {
                return in_array((int) $row->fk_id_categorie, $salaryCategoryIds, true);
            })
            ->groupBy(function ($row) {
                return (int) $row->year . '-' . (int) $row->month;
            })
            ->map(function ($rows, $key) {
                return ['period' => $key, 'amount' => (float) $rows->sum('total')];
            })->values();

        $expenseHistory = collect();
        if ($selectedCategoryIds->isNotEmpty() && $accountIds) {
            $expenseHistory = Record::query()
                ->where('validate', 1)
                ->where('deleted', 0)
                ->whereIn('fk_id_compte', $accountIds)
                ->whereIn('fk_id_categorie', $selectedCategoryIds->all())
                ->selectRaw('YEAR(date) as year, fk_id_categorie, SUM(CASE WHEN retrait = 1 THEN montant ELSE -montant END) as total')
                ->groupBy('year', 'fk_id_categorie')
                ->orderBy('year')
                ->get();
        }

        $expenseComparison = $selectedCategoryIds->map(function ($categoryId) use ($categoriesById, $expenseHistory, $year) {
            $category = $categoriesById->get($categoryId);
            $byYear = $expenseHistory->where('fk_id_categorie', $categoryId)->keyBy('year');
            return [
                'name' => $category ? $category->nom : 'Sans catégorie',
                'color' => $category ? $category->getColor() : '#adb5bd',
                'values' => $byYear->mapWithKeys(function ($row) {
                    return [$row->year => (float) $row->total];
                })->all(),
                'current' => (float) ($byYear->get($year)->total ?? 0),
            ];
        })->sortByDesc('current')->values();

        $monthlyExpenseHistory = collect();
        if ($selectedCategoryIds->isNotEmpty() && $accountIds) {
            $monthlyExpenseHistory = Record::query()
                ->where('validate', 1)
                ->where('deleted', 0)
                ->where('date', '>=', $year . '-01-01')
                ->where('date', '<', ($year + 1) . '-01-01')
                ->whereIn('fk_id_compte', $accountIds)
                ->whereIn('fk_id_categorie', $selectedCategoryIds->all())
                ->selectRaw('MONTH(date) as month, fk_id_categorie, SUM(CASE WHEN retrait = 1 THEN montant ELSE -montant END) as total')
                ->groupBy('month', 'fk_id_categorie')
                ->orderBy('month')
                ->get();
        }
        $monthlyExpenseComparison = $selectedCategoryIds->map(function ($categoryId) use ($categoriesById, $monthlyExpenseHistory) {
            $category = $categoriesById->get($categoryId);
            $byMonth = $monthlyExpenseHistory->where('fk_id_categorie', $categoryId)->keyBy('month');

            return [
                'name' => $category ? $category->nom : 'Sans catégorie',
                'color' => $category ? $category->getColor() : '#adb5bd',
                'values' => collect(range(1, 12))->mapWithKeys(function ($monthNumber) use ($byMonth) {
                    return [$monthNumber => (float) ($byMonth->get($monthNumber)->total ?? 0)];
                }),
            ];
        })->sortByDesc(function ($category) {
            return $category['values']->sum();
        })->values();

        return view('backend.finance.dashboard', [
            'dashboard' => $dashboard,
            'year' => $year,
            'years' => $years,
            'month' => $month,
            'accountFilter' => $accountFilter,
            'commonAccountConfigured' => !empty($commonAccountIds),
            'personalAccountConfigured' => !empty($personalAccountIds),
            'revenues' => $revenues,
            'salary' => $salary,
            'websiteRevenue' => $websiteRevenue,
            'otherRevenues' => $otherRevenues,
            'provisions' => $provisions,
            'expenses' => $expenses,
            'categories' => $categories,
            'expenseCategories' => $expenseCategories,
            'selectedCategoryIds' => $selectedCategoryIds,
            'expenseComparison' => $expenseComparison,
            'monthlyExpenseComparison' => $monthlyExpenseComparison,
            'historicalYears' => $historicalYears,
            'salaryMonthlyHistory' => $salaryMonthlyHistory,
            'salaryTimeline' => $salaryTimeline,
            'websiteByYearMonth' => $websiteByYearMonth,
            'transactionCount' => $transactions->count(),
        ]);
    }

    private function dashboardCategoryType(?Categorie $category): string
    {
        if (!$category) {
            return 'expense';
        }

        $name = mb_strtoupper(trim($category->nom), 'UTF-8');
        if ($name === 'PROVISION') {
            return 'provision';
        }
        if (in_array($name, ['SALAIRE', 'SALAIRES'], true)) {
            return 'salary';
        }
        if (in_array($name, ['WEBSITE', 'NOM DE DOMAINE', 'NOM DE DOMAINES'], true)) {
            return 'website';
        }
        if (mb_strtolower(trim((string) $category->group), 'UTF-8') === 'income') {
            return 'income';
        }

        return 'expense';
    }

    private function summarizeDashboardTransactions(
        \Illuminate\Support\Collection $transactions,
        \Illuminate\Support\Collection $categoryTypes,
        \Illuminate\Support\Collection $selectedCategoryIds
    ): array {
        $summary = [
            'revenues' => 0.0,
            'salary' => 0.0,
            'websiteRevenue' => 0.0,
            'otherRevenues' => 0.0,
            'provisions' => 0.0,
            'expenses' => 0.0,
            'categories' => collect(),
        ];
        $expenseTotals = [];

        foreach ($transactions as $transaction) {
            $categoryId = (int) $transaction->fk_id_categorie;
            $type = $categoryTypes->get($categoryId, 'expense');
            $amount = (float) $transaction->montant;

            if ($type === 'provision') {
                if ($transaction->retrait) {
                    $summary['provisions'] += $amount;
                }
                continue;
            }

            if (in_array($type, ['salary', 'website', 'income'], true)) {
                if (!$transaction->retrait) {
                    $summary['revenues'] += $amount;
                    if ($type === 'salary') {
                        $summary['salary'] += $amount;
                    } elseif ($type === 'website') {
                        $summary['websiteRevenue'] += $amount;
                    } else {
                        $summary['otherRevenues'] += $amount;
                    }
                }
                continue;
            }

            if (!in_array($categoryId, $selectedCategoryIds->all(), true)) {
                continue;
            }

            $key = $categoryId ?: 'uncategorized';
            if (!isset($expenseTotals[$key])) {
                $category = $transaction->category;
                $expenseTotals[$key] = [
                    'id' => $transaction->fk_id_categorie,
                    'name' => $category ? $category->nom : 'Sans catégorie',
                    'color' => $category ? $category->getColor() : '#adb5bd',
                    'spent' => 0.0,
                    'refunds' => 0.0,
                ];
            }

            if ($transaction->retrait) {
                $expenseTotals[$key]['spent'] += $amount;
            } else {
                $expenseTotals[$key]['refunds'] += $amount;
            }
        }

        $summary['categories'] = collect($expenseTotals)->map(function ($category) {
            $category['amount'] = $category['spent'] - $category['refunds'];
            return $category;
        })->sortByDesc('amount')->values();
        $summary['expenses'] = (float) $summary['categories']->sum('amount');

        return $summary;
    }

    private function home_manage_year_field($request){
      $records = Record::selectRaw(DB::raw('YEAR(date) as year'))
                    ->where('validate',1)
                    ->where('deleted',0)
                    ->distinct()->orderBy('year','asc')->get();
      $filter_year = $records->last()->year;
      $this->display_year['list_years'] = $records;
      if($request->year){
        $filter_year = $request->year;
      }
      $this->display_year['filter_year'] = $filter_year;
    }

    private function home_manage_month_field($request){
      $records = Record::selectRaw(DB::raw('MONTH(date) as month'))
                        ->where(DB::raw('YEAR(date)'),$this->display_year['filter_year'])
                        ->where('validate',1)
                        ->where('deleted',0)
                        ->distinct()->orderBy('month','asc')->get();
      $filter_month = $records->last()->month;
      $this->display_month['list_months'] = $records;
      if($request->month){
        $filter_month = $request->month;
      }
      $this->display_month['filter_month'] = $filter_month;
    }

    private function home_manage_transactions(){
      $this->transactions = Record::whereRaw(DB::raw('MONTH(date) = \''.$this->display_month['filter_month'].'\''))
                       ->whereRaw(DB::raw('YEAR(date) = \''.$this->display_year['filter_year'].'\''))
                       ->where('validate',1)
                       ->where('deleted',0)
                       ->join('categories','categories.id','=','records.fk_id_categorie')
                       ->orderBy('date')->get();

    }

    private function home_manage_chart_revenue()
    {
        $revenus  = Record::getSumPriceDepotRetrait($this->date_transaction, 0);
        $depenses = Record::getSumPriceDepotRetrait($this->date_transaction, 1);

        $this->revenu_depense_charts = [
            'labels' => [Display::monthText($this->display_month['filter_month'])],
            'datasets' => [
                [
                    'label' => 'Revenus',
                    'data' => [Display::amountForChart($revenus->total)],
                    'backgroundColor' => 'rgba(54,162,235,0.3)',
                ],
                [
                    'label' => 'Dépenses',
                    'data' => [Display::amountForChart($depenses->total)],
                    'backgroundColor' => 'rgba(255,99,132,0.2)',
                ],
            ],
        ];
    }

    private function home_manage_chart_revenue_by_categories()
    {
        $data = Record::getSumPriceDepotRetraitByCategorie($this->date_transaction, 0);

        $this->revenu_categories_charts = [
            'labels' => $data->pluck('nom')->toArray(),
            'data' => $data->map(fn ($c) => Display::amountForChart($c->total))->toArray(),
            'colors' => $data->map(
                fn ($c) => Categorie::getColorById($c->fk_id_categorie)
            )->toArray(),
        ];
    }

    private function home_manage_chart_depenses_by_categories()
    {
        $data = Record::getSumPriceDepotRetraitByCategorie($this->date_transaction, 1);

        $this->depenses_categories_charts = [
            'labels' => $data->pluck('nom')->toArray(),
            'data' => $data->map(fn ($c) => Display::amountForChart($c->total))->toArray(),
            'colors' => $data->map(
                fn ($c) => Categorie::getColorById($c->fk_id_categorie)
            )->toArray(),
        ];
    }

    public function import(Request $request){
      if ($request->isMethod('post')) {
        $validator = Validator::make($request->all(), [
            'original_filename' => 'required'
        ]);
        $msg = $this->validateFile($request->file('original_filename'));
        if(strlen($msg) > 0){
            $validator->errors()->add('upload',$msg);
            return back()->withErrors($validator);
        }
        $filePath = 'transactions.txt';
        $disk = Storage::disk('finance');
        $uploaded = $disk->put($filePath, file_get_contents($request->file('original_filename')), 'public');
        if(!$uploaded){
          Log::error("[FinanceController] - Transaction file upload failed");
          $validator->errors()->add('upload',"Issue to upload file");
          return back()->withErrors($validator);
        } else {
          Log::info("[FinanceController] - Transaction file upload succeed");
          // BACKUP ALL DB BEFORE
          Record::backup();
          // IMPORT DES DATAS
          $this->messages = Record::importFile($disk,$filePath);
          // MAINTENANT ON FAIT LES QUERIES POUR SOULAGER LE NOMBRE DE RECORDS
          $this->messages = array_merge($this->messages,Record::assignCategoryRecords());
          $disk->delete($filePath);
          // return records added
        }
        return view('backend.finance.import')->with('transactions',$this->messages);
      }else {
        return view('backend.finance.import');
      }
    }

    public function show(Request $request,$offset=0){
      if ($request->isMethod('post')) {
        $validator = Validator::make($request->all(), [
            'record_id' => 'required',
            'category_id' => 'required',
            'libelle' => 'required',
            'offset' => 'required'
        ]);
        $offset = $request->offset;
        if(!$validator->errors()->any()){
          $transaction =  Record::find($request->record_id);
          if($transaction) {
            $transaction->libelle = $request->libelle;
            $transaction->fk_id_categorie = $request->category_id;
            $transaction->validate = 1;
            $transaction->save();
          }else{
            $validator->errors()->add('finance','Transaction not found');
          }
        }
        if($validator->errors()->any())
          return $this->prepareView($offset)->withErrors($validator);
        else
          return $this->prepareView($offset)->with("success","Transaction correctly updated");
      } else {
        return $this->prepareView($offset);
      }
    }

    public function delete($transactionid){
        $transaction = Record::find($transactionid);
        $transaction->deleted = 1;
        $transaction->save();
        return back();
    }


    private function validateFile($file){
        $allowed = array('csv');
        if(!$file->isValid()){
            return "Error to upload file";
        }
        if(!in_array(strtolower($file->getClientOriginalExtension()), $allowed)){
            return "Invalid extension file. Only folowing extension are supported: (".implode(",",$allowed).").";
        }
        return "";
    }

    private function prepareView($offset){
      $all_category = Categorie::orderBy('nom')->get();
      $all_transactions = Record::where("validate",0)
                      ->where("deleted",0)
                      ->join('comptes', 'records.fk_id_compte', '=', 'comptes.compteid')
                      ->orderBy('date','asc')
                      ->get()
                      ->map(function($transaction){
                        $group = $this->getQuickGroup($transaction);
                        return [
                          'transaction' => $transaction,
                          'group' => $group,
                          'group_label' => $this->getQuickGroupLabel($group),
                          'suggested_label' => $this->buildSuggestedLabel($transaction),
                        ];
                      })
                      ->sortBy(function($row){
                        $priority = in_array($row['group'], ['paypal', 'amazon']) ? 0 : 1;
                        return [$priority, $row['transaction']->date];
                      })
                      ->values();
      $nb_transaction = $all_transactions->count();
      $batchSize = 4;
      $offset = max(0, (int) $offset);

      if($nb_transaction > 0 && $offset >= $nb_transaction){
        $offset = max(0, (int) floor(($nb_transaction - 1) / $batchSize) * $batchSize);
      }

      $display_transactions = $all_transactions->slice($offset, $batchSize)->values();

      $previous_offset = $offset > 0 ? max(0, $offset - $batchSize) : null;
      $next_offset = ($offset + $batchSize) < $nb_transaction ? $offset + $batchSize : null;

      return view('backend.finance.update')
                                   ->with('display_transactions', $display_transactions)
                                   ->with('previous_offset', $previous_offset)
                                   ->with('next_offset', $next_offset)
                                   ->with('nb_transaction', $nb_transaction)
                                   ->with('offset', $offset)
                                   ->with('all_category',$all_category);

    }

    private function getQuickGroup($transaction){
      $details = Str::lower($transaction->details . ' ' . $transaction->libelle);
      if(Str::contains($details, 'paypal')){
        return 'paypal';
      }
      if(Str::contains($details, 'amazon')){
        return 'amazon';
      }
      return 'other';
    }

    private function getQuickGroupLabel($group){
      return match($group){
        'paypal' => 'PayPal',
        'amazon' => 'Amazon',
        default => 'Autres',
      };
    }

    private function buildSuggestedLabel($transaction){
      $source = trim($transaction->libelle ?: $transaction->details ?: '');
      if($source === ''){
        return 'Transaction à catégoriser';
      }

      $source = preg_replace('/\s+/', ' ', $source);
      $source = trim($source);
      return Str::limit($source, 80);
    }

    public function all(Request $request){
      $all_category = Categorie::orderBy('nom')->get();

      $years = Record::where('validate', 1)
          ->where('deleted', 0)
          ->selectRaw('YEAR(date) as year')
          ->distinct()
          ->orderByDesc('year')
          ->pluck('year');

      if ($years->isEmpty()) {
          $years = collect([(int) date('Y')]);
      }

      $selectedYear = (int) ($request->input('year', $years->first() ?: date('Y')));
      if (!$years->contains($selectedYear)) {
          $selectedYear = (int) $years->first();
      }

      $months = Record::where('validate', 1)
          ->where('deleted', 0)
          ->whereYear('date', $selectedYear)
          ->selectRaw('MONTH(date) as month')
          ->distinct()
          ->orderBy('month')
          ->pluck('month');

      if ($months->isEmpty()) {
          $months = collect([(int) date('n')]);
      }

      $selectedMonth = $request->filled('month') ? (int) $request->input('month') : null;
      if ($selectedMonth !== null && !$months->contains($selectedMonth)) {
          $selectedMonth = null;
      }

      $selectedCategory = $request->input('category');
      $selectedSort = in_array($request->input('date_sort'), ['asc', 'desc'], true) ? $request->input('date_sort') : 'desc';
      $perPage = in_array((int) $request->input('per_page', 20), [5, 10, 20, 50, 100], true)
          ? (int) $request->input('per_page', 20)
          : 20;

      $query = Record::with(['category', 'compte'])
          ->where('validate', 1)
          ->where('deleted', 0)
          ->when($selectedYear, fn ($q) => $q->whereYear('date', $selectedYear))
          ->when($selectedMonth !== null, fn ($q) => $q->whereMonth('date', $selectedMonth))
          ->when($selectedCategory, fn ($q) => $q->where('fk_id_categorie', $selectedCategory));

      if ($request->filled('search')) {
          $search = trim($request->input('search'));
          $query->where(function ($q) use ($search) {
              $q->where('libelle', 'like', '%' . $search . '%')
                  ->orWhere('details', 'like', '%' . $search . '%')
                  ->orWhere('mouvement', 'like', '%' . $search . '%');
          });
      }

      $transactions = $query->orderBy('date', $selectedSort)->paginate($perPage)->appends($request->query());

      return view('backend.finance.all', compact(
          'all_category',
          'transactions',
          'years',
          'months',
          'selectedYear',
          'selectedMonth',
          'selectedCategory',
          'selectedSort',
          'perPage'
      ));
    }

    public function updateListTransaction(Request $request){
      $validated = $request->validate([
          'id' => 'required|exists:records,id',
          'libelle' => 'nullable|string|max:255',
          'category_id' => 'nullable|exists:categories,id',
      ]);

      $transaction = Record::findOrFail($request->input('id'));
      $transaction->libelle = trim((string) ($validated['libelle'] ?? $transaction->libelle));
      if ($request->has('category_id') && $request->input('category_id') !== null && $request->input('category_id') !== '') {
          $transaction->fk_id_categorie = (int) $request->input('category_id');
      }
      $transaction->save();
      $transaction->load('category');

      return response()->json([
          'success' => true,
          'libelle' => $transaction->libelle,
          'category_name' => $transaction->category ? $transaction->category->nom : null,
          'category_group' => $transaction->category && $transaction->category->group ? $transaction->category->group : null,
          'category_color' => $transaction->category ? Categorie::getColorById($transaction->category->id) : null,
      ]);
    }

    public function search(Request $request){
      $output = "";

      if ($request->ajax()) {
          $item_per_page = in_array((int) $request->input('per_page', 20), [10, 20, 50, 100], true)
              ? (int) $request->input('per_page', 20)
              : 20;
          $page_number = max(1, (int) $request->input('page', 1));
          $position = (($page_number - 1) * $item_per_page);
          $selectedSort = in_array($request->input('date_sort'), ['asc', 'desc'], true) ? $request->input('date_sort') : 'desc';

          $records = Record::where('validate', 1)
              ->where('deleted', 0)
              ->with(['category', 'compte'])
              ->when($request->category, fn ($q) => $q->where('fk_id_categorie', $request->category))
              ->when($request->year, fn ($q) => $q->whereYear('date', $request->year))
              ->when($request->month, fn ($q) => $q->whereMonth('date', $request->month))
              ->when($request->search, function ($q) use ($request) {
                  $q->where(function ($query) use ($request) {
                      $query->where('details', 'LIKE', '%' . $request->search . '%')
                          ->orWhere('libelle', 'LIKE', '%' . $request->search . '%');
                  });
              })
              ->orderBy('date', $selectedSort)
              ->limit($item_per_page)
              ->offset($position);

          $result = $records->get();
          if ($result) {
              foreach ($result as $transaction) {
                  $color = $transaction->retrait ? "text-danger" : "text-success";
                  $accountName = $transaction->compte ? $transaction->compte->nom : 'Compte non renseigné';
                  $output .=
                      '<tr data-record-id="' . $transaction->id . '">' .
                      '<td class="td_date">' .
                      '<div class="all_transaction_date">' .
                      '<span class="all_transaction_date_day">' . Display::day($transaction->date) . '</span>' .
                      '<span class="all_transaction_date_month">' . Display::monthTextPrefixe(date("n", strtotime($transaction->date))) . '</span>' .
                      '<span class="all_transaction_date_year">' . Display::year($transaction->date) . '</span>' .
                      '</div>' .
                      '</td>' .
                      '<td><button type="button" class="finance-inline-edit" data-field="libelle" data-record-id="' . $transaction->id . '" data-value="' . e($transaction->libelle) . '">' . $transaction->libelle . '</button></td>' .
                      '<td><button type="button" class="finance-inline-edit finance-category-inline-edit" data-field="category" data-record-id="' . $transaction->id . '" data-category-id="' . $transaction->fk_id_categorie . '" data-category-name="' . e($transaction->category ? $transaction->category->nom : '') . '">' .
                      ($transaction->category ? '<span class="finance-category-badge" style="background-color:' . Categorie::getColorById($transaction->fk_id_categorie) . ';">' . $transaction->category->nom . '</span>' : '<span class="finance-category-badge" style="background-color:#999;">Sans catégorie</span>') .
                      '</button></td>' .
                      '<td><span class="finance-group-badge" data-group-cell>' . ($transaction->category && $transaction->category->group ? $transaction->category->group : '—') . '</span></td>' .
                      '<td class="text-right"><span class="amount ' . $color . ' finance-amount-tooltip" data-tooltip="Compte : ' . e($accountName) . '" aria-label="Compte : ' . e($accountName) . '">' . Display::transactionAmount($transaction) . '</span></td>' .
                      '</tr>';
              }
          }
          return response($output);
      }

      return response('');
    }
}