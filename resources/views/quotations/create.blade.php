@extends('layouts.app')

@section('title', 'Dynamic Quotation Builder')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.quotations.index') }}">Quotations</a></li>
    <li class="breadcrumb-item active" aria-current="page">Builder</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Quotation Automation Builder</h4>
            <p class="text-muted small mb-0">Configure multi-talent deliverables, service fee lines, logistical allowances and taxes</p>
        </div>
        <a href="{{ route('admin.quotations.index') }}" class="btn btn-light border btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Quotations
        </a>
    </div>

    <form method="POST" action="{{ route('admin.quotations.store') }}" id="quotationForm">
        @csrf

        <!-- 1. Header Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle text-primary me-2"></i> 1. Commercial Header & Client</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Quotation Number <span class="text-danger">*</span></label>
                        <input type="text" name="quotation_no" class="form-control" value="{{ old('quotation_no', $suggestedNo) }}" required>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Client Organization <span class="text-danger">*</span></label>
                        <select name="client_id" id="clientSelect" class="form-select" required>
                            <option value="">-- Choose Client Brand / Agency --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ (old('client_id', request('client_id')) == $c->id) ? 'selected' : '' }}>
                                    {{ $c->company_name }} ({{ $c->client_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Linked Lead / Opportunity (Optional)</label>
                        <select name="lead_id" class="form-select">
                            <option value="">-- No Direct Lead Link --</option>
                            @foreach($leads as $l)
                                <option value="{{ $l->id }}" {{ (old('lead_id', request('lead_id')) == $l->id) ? 'selected' : '' }}>
                                    {{ $l->lead_title }} ({{ $l->lead_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Quotation Date <span class="text-danger">*</span></label>
                        <input type="date" name="quotation_date" class="form-control" value="{{ old('quotation_date', now()->toDateString()) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Validity Date <span class="text-danger">*</span></label>
                        <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', now()->addDays(30)->toDateString()) }}" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Currency <span class="text-danger">*</span></label>
                        <input type="text" name="currency" class="form-control" value="INR" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Campaign Description / Brief</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="e.g. Brand X Autumn TVC & Stills Shoot">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Talent Quick Selector Bar -->
        <div class="card border-primary-subtle bg-primary-subtle mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary p-2"><i class="bi bi-person-badge"></i></span>
                        <div>
                            <span class="fw-bold text-primary-emphasis d-block small">Talent Roster Quick Rate Picker</span>
                            <small class="text-muted">Select an artist to auto-fill their standard rate card into the items table</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <select id="quickCelebritySelect" class="form-select form-select-sm" style="min-width: 250px;">
                            <option value="">-- Select Talent Roster --</option>
                            @foreach($celebrities as $celeb)
                                <option value="{{ $celeb->id }}" data-rates="{{ json_encode($celeb->activeRates) }}">
                                    {{ $celeb->stage_name }} ({{ $celeb->category->name }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 text-nowrap" id="btnAddTalentRate">
                            <i class="bi bi-plus-circle me-1"></i> Add To Quote
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Dynamic Line Items Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check text-primary me-2"></i> 2. Deliverables & Service Fee Lines</h6>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="btnAddCustomRow">
                    <i class="bi bi-plus-lg me-1"></i> Add Custom Service Row
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="itemsTable">
                        <thead class="table-light small">
                            <tr>
                                <th style="width: 20%;">Deliverable / Service</th>
                                <th style="width: 18%;">Talent Assigned</th>
                                <th style="width: 22%;">Scope & Description</th>
                                <th style="width: 8%;">Qty</th>
                                <th style="width: 14%;">Unit Rate (INR)</th>
                                <th style="width: 8%;">Discount</th>
                                <th style="width: 6%;">Tax %</th>
                                <th style="width: 12%;">Line Total</th>
                                <th style="width: 4%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Row 1 Default -->
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="items[0][service_name]" class="form-control form-control-sm item-service" value="Celebrity Appearance & Brand Endorsement" required>
                                </td>
                                <td>
                                    <select name="items[0][celebrity_id]" class="form-select form-select-sm item-celebrity">
                                        <option value="">-- No Talent (Production) --</option>
                                        @foreach($celebrities as $celeb)
                                            <option value="{{ $celeb->id }}">{{ $celeb->stage_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="items[0][description]" class="form-control form-control-sm" value="Full commercial campaign ambassadorship and broadcast rights" placeholder="Deliverables scope...">
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm text-center item-qty" value="1" step="0.5" min="0.5" required>
                                    <input type="hidden" name="items[0][unit]" value="service">
                                </td>
                                <td>
                                    <input type="number" name="items[0][unit_price]" class="form-control form-control-sm text-end item-price" value="800000" step="1000" min="0" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][discount_amount]" class="form-control form-control-sm text-end item-discount" value="0" step="500" min="0">
                                </td>
                                <td>
                                    <input type="number" name="items[0][tax_rate]" class="form-control form-control-sm text-center item-tax" value="18" step="0.5" min="0">
                                </td>
                                <td class="text-end fw-bold item-line-total">
                                    INR 944,000.00
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm p-1 py-0 btn-remove-row" title="Remove"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Financial Summary Box -->
            <div class="card-footer bg-light p-4">
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted">Subtotal (Pre-tax):</td>
                                <td class="text-end fw-semibold" id="summarySubtotal">INR 800,000.00</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Discount Deductions:</td>
                                <td class="text-end text-danger fw-semibold" id="summaryDiscount">- INR 0.00</td>
                            </tr>
                            <tr>
                                <td class="text-muted">GST Tax Amount (Standard 18%):</td>
                                <td class="text-end fw-semibold" id="summaryTax">INR 144,000.00</td>
                            </tr>
                            <tr class="border-top border-dark border-2">
                                <td class="fs-5 fw-bold text-dark pt-2">Grand Total Payable:</td>
                                <td class="fs-5 fw-bold text-primary text-end pt-2" id="summaryGrandTotal">INR 944,000.00</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Commercial Terms & Conditions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text text-primary me-2"></i> 3. Standard Commercial Terms</h6>
            </div>
            <div class="card-body p-4">
                <textarea name="terms_conditions" class="form-control" rows="4">1. Payment Terms: 50% advance booking deposit upon signed quotation; 50% prior to shoot/event commencement.
2. Exclusivity: Direct competitor category exclusivity applies during active agreement period.
3. Overtime: Call times exceeding 8 hours billed pro-rata as per standard artist rate card.
4. Logistics: First class flights, 5-star hotel accommodations and luxury security transport provided by client.</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-end gap-3 mb-5">
            <a href="{{ route('admin.quotations.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold shadow-sm">
                <i class="bi bi-check-circle me-1"></i> Save Draft Quotation
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let rowIndex = 1;

    // Recalculate row and summary totals
    function recalculate() {
        let subtotal = 0;
        let discountTotal = 0;
        let taxTotal = 0;

        $('.item-row').each(function () {
            let row = $(this);
            let qty = parseFloat(row.find('.item-qty').val()) || 0;
            let price = parseFloat(row.find('.item-price').val()) || 0;
            let discount = parseFloat(row.find('.item-discount').val()) || 0;
            let taxRate = parseFloat(row.find('.item-tax').val()) || 0;

            let lineAmount = qty * price;
            if (discount > lineAmount) {
                discount = lineAmount;
                row.find('.item-discount').val(discount);
            }

            let taxable = lineAmount - discount;
            let tax = taxable * (taxRate / 100);
            let total = taxable + tax;

            subtotal += lineAmount;
            discountTotal += discount;
            taxTotal += tax;

            row.find('.item-line-total').text('INR ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        });

        let grandTotal = subtotal - discountTotal + taxTotal;

        $('#summarySubtotal').text('INR ' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryDiscount').text('- INR ' + discountTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryTax').text('INR ' + taxTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryGrandTotal').text('INR ' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    // Input change triggers calculation
    $(document).on('input change', '.item-qty, .item-price, .item-discount, .item-tax', function () {
        recalculate();
    });

    // Remove row
    $(document).on('click', '.btn-remove-row', function () {
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
            recalculate();
        } else {
            alert('A quotation must contain at least one service item row.');
        }
    });

    // Add custom row
    $('#btnAddCustomRow').click(function () {
        let celebOptions = $('#itemsTableBody select.item-celebrity').first().html();
        let newRow = `
            <tr class="item-row">
                <td>
                    <input type="text" name="items[${rowIndex}][service_name]" class="form-control form-control-sm item-service" placeholder="Service description..." required>
                </td>
                <td>
                    <select name="items[${rowIndex}][celebrity_id]" class="form-select form-select-sm item-celebrity">
                        ${celebOptions}
                    </select>
                </td>
                <td>
                    <input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm" placeholder="Scope notes...">
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm text-center item-qty" value="1" step="0.5" min="0.5" required>
                    <input type="hidden" name="items[${rowIndex}][unit]" value="service">
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][unit_price]" class="form-control form-control-sm text-end item-price" value="50000" step="1000" min="0" required>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][discount_amount]" class="form-control form-control-sm text-end item-discount" value="0" step="500" min="0">
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][tax_rate]" class="form-control form-control-sm text-center item-tax" value="18" step="0.5" min="0">
                </td>
                <td class="text-end fw-bold item-line-total">
                    INR 59,000.00
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm p-1 py-0 btn-remove-row" title="Remove"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
        $('#itemsTableBody').append(newRow);
        rowIndex++;
        recalculate();
    });

    // Quick add talent rate card
    $('#btnAddTalentRate').click(function () {
        let selectedOption = $('#quickCelebritySelect option:selected');
        let celebId = selectedOption.val();
        let celebName = selectedOption.text().trim();
        let rates = selectedOption.data('rates');

        if (!celebId || !rates || rates.length === 0) {
            alert('Please select a celebrity with active rate cards.');
            return;
        }

        let celebOptions = $('#itemsTableBody select.item-celebrity').first().html();

        rates.forEach(function (r) {
            let rowHtml = `
                <tr class="item-row">
                    <td>
                        <input type="text" name="items[${rowIndex}][service_name]" class="form-control form-control-sm item-service" value="${r.service_type}" required>
                    </td>
                    <td>
                        <select name="items[${rowIndex}][celebrity_id]" class="form-select form-select-sm item-celebrity">
                            ${celebOptions}
                        </select>
                    </td>
                    <td>
                        <input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm" value="${r.description || ''}">
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm text-center item-qty" value="1" step="0.5" min="0.5" required>
                        <input type="hidden" name="items[${rowIndex}][unit]" value="service">
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][unit_price]" class="form-control form-control-sm text-end item-price" value="${r.standard_fee}" step="1000" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][discount_amount]" class="form-control form-control-sm text-end item-discount" value="0" step="500" min="0">
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][tax_rate]" class="form-control form-control-sm text-center item-tax" value="18" step="0.5" min="0">
                    </td>
                    <td class="text-end fw-bold item-line-total">
                        INR 0.00
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm p-1 py-0 btn-remove-row"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            let appended = $(rowHtml);
            appended.find('.item-celebrity').val(celebId);
            $('#itemsTableBody').append(appended);
            rowIndex++;
        });

        recalculate();
    });

    recalculate();
});
</script>
@endpush

