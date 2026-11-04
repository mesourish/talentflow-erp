@extends('layouts.app')

@section('title', 'Revise Quotation ' . $quotation->quotation_no)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.quotations.index') }}">Quotations</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.quotations.show', $quotation) }}">{{ $quotation->quotation_no }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Revise</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Revise Quotation: {{ $quotation->quotation_no }} (v{{ $quotation->version_no }})</h4>
            <p class="text-muted small mb-0">Saving will increment to version {{ $quotation->version_no + 1 }} and require re-approval</p>
        </div>
        <a href="{{ route('admin.quotations.show', $quotation) }}" class="btn btn-light border btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Quote
        </a>
    </div>

    <form method="POST" action="{{ route('admin.quotations.update', $quotation) }}" id="quotationForm">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle text-primary me-2"></i> Header Details</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Quotation Number</label>
                        <input type="text" class="form-control bg-light" value="{{ $quotation->quotation_no }}" readonly>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Client Organization</label>
                        <input type="text" class="form-control bg-light" value="{{ $quotation->client->company_name }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Quote Date</label>
                        <input type="date" name="quotation_date" class="form-control" value="{{ old('quotation_date', $quotation->quotation_date->toDateString()) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $quotation->valid_until->toDateString()) }}" required>
                    </div>
                    <input type="hidden" name="currency" value="INR">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Revision Notes / Reason</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes', $quotation->notes) }}" placeholder="e.g. Revised based on client negotiation discount...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check text-primary me-2"></i> Line Items</h6>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="btnAddCustomRow">
                    <i class="bi bi-plus-lg me-1"></i> Add Row
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="itemsTable">
                        <thead class="table-light small">
                            <tr>
                                <th style="width: 22%;">Service Name</th>
                                <th style="width: 18%;">Talent</th>
                                <th style="width: 22%;">Description</th>
                                <th style="width: 8%;">Qty</th>
                                <th style="width: 12%;">Rate</th>
                                <th style="width: 8%;">Discount</th>
                                <th style="width: 6%;">Tax %</th>
                                <th style="width: 12%;">Total</th>
                                <th style="width: 4%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            @foreach($quotation->items as $idx => $item)
                                <tr class="item-row">
                                    <td>
                                        <input type="text" name="items[{{ $idx }}][service_name]" class="form-control form-control-sm" value="{{ $item->service_name }}" required>
                                    </td>
                                    <td>
                                        <select name="items[{{ $idx }}][celebrity_id]" class="form-select form-select-sm item-celebrity">
                                            <option value="">-- No Talent --</option>
                                            @foreach($celebrities as $celeb)
                                                <option value="{{ $celeb->id }}" {{ $item->celebrity_id == $celeb->id ? 'selected' : '' }}>{{ $celeb->stage_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $idx }}][description]" class="form-control form-control-sm" value="{{ $item->description }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][quantity]" class="form-control form-control-sm text-center item-qty" value="{{ (float)$item->quantity }}" step="0.5" min="0.5" required>
                                        <input type="hidden" name="items[{{ $idx }}][unit]" value="{{ $item->unit }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][unit_price]" class="form-control form-control-sm text-end item-price" value="{{ (float)$item->unit_price }}" step="1000" min="0" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][discount_amount]" class="form-control form-control-sm text-end item-discount" value="{{ (float)$item->discount_amount }}" step="500" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][tax_rate]" class="form-control form-control-sm text-center item-tax" value="{{ (float)$item->tax_rate }}" step="0.5" min="0">
                                    </td>
                                    <td class="text-end fw-bold item-line-total">
                                        INR {{ number_format($item->line_total, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 py-0 btn-remove-row"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-light p-4">
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted">Subtotal:</td>
                                <td class="text-end fw-semibold" id="summarySubtotal">INR 0.00</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Discount:</td>
                                <td class="text-end text-danger fw-semibold" id="summaryDiscount">- INR 0.00</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tax Amount:</td>
                                <td class="text-end fw-semibold" id="summaryTax">INR 0.00</td>
                            </tr>
                            <tr class="border-top border-dark border-2">
                                <td class="fs-5 fw-bold text-dark pt-2">Grand Total:</td>
                                <td class="fs-5 fw-bold text-primary text-end pt-2" id="summaryGrandTotal">INR 0.00</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-text text-primary me-2"></i> Commercial Terms</h6>
            </div>
            <div class="card-body p-4">
                <textarea name="terms_conditions" class="form-control" rows="3">{{ old('terms_conditions', $quotation->terms_conditions) }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mb-5">
            <a href="{{ route('admin.quotations.show', $quotation) }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
                <i class="bi bi-check-lg me-1"></i> Save Revision
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let rowIndex = {{ $quotation->items->count() }};

    function recalculate() {
        let subtotal = 0, discountTotal = 0, taxTotal = 0;

        $('.item-row').each(function () {
            let row = $(this);
            let qty = parseFloat(row.find('.item-qty').val()) || 0;
            let price = parseFloat(row.find('.item-price').val()) || 0;
            let discount = parseFloat(row.find('.item-discount').val()) || 0;
            let taxRate = parseFloat(row.find('.item-tax').val()) || 0;

            let lineAmount = qty * price;
            if (discount > lineAmount) discount = lineAmount;

            let taxable = lineAmount - discount;
            let tax = taxable * (taxRate / 100);
            let total = taxable + tax;

            subtotal += lineAmount;
            discountTotal += discount;
            taxTotal += tax;

            row.find('.item-line-total').text('INR ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        });

        let grandTotal = subtotal - discountTotal + taxTotal;
        $('#summarySubtotal').text('INR ' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        $('#summaryDiscount').text('- INR ' + discountTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        $('#summaryTax').text('INR ' + taxTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
        $('#summaryGrandTotal').text('INR ' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    }

    $(document).on('input change', '.item-qty, .item-price, .item-discount, .item-tax', recalculate);

    $(document).on('click', '.btn-remove-row', function () {
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
            recalculate();
        }
    });

    $('#btnAddCustomRow').click(function () {
        let celebOptions = $('#itemsTableBody select.item-celebrity').first().html();
        let newRow = `
            <tr class="item-row">
                <td><input type="text" name="items[${rowIndex}][service_name]" class="form-control form-control-sm" placeholder="Service description..." required></td>
                <td><select name="items[${rowIndex}][celebrity_id]" class="form-select form-select-sm item-celebrity">${celebOptions}</select></td>
                <td><input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm" placeholder="Scope notes..."></td>
                <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm text-center item-qty" value="1" step="0.5" min="0.5" required><input type="hidden" name="items[${rowIndex}][unit]" value="service"></td>
                <td><input type="number" name="items[${rowIndex}][unit_price]" class="form-control form-control-sm text-end item-price" value="50000" step="1000" min="0" required></td>
                <td><input type="number" name="items[${rowIndex}][discount_amount]" class="form-control form-control-sm text-end item-discount" value="0" step="500" min="0"></td>
                <td><input type="number" name="items[${rowIndex}][tax_rate]" class="form-control form-control-sm text-center item-tax" value="18" step="0.5" min="0"></td>
                <td class="text-end fw-bold item-line-total">INR 59,000.00</td>
                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm p-1 py-0 btn-remove-row"><i class="bi bi-trash"></i></button></td>
            </tr>
        `;
        $('#itemsTableBody').append(newRow);
        rowIndex++;
        recalculate();
    });

    recalculate();
});
</script>
@endpush

