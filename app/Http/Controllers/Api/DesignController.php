<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Stage;
use App\Models\Decision;
use App\Models\DesignSession;
use App\Services\FactoryModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
class DesignController extends Controller {
 private function owned(Request $r,DesignSession $session):void {abort_unless($session->user_id===$r->user()->id,403);}
 public function stages(){return Stage::orderBy('number')->get();}
 public function index(Request $r){return DesignSession::where('user_id',$r->user()->id)->latest()->get();}
 public function store(Request $r){$v=$r->validate(['name'=>'required|string|max:150']);return response()->json(DesignSession::create(['user_id'=>$r->user()->id,'name'=>$v['name']]),201);}
 public function show(Request $r,DesignSession $session,FactoryModel $service){$this->owned($r,$session);return response()->json(['session'=>$session,'decisions'=>$session->decisions,'model'=>$service->build($session)]);}
 public function decide(Request $r,DesignSession $session){$this->owned($r,$session);$v=$r->validate(['stage_id'=>'required|integer|exists:stages,id','type'=>'required|in:ACCEPTED,ALTERNATIVE,CUSTOM','scenario_code'=>'required|string|max:100','custom_text'=>'nullable|string|max:10000']);$stage=Stage::findOrFail($v['stage_id']);if(!$stage->requires_decision)throw ValidationException::withMessages(['stage_id'=>'This stage is informational.']);
  $codes=collect($stage->options??[])->pluck('code');$code=$v['scenario_code'];
  if($v['type']==='ACCEPTED'&&($code!==$stage->recommended_code||!$stage->recommended_code))throw ValidationException::withMessages(['scenario_code'=>'Must match consultant recommendation.']);
  if($v['type']==='ALTERNATIVE'&&(!$codes->contains($code)||$code===$stage->recommended_code||$code==='CUSTOM_INPUT'))throw ValidationException::withMessages(['scenario_code'=>'Invalid alternative.']);
  if($v['type']==='CUSTOM'&&($code!=='CUSTOM_INPUT'||blank($v['custom_text']??null)))throw ValidationException::withMessages(['custom_text'=>'A custom proposal is required.']);
  $d=Decision::updateOrCreate(['design_session_id'=>$session->id,'stage_id'=>$stage->id],['type'=>$v['type'],'scenario_code'=>$code,'custom_text'=>$v['type']==='CUSTOM'?$v['custom_text']:null]);return response()->json($d);
 }
 public function model(Request $r,DesignSession $session,FactoryModel $service){$this->owned($r,$session);return $service->build($session);}
 public function report(Request $r,DesignSession $session,FactoryModel $service){$this->owned($r,$session);$model=$service->build($session);abort_unless($model['completed']===$model['total'],422,'Complete all decision stages first.');return Pdf::loadView('factory-report',['model'=>$model])->setPaper('a4')->download('factory-design-'.$session->id.'.pdf');}
}
