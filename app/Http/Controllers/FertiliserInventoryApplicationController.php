<?php

namespace App\Http\Controllers;

use App\Models\FertiliserInventoryApplication;
use App\Models\FertiliserInventoryPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * This is the main controller for the Inventory Application form page
 *
 * Class FertiliserInventoryApplicationController
 * @package App\Http\Controllers
 */

class FertiliserInventoryApplicationController extends Controller
{

    /**
     * Just the landing page for the form
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('add-fertiliser-application-form');
    }

    /**
     * Called by the form to do the business logic of an application and return the user back
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function apply(Request $request)
    {

        // Make sure the requested data is a real number
        $requestedQuantity = (int)$request->applicationQty;

        // No units requested? Y tho?
        if($requestedQuantity === 0) {
            $message = "ERROR! You need to request at least one unit!";
        } else {

            $applicationObj = new FertiliserInventoryApplication();
            $purchaseObj = new FertiliserInventoryPurchase();

            // Attempt to apply requested product from stock
            $applicationResult = $purchaseObj->allocateProduct($request->applicationQty);

            // Just a picky language thing
            $unitString = $request->applicationQty === 1 ? 'unit' : 'units';

            if ($applicationResult['status'] === FertiliserInventoryPurchase::TYPE_OK) {
                // Allocation succeeded. Save the application and report back to the user
                $applicationObj->date = Carbon::now()->format('Y-m-d H:i:s');
                $applicationObj->quantity = $request->applicationQty;
                $applicationObj->save();
                $message = "SUCCESS! " . $request->applicationQty . " $unitString applied at a total cost of $" . number_format($applicationResult['cost'], 2);
                $status = 'status';
            } else {
                // Allocation failed. Don't save anything and report back to the user
                $message = "ERROR! " . $request->applicationQty . " $unitString exceeds available stock!";
                $status = 'status-bad';
            }
        }

        return redirect('add-fertiliser-application-form')->with($status, $message);
    }

}
