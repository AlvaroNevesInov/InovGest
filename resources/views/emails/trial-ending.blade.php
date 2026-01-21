<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Período de Teste a Terminar</title>
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
        .alert-box {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .plan-details {
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
        .cta-button {
            display: inline-block;
            background-color: #4F46E5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
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
        <h1>⏰ Período de Teste a Terminar</h1>
    </div>

    <div class="content">
        <p>Olá {{ $tenant->owner->name }},</p>

        <div class="alert-box">
            <strong>O seu período de teste termina em {{ $daysRemaining }} {{ $daysRemaining == 1 ? 'dia' : 'dias' }}!</strong>
        </div>

        <p>Esperamos que esteja a aproveitar a sua experiência com o {{ config('app.name') }}!</p>

        <p>O seu período de teste do plano <strong>{{ $plan->name }}</strong> termina em breve. Para continuar a usar todas as funcionalidades sem interrupções, escolha um plano que melhor se adequa às suas necessidades.</p>

        <div class="plan-details">
            <div class="detail-row">
                <span class="detail-label">Plano Atual:</span>
                <span class="detail-value">{{ $plan->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Fim do Teste:</span>
                <span class="detail-value">{{ $subscription->trial_ends_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dias Restantes:</span>
                <span class="detail-value">{{ $daysRemaining }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/plans') }}" class="cta-button">Ver Planos Disponíveis</a>
        </div>

        <p><strong>O que acontece depois do período de teste?</strong></p>
        <ul>
            <li>Se não escolher um plano, o acesso será limitado</li>
            <li>Os seus dados serão mantidos em segurança</li>
            <li>Pode reativar a qualquer momento escolhendo um plano</li>
        </ul>

        <p>Se tiver alguma dúvida ou precisar de ajuda, não hesite em contactar-nos.</p>

        <p>Com os melhores cumprimentos,<br>
        <strong>Equipa {{ config('app.name') }}</strong></p>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
    </div>
</body>
</html>
