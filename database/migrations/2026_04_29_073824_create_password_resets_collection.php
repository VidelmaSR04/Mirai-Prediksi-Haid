<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('mongodb')->create('password_resets', function ($collection) {
            $collection->index('email');
            $collection->index('token');
            $collection->index('created_at');
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->drop('password_resets');
    }
};