<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Decision extends Model { protected $guarded=[]; public function stage(){return $this->belongsTo(Stage::class);} }
