<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DesignSession extends Model { protected $guarded=[]; public function decisions(){return $this->hasMany(Decision::class);} }
