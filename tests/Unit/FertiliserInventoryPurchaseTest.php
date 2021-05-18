<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\FertiliserInventoryPurchase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FertiliserInventoryPurchaseTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $purchase = new FertiliserInventoryPurchase([
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
            'quantity_purchased' => 10,
            'quantity_remaining' => 10,
            'unit_price' => 5
        ]);
        $purchase->save();

        $purchase2 = new FertiliserInventoryPurchase([
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
            'quantity_purchased' => 20,
            'quantity_remaining' => 20,
            'unit_price' => 4.7
        ]);
        $purchase2->save();

        $purchase3 = new FertiliserInventoryPurchase([
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
            'quantity_purchased' => 37,
            'quantity_remaining' => 37,
            'unit_price' => 3.99
        ]);
        $purchase3->save();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_application()
    {

        // Just an object to use to access non-static methods
        $purchasedObj = new FertiliserInventoryPurchase();

        // Request Zero. We expect a value of zero
        // This could return an error depending on the design of the application
        $requestedApplication1 = 0;
        $application1Result = $purchasedObj->allocateProduct($requestedApplication1);
        $this->assertEquals('SUCCESS', $application1Result['status']);
        $this->assertEquals(0, $application1Result['cost']);

        // Request 10. We expect all of this to be filled by the first purchase
        // Expected cost of $50 (10*5)
        $requestedApplication1 = 10;
        $application1Result = $purchasedObj->allocateProduct($requestedApplication1);
        $this->assertEquals('SUCCESS', $application1Result['status']);
        $this->assertEquals(50, $application1Result['cost']);

        // Request 25. We expect 20 of this to be filled by the second purchase and 5 to be filled by the third purchase
        // Expected cost of $113.95 (20*4.70 + 5*3.99)
        $requestedApplication1 = 25;
        $application1Result = $purchasedObj->allocateProduct($requestedApplication1);
        $this->assertEquals('SUCCESS', $application1Result['status']);
        $this->assertEquals(113.95, $application1Result['cost']);

        // Request 31. We expect all of this to be filled by the third purchase
        // Expected cost of $123.69 (31*3.99)
        $requestedApplication1 = 31;
        $application1Result = $purchasedObj->allocateProduct($requestedApplication1);
        $this->assertEquals('SUCCESS', $application1Result['status']);
        $this->assertEquals(123.69, $application1Result['cost']);

        // Request 2. There should be only one unit left at this point so we should get an error
        $requestedApplication1 = 2;
        $application1Result = $purchasedObj->allocateProduct($requestedApplication1);
        $this->assertEquals('ERROR', $application1Result['status']);
        $this->assertEquals(0, $application1Result['cost']);

        // Request 1. This should use up the last unit
        // Expected cost of $3.99 (1*3.99)
        $requestedApplication1 = 1;
        $application1Result = $purchasedObj->allocateProduct($requestedApplication1);
        $this->assertEquals('SUCCESS', $application1Result['status']);
        $this->assertEquals(3.99, $application1Result['cost']);

        // Out of stock test. We should get an error for any request
        $requestedApplication1 = 1;
        $application1Result = $purchasedObj->allocateProduct($requestedApplication1);
        $this->assertEquals('ERROR', $application1Result['status']);
        $this->assertEquals(0, $application1Result['cost']);

    }
}
