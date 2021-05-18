<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\FertiliserInventoryApplication;
use App\Models\FertiliserInventoryPurchase;

class InventorySeeder extends Seeder
{
    use CSVSeeder;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the test sample file and use a Trait to parse it to an Array
        $testInventoryFile = dirname(__FILE__) . '/../data/Fertiliser inventory movements - Sheet1.csv';
        $testInventoryData = $this->csv_to_array($testInventoryFile);

        // Just since this is a test gig, purge the table. We can run migrate:fresh --seed too
        //DB::table('fertiliser_inventory_purchases')->truncate();
        //DB::table('fertiliser_inventory_applications')->truncate();

        // Loop the data and split it up into purchases and applications
        foreach($testInventoryData as $testInventoryRecord) {

            $actionDate = \DateTime::createFromFormat('d/m/Y', $testInventoryRecord['Date']);

            if($testInventoryRecord['Type'] === 'Purchase') {
                // Populate purchase table
                DB::table('fertiliser_inventory_purchases')->insert([
                    'date' => $actionDate->format('Y-m-d H:i:s'),
                    'quantity_purchased' => $testInventoryRecord['Quantity'],
                    'quantity_remaining' => $testInventoryRecord['Quantity'],
                    'unit_price' => $testInventoryRecord['Unit Price'],
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
            } elseif ($testInventoryRecord['Type'] === 'Application') {
                // Populate application table
                DB::table('fertiliser_inventory_applications')->insert([
                    'date' => $actionDate->format('Y-m-d H:i:s'),
                    'quantity' => abs($testInventoryRecord['Quantity']),
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
            } else {
                // Data isn't as expected
                continue;
            }
        }

        // Prime the data using the FIFO logic for working out inventory levels remaining for each purchase record
        // This normally wouldn't be in a seeder, but this is all prepping for the test interface being the correct state
        foreach (FertiliserInventoryApplication::all()->sortBy('date') as $fertiliserApplication) {
            $applicationQuantity = $fertiliserApplication['quantity'];

            // Allocate purchases to this Application
            $purchasedObj = new FertiliserInventoryPurchase();
            $valueOfApplication = $purchasedObj->allocateProduct($applicationQuantity);

            // If allocation went well, notify in CLI
            if($valueOfApplication['status'] == FertiliserInventoryPurchase::TYPE_OK) {
                $value = number_format($valueOfApplication['cost'], 2);
                $this->command->getOutput()->writeln("<info>Purchases Allocated for {$applicationQuantity} units with a cost of \${$value}</info>");
            } else {
                $this->command->getOutput()->writeln("<error>Allocation failed for {$applicationQuantity} units</error>");
            }

        }
    }

}
