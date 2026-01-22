<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Record;
use App\Models\Categorie;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Debug\Exception\FatalErrorException;
use App\Models\Display;

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

        return view('finance.home')->with('year',$this->display_year)
                                    ->with('month',$this->display_month)
                                    ->with('revenu_depense_charts',$this->revenu_depense_charts)
                                    ->with('revenu_categories_charts',$this->revenu_categories_charts)
                                    ->with('depenses_categories_charts',$this->depenses_categories_charts)
                                    ->with('transactions',$this->transactions);
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
        return view('finance.import')->with('transactions',$this->messages);
      }else {
        return view('finance.import');
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
                      ->orderBy('date','asc');
      $nb_transaction = $all_transactions->count();
      $transaction = $all_transactions->offset($offset)
                            ->limit(1)->first();

      if($offset == 0){
        $previous = null;
      } else {
        $previous = $offset-1;
      }
      if($offset >= $nb_transaction-1){
        $next = null;
      } else {
        $next = $offset+1;
      }

      return view('finance.update')->with('current_transaction', $transaction)
                                   ->with('previous_transaction', $previous)
                                   ->with('next_transaction', $next)
                                   ->with('nb_transaction', $nb_transaction)
                                   ->with('offset', $offset)
                                   ->with('all_category',$all_category);

    }

    public function all(Request $request){
      $all_category = Categorie::orderBy('nom')->get();
      $cat = $all_category->first();
      $filter_category = $cat->nom;
      $filter_categoryid = $cat->id;
      if($request->category){
        $cat = Categorie::find($request->category);
        $filter_category = $cat->nom;
        $filter_categoryid = $cat->id;
      }
      return view('finance.all')->with('all_category',$all_category)->with('filter_category',$filter_category)->with('filter_categoryid',$filter_categoryid);
    }

    public function search(Request $request){
      $output="";

      if($request->ajax())
      {
          $item_per_page = 20;
          $page_number = $request->page;
          $position = (($page_number-1) * $item_per_page);
          $records=Record::where('validate',1)
                           ->where('deleted',0)
                           ->join('categories','categories.id','=','records.fk_id_categorie')
                           ->orderBy('date','desc')->limit($item_per_page)->offset($position);
          if($request->search){
            $records->where('details','LIKE','%'.$request->search."%")
                     ->orwhere('libelle','LIKE','%'.$request->search."%");
          }
          if($request->category){
            $records->where('categories.id',$request->category);
          }
          $result = $records->get();
          if($result)
          {
            foreach ($result as $transaction) {
              $color =  $transaction->retrait ? "": "orange";
              $output.=
              '<tr>'.
                '<td class="td_date">'.
                  '<div class="all_transaction_date">'.
                    '<span class="all_transaction_date_day">'.Display::day($transaction->date).'</span>'.
                    '<span class="all_transaction_date_month">'.Display::monthTextPrefixe(date("n",strtotime($transaction->date))).'</span>'.
                    '<span class="all_transaction_date_year">'.Display::year($transaction->date).'</span>'.
                    '</div>'.
                '</td>'.
                '<td>'.$transaction->libelle.'</td>'.
                '<td>'.
                  '<span class="badge badge-info" style="background-color:'.Categorie::getColorById($transaction->fk_id_categorie).';color:white;font-size:14px;margin-top:3px;">'.$transaction->nom.'</span>'.
                '</td>'.
                '<td>'.
                  '<div class="col-2 text-right align-self-center amount '.$color .'">'.
                    Display::transactionAmount($transaction).
                  '</div>'.
                '</td>'.
             '</tr>';
            }
          }
      }
      return Response($output);
    }
}
