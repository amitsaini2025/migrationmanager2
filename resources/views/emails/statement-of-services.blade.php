<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Helvetica Neue', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.4;
        }
        .sheet {
            max-width: 800px;
            margin: 0 auto;
            padding: 8px;
        }
        .header-section {
            border-bottom: 3px solid #3abaf4;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .company-name {
            font-size: 15px;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 13px;
            line-height: 1.6;
            color: #1f2937;
            margin-top: 8px;
        }
        .document-title {
            font-size: 26px;
            font-weight: 700;
            color: #3abaf4;
            margin: 0 0 10px 0;
        }
        .document-info {
            font-size: 13px;
            line-height: 1.7;
            color: #1f2937;
        }
        .bill-to-section {
            background: #f9f9f9;
            padding: 10px 12px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .bill-to-label {
            font-size: 13px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .bill-to-content {
            font-size: 13px;
            line-height: 1.5;
            color: #1f2937;
        }
        .matter-info {
            font-size: 13px;
            color: #1f2937;
            margin: 8px 0;
        }
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ledger-table thead {
            background: #3abaf4;
        }
        .ledger-table thead th {
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #ffffff !important;
            text-transform: uppercase;
        }
        .ledger-table tbody td {
            padding: 9px 8px;
            font-size: 12px;
            color: #1f2937;
            border-bottom: 1px solid #eee;
        }
        .ledger-table thead th:last-child,
        .ledger-table tbody td:last-child {
            text-align: right;
        }
        .totals-section {
            margin: 12px 0;
            padding: 10px 12px;
            background: #f9f9f9;
            text-align: right;
            font-size: 13px;
            font-weight: 700;
        }
        .statement-note {
            margin-top: 12px;
            padding: 10px 12px;
            background: #f0f8ff;
            border-left: 4px solid #3abaf4;
            font-size: 12px;
            color: #1f2937;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('img/logo.png');
        $logoData = '';
        if (file_exists($logoPath)) {
            $logoData = 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath));
        }
    @endphp
    <div class="sheet">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr class="header-section">
                <td style="text-align: left; width: 50%; vertical-align: top;">
                    @if($logoData)
                        <img width="85" style="height:auto;display:block;margin-bottom:10px;" src="{{ $logoData }}" alt="Logo"/>
                    @endif
                    <div class="company-name">BANSAL IMMIGRATION</div>
                    <div class="company-info">
                        Level 8, 278 Collins Street<br/>
                        Melbourne VIC 3000<br/>
                        E-mail: invoice@bansalimmigration.com.au<br/>
                        Phone: 03 96021330
                    </div>
                </td>
                <td style="text-align: right; width: 50%; vertical-align: top;">
                    <h1 class="document-title">Statement of services</h1>
                    <div class="document-info">
                        <b>ABN</b> 70 958 120 428<br/>
                        @if(!empty($migration_agent_name) || !empty($migration_agent_marn))
                            <b>Migration Agent:</b>
                            @if(!empty($migration_agent_name)){{ $migration_agent_name }}@endif
                            @if(!empty($migration_agent_marn))
                                @if(!empty($migration_agent_name)) &mdash; @endif MARN {{ $migration_agent_marn }}
                            @endif
                            <br/>
                        @endif
                        <b>Statement Date:</b> {{ $statement_date }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="bill-to-section">
            <div class="bill-to-label">Client:</div>
            <div class="bill-to-content">
                <strong>{{ $client_name }}</strong><br/>
                @foreach($client_address_lines as $addressLine)
                    {{ $addressLine }}<br/>
                @endforeach
            </div>
        </div>

        <div class="matter-info">
            <strong>Matter:</strong> {{ $client_matter_display }}
        </div>

        <table class="ledger-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Invoice No</th>
                    <th>Charge</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lines as $line)
                    <tr>
                        <td>{{ $line['date'] }}</td>
                        <td>{{ $line['description'] }}</td>
                        <td>{{ $line['invoice_no'] }}</td>
                        <td>${{ number_format($line['charge'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No invoice items have been issued for this matter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="totals-section">
            Total: ${{ number_format($total, 2) }}
        </div>

        <div class="statement-note">
            This statement sets out each invoice item and the charge for that item.
        </div>
    </div>
</body>
</html>
