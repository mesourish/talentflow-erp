<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header-table, .info-table, .items-table, .totals-table, .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .logo-sub {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .quote-title {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
        }
        .quote-meta {
            text-align: right;
            font-size: 11px;
            color: #64748b;
        }
        .box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .box h4 {
            margin: 0 0 6px 0;
            font-size: 12px;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .items-table {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px;
            text-align: left;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-table td {
            padding: 4px 8px;
            font-size: 11px;
        }
        .grand-total-row td {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            border-top: 2px solid #0f172a;
            padding-top: 6px;
        }
        .terms-box {
            margin-top: 25px;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            color: #475569;
        }
        .signature-box {
            margin-top: 40px;
            width: 100%;
        }
        .sig-line {
            border-top: 1px solid #333333;
            width: 200px;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="logo-text">TALENTFLOW <span style="color: #2563eb;">ERP</span></div>
                <div class="logo-sub">Celebrity, Talent & Brand Management</div>
                <div style="margin-top: 6px; font-size: 10px; color: #64748b;">
                    Level 14, One International Center, Senapati Bapat Marg,<br>
                    Prabhadevi, Mumbai, Maharashtra 400013 &bull; GSTIN: 27AABCT9988E1Z4
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="quote-title">COMMERCIAL PROPOSAL</div>
                <div class="quote-meta">
                    <strong>Quotation #:</strong> {{ $quotation->quotation_no }} (v{{ $quotation->version_no }})<br>
                    <strong>Date:</strong> {{ $quotation->quotation_date->format('d F Y') }}<br>
                    <strong>Valid Until:</strong> {{ $quotation->valid_until->format('d F Y') }}<br>
                    <strong>Status:</strong> {{ strtoupper($quotation->status) }}
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td style="width: 50%; padding-right: 10px;">
                <div class="box">
                    <h4>CLIENT / BILL TO:</h4>
                    <strong>{{ $quotation->client->company_name }}</strong><br>
                    {{ $quotation->client->billing_address ?? 'Address on file' }}<br>
                    @if($quotation->client->tax_number)
                        GSTIN / Tax ID: {{ $quotation->client->tax_number }}<br>
                    @endif
                    Email: {{ $quotation->client->email ?? 'N/A' }} | Phone: {{ $quotation->client->phone ?? 'N/A' }}
                </div>
            </td>
            <td style="width: 50%; padding-left: 10px;">
                <div class="box">
                    <h4>PROPOSAL SUMMARY:</h4>
                    <strong>Campaign:</strong> {{ $quotation->notes ?? 'Celebrity Brand Engagement' }}<br>
                    <strong>Account Lead:</strong> {{ $quotation->creator->name }}<br>
                    <strong>Currency:</strong> {{ $quotation->currency }}<br>
                    <strong>Payment Terms:</strong> Net {{ $quotation->client->payment_terms_days }} Days
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 32%;">Service Deliverable</th>
                <th style="width: 20%;">Artist / Talent</th>
                <th class="text-center" style="width: 8%;">Qty</th>
                <th class="text-right" style="width: 15%;">Unit Rate</th>
                <th class="text-center" style="width: 8%;">Tax</th>
                <th class="text-right" style="width: 12%;">Total (INR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->service_name }}</strong>
                        @if($item->description)
                            <div style="font-size: 10px; color: #64748b;">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td>{{ $item->celebrity ? $item->celebrity->stage_name : 'Agency Production' }}</td>
                    <td class="text-center">{{ (float)$item->quantity }} {{ $item->unit }}</td>
                    <td class="text-right">INR {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center">{{ (float)$item->tax_rate }}%</td>
                    <td class="text-right"><strong>INR {{ number_format($item->line_total, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="terms-box">
                    <strong>COMMERCIAL TERMS & SIGN-OFF:</strong><br>
                    {!! nl2br(e($quotation->terms_conditions)) !!}
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                <table class="totals-table">
                    <tr>
                        <td class="text-right text-muted">Subtotal (Pre-Tax):</td>
                        <td class="text-right" style="width: 130px;">INR {{ number_format($quotation->subtotal, 2) }}</td>
                    </tr>
                    @if($quotation->discount_total > 0)
                        <tr>
                            <td class="text-right text-muted">Total Discount:</td>
                            <td class="text-right" style="color: #dc2626;">- INR {{ number_format($quotation->discount_total, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right text-muted">Statutory GST (Tax):</td>
                        <td class="text-right">INR {{ number_format($quotation->tax_total, 2) }}</td>
                    </tr>
                    <tr class="grand-total-row">
                        <td class="text-right">Grand Total:</td>
                        <td class="text-right">INR {{ number_format($quotation->grand_total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="signature-box">
        <tr>
            <td style="width: 50%;">
                <div class="sig-line">
                    <strong>Authorized Agency Signatory</strong><br>
                    TalentFlow Management Ltd.
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="sig-line" style="margin-left: auto;">
                    <strong>Client Acceptance & Seal</strong><br>
                    {{ $quotation->client->company_name }}
                </div>
            </td>
        </tr>
    </table>
</body>
</html>

