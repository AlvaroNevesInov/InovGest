<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovativo de Pagamento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }
        .invoice-details {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #6b7280;
        }
        .detail-value {
            color: #111827;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comprovativo de Pagamento</h1>
    </div>

    <div class="content">
        <p>Exmo(a). Senhor(a),</p>

        <p>Informamos que o pagamento da fatura referente à encomenda foi efetuado com sucesso.</p>

        <div class="invoice-details">
            <div class="detail-row">
                <span class="detail-label">Número da Fatura:</span>
                <span class="detail-value">{{ $invoice->number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Data da Fatura:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Data de Pagamento:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($invoice->payment_date)->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Valor Total:</span>
                <span class="detail-value">{{ number_format($invoice->total_amount, 2, ',', '.') }} €</span>
            </div>
            @if($invoice->supplier)
            <div class="detail-row">
                <span class="detail-label">Fornecedor:</span>
                <span class="detail-value">{{ $invoice->supplier->name }}</span>
            </div>
            @endif
        </div>

        @if($invoice->payment_proof_path)
        <p><strong>Em anexo encontra o comprovativo de pagamento.</strong></p>
        @endif

        <p>Se tiver alguma questão, não hesite em contactar-nos.</p>

        <p>Com os melhores cumprimentos,<br>
        <strong>{{ config('app.name') }}</strong></p>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
    </div>
</body>
</html>
