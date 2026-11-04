<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Services
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Services;

use InvalidArgumentException;

/**
 * Class QuotationCalculator
 *
 * Domain service calculating deliverable line items, agency service fees, logistical expenses, and GST breakdown.
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class QuotationCalculator
{
    /**
     * Calculate line and header totals for quotation items.
     *
     * @param array $items Array of item rows with quantity, unit_price, discount_amount, tax_rate
     * @return array
     */
    public function calculate(array $items): array
    {
        $subtotal = 0.0;
        $discountTotal = 0.0;
        $taxTotal = 0.0;
        $calculatedItems = [];

        foreach ($items as $index => $item) {
            $quantity = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $lineAmount = round($quantity * $unitPrice, 2);

            $discountAmount = (float) ($item['discount_amount'] ?? 0);
            if ($discountAmount < 0 || $discountAmount > $lineAmount) {
                throw new InvalidArgumentException("Invalid discount on item #".($index + 1).". Discount cannot be negative or exceed line subtotal.");
            }

            $taxableAmount = $lineAmount - $discountAmount;
            $taxRate = (float) ($item['tax_rate'] ?? 0);
            $taxAmount = round($taxableAmount * ($taxRate / 100), 2);
            $lineTotal = round($taxableAmount + $taxAmount, 2);

            $subtotal += $lineAmount;
            $discountTotal += $discountAmount;
            $taxTotal += $taxAmount;

            $item['line_amount'] = $lineAmount;
            $item['taxable_amount'] = $taxableAmount;
            $item['tax_amount'] = $taxAmount;
            $item['line_total'] = $lineTotal;
            $calculatedItems[] = $item;
        }

        $grandTotal = round($subtotal - $discountTotal + $taxTotal, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'grand_total' => $grandTotal,
            'items' => $calculatedItems,
        ];
    }
}

