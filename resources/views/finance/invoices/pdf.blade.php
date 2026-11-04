<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - {{ $invoice->invoice_no }}</title>
    <style>
        @page {
            margin: 25mm 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            color: #2d3748;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .agency-name {
            font-size: 20pt;
            font-weight: bold;
            color: #1e1b4b;
            letter-spacing: -0.5px;
        }
        .agency-sub {
            font-size: 8.5pt;
            color: #64748b;
        }
        .invoice-title-block {
            text-align: right;
        }
        .invoice-title {
            font-size: 16pt;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
        }
        .invoice-no {
            font-size: 11pt;
            font-weight: bold;
            color: #1e293b;
            font-family: monospace;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .meta-table td {
            vertical-align: top;
            width: 50%;
            font-size: 9.5pt;
        }
        .section-heading {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }
        .party-name {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9.5pt;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .totals-table td {
            padding: 5px 8px;
            font-size: 9.5pt;
        }
        .grand-total-row td {
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-size: 11pt;
            font-weight: bold;
            color: #1e1b4b;
            padding: 8px;
        }
        .bank-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
            border-radius: 6px;
            font-size: 8.5pt;
            color: #475569;
            margin-bottom: 30px;
        }
        .bank-box strong {
            color: #0f172a;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-table td {
            width: 50%;
            vertical-align: bottom;
            font-size: 9pt;
        }
        .stamp-line {
            width: 200px;
            border-bottom: 1px solid #94a3b8;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 55%; vertical-align: middle;">
                <div class="agency-name">TALENTFLOW ERP</div>
                <div class="agency-sub">
                    Celebrity, Talent & Brand Management Agency<br>
                    Level 14, One World Centre, Lower Parel, Mumbai 400013<br>
                    GSTIN: 27AABCT1234F1Z5 • PAN: AABCT1234F
                </div>
            </td>
            <td class="invoice-title-block" style="width: 45%; vertical-align: middle;">
                <div class="invoice-title">TAX INVOICE</div>
                <div class="invoice-no">{{ $invoice->invoice_no }}</div>
                <div style="font-size: 9pt; color: #64748b; margin-top: 4px;">
                    Date: <strong>{{ $invoice->invoice_date?->format('d M Y') }}</strong><br>
                    Due Date: <strong>{{ $invoice->due_date?->format('d M Y') }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Parties & Meta -->
    <table class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="section-heading">Billed To (Client / Customer):</div>
                <div class="party-name">{{ $invoice->client?->company_name }}</div>
                <div style="color: #475569; margin-top: 3px;">
                    {{ $invoice->client?->billing_address ?? 'Mumbai, India' }}<br>
                    GSTIN: <strong>{{ $invoice->client?->tax_number ?? 'URP' }}</strong><br>
                    Contact: {{ $invoice->client?->billing_email ?? 'accounts@client.com' }}
                </div>
            </td>
            <td style="padding-left: 20px;">
                <div class="section-heading">Project & Engagement Reference:</div>
                <div style="font-weight: bold; color: #0f172a;">
                    {{ $invoice->project?->project_name ?? 'General Agency Services' }}
                </div>
                <div style="color: #475569; margin-top: 3px;">
                    Project Code: <strong>{{ $invoice->project?->project_code ?? 'PRJ-N/A' }}</strong><br>
                    Payment Terms: <strong>Net 15 Days</strong><br>
                    Currency: <strong>{{ $invoice->currency }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Description of Goods / Services</th>
                <th class="text-right" style="width: 10%;">Qty</th>
                <th class="text-right" style="width: 15%;">Unit Rate (₹)</th>
                <th class="text-right" style="width: 10%;">GST %</th>
                <th class="text-right" style="width: 15%;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold; color: #0f172a;">{{ $item->description }}</div>
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ $item->tax_rate }}%</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Table -->
    <table class="totals-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="color: #475569;">Taxable Subtotal:</td>
            <td class="text-right" style="font-weight: bold;">₹{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td style="color: #475569;">GST Tax Total (18%):</td>
            <td class="text-right" style="font-weight: bold;">₹{{ number_format($invoice->tax_total, 2) }}</td>
        </tr>
        <tr class="grand-total-row">
            <td>Grand Total (INR):</td>
            <td class="text-right">₹{{ number_format($invoice->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td style="color: #16a34a; font-weight: bold;">Amount Paid:</td>
            <td class="text-right" style="color: #16a34a; font-weight: bold;">₹{{ number_format($invoice->amount_paid, 2) }}</td>
        </tr>
        <tr>
            <td style="color: #dc2626; font-weight: bold;">Balance Due:</td>
            <td class="text-right" style="color: #dc2626; font-weight: bold;">₹{{ number_format($invoice->balance_due, 2) }}</td>
        </tr>
    </table>

    <!-- Bank Details -->
    <div class="bank-box">
        <strong>Bank Remittance Account Details for Electronic Transfer (NEFT/RTGS):</strong><br>
        Beneficiary Name: <strong>TalentFlow Entertainment Private Limited</strong><br>
        Bank Name: <strong>HDFC Bank Ltd</strong> • Branch: <strong>Fort Branch, Mumbai</strong><br>
        Current Account Number: <strong>50200012345678</strong> • IFSC Code: <strong>HDFC0000060</strong>
    </div>

    <!-- Signatures -->
    <table class="signature-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div style="font-size: 8pt; color: #64748b;">
                    Declaration:<br>
                    We declare that this invoice shows the actual price of the services described and that all particulars are true and correct.
                </div>
            </td>
            <td style="text-align: right;">
                <div class="stamp-line" style="margin-left: auto;"></div>
                <div style="font-weight: bold; color: #0f172a;">For TalentFlow Entertainment Pvt. Ltd.</div>
                <div style="font-size: 8pt; color: #64748b;">Authorized Signatory</div>
            </td>
        </tr>
    </table>

</body>
</html>

