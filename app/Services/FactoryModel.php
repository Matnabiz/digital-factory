<?php
namespace App\Services;
use App\Models\DesignSession;
use App\Models\Stage;
class FactoryModel {
 public function build(DesignSession $session):array {
  $stages=Stage::orderBy('number')->get();$decisions=$session->decisions()->get()->keyBy('stage_id');
  $metrics=['agility'=>50,'complexity'=>50,'cost'=>50,'openness'=>50,'external_dependency'=>50,'launch_speed'=>50,'risk'=>50];
  $nodes=[['id'=>'factory','label'=>'Digital Factory','type'=>'factory'],['id'=>'nikan','label'=>'Nikan Sepehr','type'=>'organization'],['id'=>'soroush','label'=>'Soroush Sepehr','type'=>'organization']];
  $links=[['source'=>'factory','target'=>'nikan'],['source'=>'factory','target'=>'soroush']];$results=[];
  foreach($stages as $stage){if(!$stage->requires_decision)continue;$d=$decisions->get($stage->id);if(!$d)continue;
   $option=collect($stage->options??[])->firstWhere('code',$d->scenario_code);
   $results[]=['stage_number'=>$stage->number,'title'=>$stage->title,'code'=>$d->scenario_code,'label'=>$option['title']??'Custom CEO proposal','custom_text'=>$d->custom_text,'type'=>$d->type];
   // Deliberately illustrative rules, NOT architecturally validated KPIs.
   $code=$d->scenario_code;
   if(str_contains($code,'HYBRID')||str_contains($code,'NETWORK')||str_contains($code,'MULTI')||str_contains($code,'EXTERNAL')){$metrics['openness']+=5;$metrics['external_dependency']+=5;$metrics['agility']+=3;$metrics['complexity']+=2;}
   if(str_contains($code,'INTERNAL')||str_contains($code,'DIRECT')){$metrics['cost']+=4;$metrics['external_dependency']-=4;$metrics['openness']-=3;}
   if($d->type==='CUSTOM'){$metrics['risk']+=2;}
  }
  $selected=collect($results)->pluck('code');
  if($selected->contains(fn($x)=>str_contains($x,'HYBRID')||str_contains($x,'NETWORK')||str_contains($x,'PARTNER'))){$nodes[]=['id'=>'partners','label'=>'External partners','type'=>'partner'];$links[]=['source'=>'factory','target'=>'partners'];}
  foreach(['academy'=>'Academy','observatory'=>'Technology Observatory','studio'=>'Startup Studio','accelerator'=>'Accelerator','lab'=>'PoC Lab'] as $id=>$label){$nodes[]=['id'=>$id,'label'=>$label,'type'=>'unit'];$links[]=['source'=>'factory','target'=>$id];}
  return ['session'=>$session,'decisions'=>$results,'metrics'=>array_map(fn($v)=>max(0,min(100,$v)),$metrics),'nodes'=>$nodes,'links'=>$links,'completed'=>count($results),'total'=>$stages->where('requires_decision',true)->count()];
 }
}
