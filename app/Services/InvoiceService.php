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

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Payment;
use DomainException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Class InvoiceService
 *
 * Domain service managing invoice generation, payment allocations, balance calculations, and receipt reconciliation.
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class InvoiceService
{
    /**
     * Record a partial or full payment against an invoice.
     */
    public function recordPayment(int $invoiceId, array $data): Payment
    {
        return DB::transaction(function () use ($invoiceId, $data) {
            $invoice = Invoice::lockForUpdate()->findOrFail($invoiceId);

            $amount = (float) ($data['amount'] ?? 0);
            if ($amount <= 0) {
                throw new InvalidArgumentException('Payment amount must be greater than zero.');
            }

            $outstanding = round($invoice->grand_total - $invoice->amount_paid, 2);
            if ($amount > $outstanding) {
                throw new DomainException("Payment amount (INR {$amount}) cannot exceed outstanding balance (INR {$outstanding}).");
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_reference' => $data['payment_reference'] ?? null,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'notes' => $data['notes'] ?? null,
                'received_by' => auth()->id() ?? 1,
            ]);

            $newAmountPaid = round($invoice->amount_paid + $amount, 2);
            $newStatus = ($newAmountPaid >= $invoice->grand_total) ? 'paid' : 'partially_paid';

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'status' => $newStatus,
            ]);

            ActivityLog::log('payment_recorded', "Payment of INR {$amount} recorded for Invoice #{$invoice->invoice_no}", $payment);

            return $payment;
        });
    }

    /**
     * Recalculate invoice status based on due date and payments.
     */
    public function refreshInvoiceStatus(Invoice $invoice): void
    {
        $amountPaid = (float) $invoice->payments()->sum('amount');
        $grandTotal = (float) $invoice->grand_total;

        if ($amountPaid >= $grandTotal && $grandTotal > 0) {
            $status = 'paid';
        } elseif ($amountPaid > 0) {
            $status = 'partially_paid';
        } elseif ($invoice->due_date && $invoice->due_date < now()->toDateString() && $invoice->status !== 'draft') {
            $status = 'overdue';
        } else {
            $status = ($invoice->status === 'draft') ? 'draft' : 'issued';
        }

        $invoice->update([
            'amount_paid' => $amountPaid,
            'status' => $status,
        ]);
    }
}

