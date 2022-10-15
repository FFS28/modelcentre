<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BPController;
use Illuminate\Support\Facades\DB;

class bpconnect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BPConnect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $productIDs = [];
        $bpIDs = [];
        $bpadapter = new BPController();
        $bpadapter->init();
        $bpadapter->authenticate();

        $products = DB::table('products')->select('id', 'sku')->get();
        echo "Getting product IDs...\n";

        foreach($products as $product){

            $bpId = $bpadapter->getProductIDFromSKU($product->sku);
            if(count($bpId) != 0){
                array_push($productIDs, ["realId"=> $product->id, "bpId"=> $bpId[0][0]]);
                array_push($bpIDs, $bpId[0][0]);
            }
            else {
                echo "Please check product sku in your db! Not match brightpearl db";
                return Command::FAILURE;
            }
        }

        echo "Success.\nUpdating stocks...";
        sort($bpIDs);
        $productStocks = $bpadapter->getProductStock($bpIDs);
        foreach($productIDs as $productID) {
            $stock = $productStocks[$productID['bpId']]['total']['inStock'];
            DB::table('product_inventories')
                ->where('product_id', $productID['realId'])
                ->update(['qty' => $stock]);
        }
        echo "\nSuccess uploaded!";
        return Command::SUCCESS;
    }
}
