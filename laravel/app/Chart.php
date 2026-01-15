<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    var $display;

    var $name;
    var $height;
    var $width;
    var $labels;
    var $nbdatasets;
    var $datas;

    public function __construct($name,$height,$width){
      $this->name = $name;
      $this->height = $height;
      $this->width = $width;
    }

    public function setlabelsDatasets($datas){
      $this->labels = array_keys($datas);
      $this->nbdatasets = count(array_values($datas)[0]);
      $this->datas = array_values($datas);
    }

    public function bar($label_name,$bkgcolors){
      $datasets = array();
      for($i=0;$i<$this->nbdatasets;$i++){
        $dataset["label"] = $label_name[$i];
        $dataset["backgroundColor"] = $bkgcolors[$i];
        $dataset["data"] = array_column($this->datas,$i);
        array_push($datasets,$dataset);
      }

      $this->display = app()->chartjs
         ->name($this->name)
         ->type('bar')
         ->size(['width' => $this->width, 'height' => $this->height])
         ->labels($this->labels)
         ->datasets($datasets)
         ->options([]);

       $this->display->optionsRaw("{
                     legend: {
                         display:false
                         },scales: {
                           yAxes: [{
                               ticks: {
                                   beginAtZero:true
                               }
                           }]
                       }
                     }");
    }

    public function pie($labels,$bkgcolors,$hovercolors,$datas){
      $datasets = array();
      $dataset = array();
      $dataset["hoverBackgroundColor"] = $hovercolors;
      $dataset["backgroundColor"] = $bkgcolors;
      $dataset["data"] = $datas;
      array_push($datasets,$dataset);

      $this->display = app()->chartjs
        ->name($this->name)
        ->type('pie')
        ->size(['width' => $this->width, 'height' => $this->height])
        ->labels($labels)
        ->datasets($datasets)
        ->options([]);
      $this->display->optionsRaw("{
                      legend: {
                          display:false
                          }
                      }");
    }

    public function line($label_name,$color,$bkgcolor){
      $datasets = array();
      for($i=0;$i<$this->nbdatasets;$i++){
        $dataset["label"] = $label_name[$i];
        $dataset["backgroundColor"] = $bkgcolor;
        $dataset["borderColor"] = $color;
        $dataset["pointBorderColor"] = $color;
        $dataset["pointBackgroundColor"] = $color;
        $dataset["pointHoverBackgroundColor"] = $color;
        $dataset["pointHoverBorderColor"] = $color;
        $dataset["data"] = array_column($this->datas,$i);
        array_push($datasets,$dataset);
      }
      $this->display = app()->chartjs
          ->name($this->name)
          ->type('line')
          ->size(['width' => $this->width, 'height' => $this->height])
          ->labels($this->labels)
          ->datasets($datasets)
          ->options([]);
          $this->display->optionsRaw("{
                        legend: {
                            display:false
                            },scales: {
                              yAxes: [{
                                  ticks: {
                                      beginAtZero:true
                                  }
                              }]
                          }
                        }");
    }
}
