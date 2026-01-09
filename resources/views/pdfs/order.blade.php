<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomenda #{{ $order->number }}</title>
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
            border-bottom: 2px solid #16a34a;
            padding-bottom: 20px;
        }

        .company-info {
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #16a34a;
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
            color: #16a34a;
            margin-bottom: 10px;
        }

        .document-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .client-info, .order-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .order-info {
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
            color: #16a34a;
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
            background: #16a34a;
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
            border-top: 2px solid #16a34a;
            font-weight: bold;
            font-size: 14px;
            color: #16a34a;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #f9fafb;
            border-left: 4px solid #16a34a;
            border-radius: 3px;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #16a34a;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .reference-info {
            margin-top: 10px;
            padding: 10px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 3px;
            font-size: 10px;
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
            <div class="document-title">ENCOMENDA Nº {{ $order->number }}</div>
        </div>

        <div class="document-info">
            <div class="client-info">
                <div class="info-block">
                    <div class="info-title">CLIENTE</div>
                    <div class="info-line"><strong>{{ $order->entity->name }}</strong></div>
                    @if($order->entity->nif)
                        <div class="info-line">NIF: {{ $order->entity->nif }}</div>
                    @endif
                    @if($order->entity->address)
                        <div class="info-line">{{ $order->entity->address }}</div>
                    @endif
                    @if($order->entity->postal_code || $order->entity->city)
                        <div class="info-line">
                            @if($order->entity->postal_code){{ $order->entity->postal_code }} @endif
                            @if($order->entity->city){{ $order->entity->city }}@endif
                        </div>
                    @endif
                    @if($order->entity->country)
                        <div class="info-line">{{ $order->entity->country->name }}</div>
                    @endif
                </div>
            </div>

            <div class="order-info">
                <div class="info-block">
                    <div class="info-title">DADOS DA ENCOMENDA</div>
                    <div class="info-line">
                        <span class="label">Data:</span> {{ $order->order_date->format('d/m/Y') }}
                    </div>
                    @if($order->proposal_id)
                        <div class="info-line">
                            <span class="label">Proposta Origem:</span> #{{ $order->proposal->number }}
                        </div>
                    @endif
                    <div class="info-line">
                        <span class="label">Estado:</span>
                        @if($order->status === 'draft')
                            Rascunho
                        @elseif($order->status === 'closed')
                            Fechada
                        @else
                            {{ ucfirst($order->status) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($order->proposal_id)
            <div class="reference-info">
                <strong>Nota:</strong> Esta encomenda foi gerada a partir da Proposta #{{ $order->proposal->number }}
            </div>
        @endif

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
                @foreach($order->lines as $line)
                    <tr>
                        <td>{{ $line->article_reference }}</td>
                        <td>
                            <strong>{{ $line->article_name }}</strong>
                            @if($line->description)
                                <br><small style="color: #666;">{{ $line->description }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($line->quantity, 0, ',', '.') }}</td>
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
                <td class="value-col">{{ number_format($order->subtotal, 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <td class="label-col">IVA:</td>
                <td class="value-col">{{ number_format($order->tax_total, 2, ',', '.') }} €</td>
            </tr>
            <tr class="total-row">
                <td class="label-col">TOTAL:</td>
                <td class="value-col">{{ number_format($order->total, 2, ',', '.') }} €</td>
            </tr>
        </table>

        @if($order->notes)
            <div class="notes">
                <div class="notes-title">Notas:</div>
                <div>{{ $order->notes }}</div>
            </div>
        @endif

        <div class="footer">
            Documento gerado em {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
