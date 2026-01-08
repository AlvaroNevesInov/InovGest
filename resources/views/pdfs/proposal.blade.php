<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta #{{ $proposal->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }

        .company-info {
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 10px;
            color: #666;
        }

        .document-title {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .document-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .client-info, .proposal-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .proposal-info {
            text-align: right;
        }

        .info-block {
            background: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .info-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            color: #2563eb;
        }

        .info-line {
            margin-bottom: 3px;
        }

        .label {
            font-weight: bold;
            color: #666;
        }

        .lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .lines-table thead {
            background: #2563eb;
            color: white;
        }

        .lines-table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        .lines-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .lines-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-table {
            margin-left: auto;
            margin-top: 20px;
            width: 300px;
        }

        .totals-table tr td {
            padding: 5px 10px;
        }

        .totals-table .label-col {
            text-align: right;
            font-weight: bold;
            color: #666;
        }

        .totals-table .value-col {
            text-align: right;
            width: 120px;
        }

        .total-row {
            border-top: 2px solid #2563eb;
            font-weight: bold;
            font-size: 14px;
            color: #2563eb;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #f9fafb;
            border-left: 4px solid #2563eb;
            border-radius: 3px;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #2563eb;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-info">
                @if($company)
                    <div class="company-name">{{ $company->name }}</div>
                    <div class="company-details">
                        @if($company->nif)NIF: {{ $company->nif }} | @endif
                        @if($company->address){{ $company->address }}, @endif
                        @if($company->postal_code){{ $company->postal_code }} @endif
                        @if($company->city){{ $company->city }}@endif
                        @if($company->country), {{ $company->country->name }}@endif
                        <br>
                        @if($company->phone)Tel: {{ $company->phone }} | @endif
                        @if($company->email)Email: {{ $company->email }} | @endif
                        @if($company->website){{ $company->website }}@endif
                    </div>
                @endif
            </div>
            <div class="document-title">PROPOSTA Nº {{ $proposal->number }}</div>
        </div>

        <div class="document-info">
            <div class="client-info">
                <div class="info-block">
                    <div class="info-title">CLIENTE</div>
                    <div class="info-line"><strong>{{ $proposal->entity->name }}</strong></div>
                    @if($proposal->entity->nif)
                        <div class="info-line">NIF: {{ $proposal->entity->nif }}</div>
                    @endif
                    @if($proposal->entity->address)
                        <div class="info-line">{{ $proposal->entity->address }}</div>
                    @endif
                    @if($proposal->entity->postal_code || $proposal->entity->city)
                        <div class="info-line">
                            @if($proposal->entity->postal_code){{ $proposal->entity->postal_code }} @endif
                            @if($proposal->entity->city){{ $proposal->entity->city }}@endif
                        </div>
                    @endif
                    @if($proposal->entity->country)
                        <div class="info-line">{{ $proposal->entity->country->name }}</div>
                    @endif
                </div>
            </div>

            <div class="proposal-info">
                <div class="info-block">
                    <div class="info-title">DADOS DA PROPOSTA</div>
                    <div class="info-line">
                        <span class="label">Data:</span> {{ $proposal->proposal_date->format('d/m/Y') }}
                    </div>
                    <div class="info-line">
                        <span class="label">Validade:</span> {{ $proposal->validity_date->format('d/m/Y') }}
                    </div>
                    <div class="info-line">
                        <span class="label">Estado:</span>
                        @if($proposal->status === 'draft')
                            Rascunho
                        @elseif($proposal->status === 'closed')
                            Fechada
                        @else
                            {{ ucfirst($proposal->status) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <table class="lines-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Referência</th>
                    <th style="width: 30%;">Artigo</th>
                    <th class="text-center" style="width: 10%;">Qtd.</th>
                    <th class="text-right" style="width: 12%;">Preço Unit.</th>
                    <th class="text-right" style="width: 10%;">Desc.</th>
                    <th class="text-center" style="width: 8%;">IVA</th>
                    <th class="text-right" style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proposal->lines as $line)
                    <tr>
                        <td>{{ $line->article_reference }}</td>
                        <td>
                            <strong>{{ $line->article_name }}</strong>
                            @if($line->description)
                                <br><small style="color: #666;">{{ $line->description }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($line->quantity, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($line->unit_price, 2, ',', '.') }} €</td>
                        <td class="text-right">
                            @if($line->discount > 0)
                                {{ number_format($line->discount, 2, ',', '.') }} €
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($line->vat_rate, 0) }}%</td>
                        <td class="text-right">{{ number_format($line->total, 2, ',', '.') }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td class="label-col">Subtotal:</td>
                <td class="value-col">{{ number_format($proposal->subtotal, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <td class="label-col">IVA:</td>
                <td class="value-col">{{ number_format($proposal->tax_total, 2, ',', '.') }} €</td>
            </tr>
            <tr class="total-row">
                <td class="label-col">TOTAL:</td>
                <td class="value-col">{{ number_format($proposal->total, 2, ',', '.') }} €</td>
            </tr>
        </table>

        @if($proposal->notes)
            <div class="notes">
                <div class="notes-title">Notas:</div>
                <div>{{ $proposal->notes }}</div>
            </div>
        @endif

        <div class="footer">
            Documento gerado em {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
