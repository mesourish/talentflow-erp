<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Celebrity;
use App\Models\Claim;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Quotation;
use App\Models\TalentCategory;
use App\Models\User;
use App\Services\BookingAvailabilityService;
use App\Services\ClaimApprovalService;
use App\Services\CommissionCalculator;
use App\Services\ContractExpiryService;
use App\Services\InvoiceService;
use App\Services\QuotationCalculator;
use App\Services\QuotationConversionService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TalentFlowDemoScenarioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Quotation Calculation with 18% GST and Line Item discounts.
     */
    public function test_quotation_calculator_computes_correct_totals(): void
    {
        $calculator = app(QuotationCalculator::class);

        $items = [
            [
                'item_type' => 'celebrity_booking',
                'description' => 'Aanya Sen Endorsement',
                'quantity' => 1,
                'unit_price' => 1000000,
                'discount_amount' => 50000, // 50,000 discount => 950,000 taxable
                'tax_rate' => 18,           // 18% of 950,000 = 171,000
            ],
            [
                'item_type' => 'production',
                'description' => 'Studio & Crew Logistics',
                'quantity' => 1,
                'unit_price' => 100000,
                'discount_amount' => 0,
                'tax_rate' => 18,           // 18% of 100,000 = 18,000
            ],
        ];

        $result = $calculator->calculate($items);

        $this->assertEquals(1100000.00, $result['subtotal']);
        $this->assertEquals(50000.00, $result['discount_total']);
        $this->assertEquals(189000.00, $result['tax_total']);
        $this->assertEquals(1239000.00, $result['grand_total']);
    }

    /**
     * Test 2: Agency Commission Calculator (Default 20% + TDS deduction).
     */
    public function test_commission_calculator_applies_twenty_percent(): void
    {
        $calculator = app(CommissionCalculator::class);

        // Gross 10,00,000 INR
        $result = $calculator->calculate(1000000, null, null, 0);

        $this->assertEquals(1000000.00, $result['gross_amount']);
        $this->assertEquals(20.0, $result['commission_rate']);
        $this->assertEquals(200000.00, $result['commission_amount']); // 20%
        $this->assertEquals(800000.00, $result['net_payable']);        // 80%
    }

    /**
     * Test 3: Booking Availability Conflict Detection.
     */
    public function test_booking_conflict_detector_identifies_overlaps(): void
    {
        $availabilityService = app(BookingAvailabilityService::class);

        $user = User::factory()->create();
        $category = TalentCategory::create([
            'name' => 'Actors & Film',
            'code' => 'ACT-FLM',
            'is_active' => true,
        ]);

        $client = Client::create([
            'client_code' => 'CLT-TEST',
            'company_name' => 'Test Client',
            'client_type' => 'brand',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $project = Project::create([
            'project_code' => 'PRJ-TEST-01',
            'client_id' => $client->id,
            'project_name' => 'Test Campaign',
            'project_type' => 'campaign',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'budget_amount' => 500000,
            'currency' => 'INR',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $celebrity = Celebrity::create([
            'talent_code' => 'TLT-TEST',
            'stage_name' => 'Test Artist',
            'category_id' => $category->id,
            'tier' => 'A',
            'profile_status' => 'active',
            'created_by' => $user->id,
        ]);

        // Create booking from 10:00 to 14:00 tomorrow
        $start = Carbon::tomorrow()->setTime(10, 0, 0);
        $end = Carbon::tomorrow()->setTime(14, 0, 0);

        $booking = Booking::create([
            'booking_code' => 'BKG-TEST-001',
            'project_id' => $project->id,
            'title' => 'Morning Shoot',
            'booking_type' => 'shoot',
            'starts_at' => $start,
            'ends_at' => $end,
            'timezone' => 'Asia/Kolkata',
            'status' => 'confirmed',
            'created_by' => $user->id,
        ]);

        $booking->celebrities()->attach($celebrity->id, ['assignment_role' => 'Lead']);

        // Check overlapping slot: 12:00 to 16:00
        $overlapCheck = $availabilityService->getCelebrityConflicts(
            $celebrity->id,
            Carbon::tomorrow()->setTime(12, 0, 0),
            Carbon::tomorrow()->setTime(16, 0, 0)
        );

        $this->assertTrue($overlapCheck['has_conflict']);
        $this->assertCount(1, $overlapCheck['bookings']);

        // Check non-overlapping slot: 15:00 to 18:00
        $freeCheck = $availabilityService->getCelebrityConflicts(
            $celebrity->id,
            Carbon::tomorrow()->setTime(15, 0, 0),
            Carbon::tomorrow()->setTime(18, 0, 0)
        );

        $this->assertFalse($freeCheck['has_conflict']);
    }

    /**
     * Test 4: Invoice Partial Payment Deduction.
     */
    public function test_invoice_partial_payment_recording(): void
    {
        $invoiceService = app(InvoiceService::class);
        $user = User::factory()->create();

        $client = Client::create([
            'client_code' => 'CLT-TEST-2',
            'company_name' => 'Test Client 2',
            'client_type' => 'brand',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $project = Project::create([
            'project_code' => 'PRJ-TEST-02',
            'client_id' => $client->id,
            'project_name' => 'Test Campaign 2',
            'project_type' => 'campaign',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'budget_amount' => 500000,
            'currency' => 'INR',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $invoice = Invoice::create([
            'invoice_no' => 'INV-TEST-999',
            'client_id' => $client->id,
            'project_id' => $project->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(15),
            'currency' => 'INR',
            'subtotal' => 1000000,
            'discount_total' => 0,
            'tax_total' => 180000,
            'grand_total' => 1180000,
            'amount_paid' => 0,
            'status' => 'issued',
        ]);

        // Record partial payment of 5,00,000 INR
        $invoiceService->recordPayment($invoice->id, [
            'amount' => 500000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'UTR-TEST-123456',
        ]);

        $invoice->refresh();

        $this->assertEquals(500000.00, $invoice->amount_paid);
        $this->assertEquals(680000.00, $invoice->balance_due);
        $this->assertEquals('partially_paid', $invoice->status);

        // Record remaining balance payment of 6,80,000 INR
        $invoiceService->recordPayment($invoice->id, [
            'amount' => 680000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'UTR-TEST-789012',
        ]);

        $invoice->refresh();

        $this->assertEquals(1180000.00, $invoice->amount_paid);
        $this->assertEquals(0.00, $invoice->balance_due);
        $this->assertEquals('paid', $invoice->status);
    }

    /**
     * Test 5: Quotation Approval and Atomic Conversion to Project.
     */
    public function test_quotation_conversion_creates_project_and_prevents_duplicate_conversion(): void
    {
        $conversionService = app(QuotationConversionService::class);
        $user = User::factory()->create();

        $client = Client::create([
            'client_code' => 'CLT-TEST-3',
            'company_name' => 'ABC Fashion',
            'client_type' => 'brand',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $quotation = Quotation::create([
            'quotation_no' => 'QT-TEST-0001',
            'client_id' => $client->id,
            'status' => 'approved',
            'quotation_date' => now(),
            'valid_until' => now()->addDays(30),
            'currency' => 'INR',
            'subtotal' => 1100000,
            'discount_total' => 50000,
            'tax_total' => 189000,
            'grand_total' => 1239000,
            'created_by' => $user->id,
        ]);

        // Convert approved quotation
        $project = $conversionService->convert($quotation->id, 'Festive Brand Ambassador Campaign 2026', 'campaign');

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($client->id, $project->client_id);
        $this->assertEquals(1239000.00, $project->budget_amount);
        $this->assertEquals($quotation->id, $project->quotation_id);

        $quotation->refresh();
        $this->assertEquals('converted', $quotation->status);

        // Verify duplicate conversion is blocked
        $this->expectException(DomainException::class);
        $conversionService->convert($quotation->id);
    }

    /**
     * Test 6: Claim Approval posts Project Expense and blocks double-posting.
     */
    public function test_claim_approval_posts_expense_and_prevents_double_posting(): void
    {
        $approvalService = app(ClaimApprovalService::class);
        $user = User::factory()->create();

        $client = Client::create([
            'client_code' => 'CLT-TEST-4',
            'company_name' => 'XYZ Global',
            'client_type' => 'brand',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $project = Project::create([
            'project_code' => 'PRJ-TEST-04',
            'client_id' => $client->id,
            'project_name' => 'Global Launch',
            'project_type' => 'campaign',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'budget_amount' => 500000,
            'currency' => 'INR',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $claim = Claim::create([
            'claim_no' => 'CLM-TEST-001',
            'project_id' => $project->id,
            'submitted_by' => $user->id,
            'claim_date' => now(),
            'description' => 'Travel & Hotel reimbursement',
            'total_amount' => 75000.00,
            'currency' => 'INR',
            'status' => 'pending',
        ]);

        // Approve claim
        $approvedClaim = $approvalService->approve($claim->id, $user->id, 'Receipts verified.');

        $this->assertEquals('approved', $approvedClaim->status);

        // Verify project expense was created
        $expense = ProjectExpense::where('source_claim_id', $claim->id)->first();
        $this->assertNotNull($expense);
        $this->assertEquals(75000.00, $expense->amount);
        $this->assertEquals($project->id, $expense->project_id);

        // Try to approve again => must throw DomainException
        $this->expectException(DomainException::class);
        $approvalService->approve($claim->id, $user->id);
    }

    /**
     * Test 7: Contract Expiry Detection for 30-day notifications.
     */
    public function test_contract_expiry_service_identifies_expiring_agreements(): void
    {
        $expiryService = app(ContractExpiryService::class);
        $user = User::factory()->create();

        // Create contract expiring exactly 30 days from now
        $expiringContract = Contract::create([
            'contract_no' => 'CNT-TEST-001',
            'contract_title' => 'Celebrity Endorsement 2026',
            'contract_type' => 'Endorsement',
            'start_date' => now()->subMonths(11),
            'expiry_date' => now()->addDays(30)->toDateString(),
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $notified = $expiryService->checkAndNotifyExpiringContracts(30);

        $this->assertCount(1, $notified);
        $this->assertEquals('CNT-TEST-001', $notified[0]->contract_no);
    }
}
