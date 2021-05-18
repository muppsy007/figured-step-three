<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * This model handles all the data for the Inventory Purchases. It is also where the primary logic for how
 * product applications are consumed resides.
 *
 * Class FertiliserInventoryPurchase
 * @package App\Models
 */

class FertiliserInventoryPurchase extends Model
{
    use HasFactory;

    const TYPE_ERROR = 'ERROR';
    const TYPE_OK = 'SUCCESS';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'quantity_purchased',
        'quantity_remaining',
        'unit_price'
    ];

    /**
     * Primary method to work out the allocation of purchased product for a given application qty
     *
     * @param int $applicationQuantity
     * @return array
     */
    public function allocateProduct(int $applicationQuantity) : array {

        // Default response
        $allocationResult = [
            'status' => self::TYPE_ERROR,
            'cost' => 0
        ];

        // Get the next purchase that has allocatable inventory (ordered by date purchased)
        $purchaseToUseNext = FertiliserInventoryPurchase::where('quantity_remaining', '>', 0)->orderBy('date')->get()->first();

        if(!$purchaseToUseNext) {
            // Whoops, there's nothing left in stock. Outta here with your shenanigans
            // But better alert someone with money about here
        } else {
            if ($applicationQuantity > $purchaseToUseNext->quantity_remaining) {
                /** This purchase does not have enough on had to fulfill the application on it's own **/

                //var_dump(' using ' . $purchaseToUseNext->quantity_remaining . ' units from ' . $purchaseToUseNext->date);

                // The cost will be whatever inventory is left for this purchase, times its unit price
                $allocationResult['cost'] = (float)$purchaseToUseNext->quantity_remaining * $purchaseToUseNext->unit_price;

                // Work out how much more product we need to find in another purchase(s)
                $remainingToAllocate = $applicationQuantity - $purchaseToUseNext->quantity_remaining;

                // If all went well, set the quantity remaining for this purchase to zero and save
                $originalQtyRemaining = $purchaseToUseNext->quantity_remaining;
                $purchaseToUseNext->quantity_remaining = 0;
                $purchaseToUseNext->save();

                // Recurse to the next purchase and increment the total
                $nextPurchase = $this->allocateProduct($remainingToAllocate);
                if (isset($nextPurchase['status']) && $nextPurchase['status'] == self::TYPE_OK) {
                    $allocationResult['status'] = self::TYPE_OK;
                    $allocationResult['cost'] += (float)$nextPurchase['cost'];
                } else {
                    $allocationResult['status'] = self::TYPE_ERROR;
                    $allocationResult['cost'] = 0;
                }

                // This needs to be done last as recursive calls above might have run out of stock, so we don't want to
                // zero-out unless the entire application is filled.
                if ($allocationResult['status'] == self::TYPE_ERROR) {
                    $purchaseToUseNext->quantity_remaining = $originalQtyRemaining;
                    $purchaseToUseNext->save();
                }
            } else {
                /** This purchase has enough on had to fulfill the whole application! **/

                //var_dump(' using ' . $applicationQuantity . ' units from ' . $purchaseToUseNext->date);

                // Cost is simply full application qty times its unit price
                $allocationResult['status'] = self::TYPE_OK;
                $allocationResult['cost'] += (float)($applicationQuantity * $purchaseToUseNext->unit_price);

                // Set the remaining quantity to what it was less the application qty, and save
                $purchaseToUseNext->quantity_remaining -= $applicationQuantity;
                $purchaseToUseNext->save();
            }
        }

        return $allocationResult;
    }
}
