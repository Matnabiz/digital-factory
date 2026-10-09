<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void {
 Schema::create('stages',function(Blueprint $t){$t->id();$t->unsignedTinyInteger('number')->unique();$t->string('title');$t->longText('description')->nullable();$t->boolean('requires_decision')->default(false);$t->json('options')->nullable();$t->string('recommended_code')->nullable();$t->text('recommendation')->nullable();$t->timestamps();});
 Schema::create('design_sessions',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->string('name');$t->timestamps();});
 Schema::create('decisions',function(Blueprint $t){$t->id();$t->foreignId('design_session_id')->constrained()->cascadeOnDelete();$t->foreignId('stage_id')->constrained()->cascadeOnDelete();$t->enum('type',['ACCEPTED','ALTERNATIVE','CUSTOM']);$t->string('scenario_code');$t->longText('custom_text')->nullable();$t->timestamps();$t->unique(['design_session_id','stage_id']);});
 } public function down():void {Schema::dropIfExists('decisions');Schema::dropIfExists('design_sessions');Schema::dropIfExists('stages');} };
