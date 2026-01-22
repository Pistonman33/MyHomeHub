<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record;
use App\Models\Display;
use App\Models\Chart;
use App\Models\Categorie;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
  private $chart1;
  private $chart2;
  private $chart_title1="";
  private $chart_title2="";
  private $display_year = array();
  private $display_period = "year";


  public function home(){
    $all_category = Categorie::orderBy('nom')->get();
    return view('stats.home')->with('all_category',$all_category);
  }

  public function show(Request $request){
    if($request->id == 22 ){
        $this->display_period = "month";
    }
    $this->manage_filters($request);
    switch ($request->id) {
      case 44:
      case 38:
        $cats = array(38,44);
        $this->chart1 = $this->manage_bar_depot_retrait_chart(1,$cats,'year');
        $this->chart_title1 = "Mutuelle / Soins par an";
        $this->chart2 = $this->manage_bar_depot_retrait_chart(2,$cats,'month');
        $this->chart_title2 = "Mutuelle / Soins cette année";
        break;
      default:
        $name = Categorie::find($request->id)->nom;
        $this->chart_title1 = "Moyenne de la catégorie ".$name." / an";
        $this->chart1 = $this->manage_line_by_years(1,$request->id,$name);
        $this->chart2 = $this->manage_bar_depot_retrait_chart(1,array($request->id),'month');
        $this->chart_title2 = "Moyenne de la catégorie ".$name." année:".$this->display_year['filter_year'];
        break;
    }
    return view('stats.show')->with('chart_1',$this->chart1)
                             ->with('chart_2',$this->chart2)
                             ->with('chart_title1',$this->chart_title1)
                             ->with('chart_title2',$this->chart_title2)
                             // FILTERS
                             ->with('period',$this->display_period)
                             ->with('year',$this->display_year);

  }

  private function manage_filters(Request $request){
    $this->home_manage_year_field($request);
    $this->home_manage_radio_field($request);
  }

  private function home_manage_radio_field($request){
    if($request->byperiod){
      $this->display_period = $request->byperiod;
    }
  }

  private function home_manage_year_field($request){
    $records = Record::selectRaw(DB::raw('YEAR(date) as year'))
                  ->where('validate',1)
                  ->where('deleted',0)
                  ->where('fk_id_categorie',$request->id)
                  ->distinct()->orderBy('year','asc')->get();
    $filter_year = $records->last()->year;
    $this->display_year['list_years'] = $records;
    if($request->year){
      $filter_year = $request->year;
    }
    $this->display_year['filter_year'] = $filter_year;
  }


  private function manage_line_by_years($id,$category_id,$name){
    $cats=array($category_id);
    $depots = Record::getSumPriceDepotRetraitByYear(0,$cats);
    $retraits = Record::getSumPriceDepotRetraitByYear(1,$cats);
    if($category_id == 22){
        $depots->shift();
    }
    $more_retrait = (sizeof($retraits) > sizeof($depots));
    $amounts= array();
    foreach ($depots as $depot) {
      $amounts[$depot->dd] = array($depot->total);
    }
    foreach ($retraits as $retrait) {
      if(array_key_exists($retrait->dd, $amounts)){
        $amounts[$retrait->dd][0] = $amounts[$retrait->dd][0] - $retrait->total;
      }else{
        $amounts[$retrait->dd] = array($retrait->total * -1);
      }
    }
    ksort($amounts);
    if($more_retrait){
      $amounts = array_map(function($total){
        return array($total[0] * -1);
      },$amounts);
    }
    if($this->display_period == "month"){
      $amounts = array_map(function($total){
        $avg = $total[0] / 12;
        return array(Display::amountForChart($avg));
      },$amounts);
    } else {
      $amounts = array_map(function($total){
        return array(Display::amountForChart($total[0]));
      },$amounts);
    }
    $chart = new Chart('ChartLineYear_'.$id, 400, 700);
    $legends = [$name];
    $chart->setLabelsAndDatasets($amounts);
    $color = Categorie::getColorById($category_id);
    $bkgcolor = Categorie::getColorRGBAWithOpacity($category_id, 0.3);

    $chart->line($legends, [$color], [$bkgcolor]);
    return $chart->toJson();
  }

  private function manage_bar_depot_retrait_chart($id,$cats,$state){
    if($state=='month'){
      $year_specify = $this->display_year['filter_year'];
      if($year_specify == date('Y')){
        $date = strtotime(Record::getLastTransactionDateEncoded(). " - 365 day");
        $start_date = date('Y-m-d',$date);
        $end_date = date('Y-m-d');
      }else{
        $start_date = $year_specify."-01-01";
        $end_date = $year_specify+1 ."-01-01";
      }
      $depots = Record::getSumPriceDepotRetraitByMonth($start_date,$end_date,0,$cats);
      $retraits = Record::getSumPriceDepotRetraitByMonth($start_date,$end_date,1,$cats);
    } else {
      $depots = Record::getSumPriceDepotRetraitByYear(0,$cats);
      $retraits = Record::getSumPriceDepotRetraitByYear(1,$cats);
    }
    $name = 'BarChartByMonth_'.$id;

    $amounts= array();
    foreach ($depots as $depot) {
      $transaction = array();
      $transaction[0] = Display::amountForChart($depot->total);
      $transaction[1] = 0;
      $amounts[$depot->dd] = $transaction;
    }
    foreach ($retraits as $retrait) {
      if(array_key_exists($retrait->dd, $amounts)){
          $amounts[$retrait->dd][1] = Display::amountForChart($retrait->total);
      }else{
        $transaction = array();
        $transaction[0] = 0;
        $transaction[1] = Display::amountForChart($retrait->total);
        $amounts[$retrait->dd] = $transaction;
      }
    }
    ksort($amounts);
    $bkgcolors = array();
    $bkgcolors[] = array_fill(0,count(array_values($amounts)),'rgba(54, 162, 235, 0.3)');
    $bkgcolors[] = array_fill(0,count(array_values($amounts)),'rgba(255, 99, 132, 0.2)');
    $legends = array("Remboursement","Payé");

    $chart = new Chart($name, 400, 700);
    $chart->setLabelsAndDatasets($amounts);
    $chart->bar($legends, $bkgcolors);
    return $chart->toJson();
  }

}
