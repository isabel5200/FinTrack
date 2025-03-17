<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
     public function run(): void
    {
        Transaction::factory(20)->create();
    }
}
