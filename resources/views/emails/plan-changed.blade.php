<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alteração de Plano</title>
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
            background-color: #10B981;
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
        .success-box {
            background-color: #D1FAE5;
            border-left: 4px solid #10B981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .plan-comparison {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        .plan-row {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            margin: 10px 0;
        }
        .old-plan {
            background-color: #FEE2E2;
            border-radius: 5px;
            padding: 15px;
            flex: 1;
            margin-right: 10px;
        }
        .arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            padding: 0 10px;
        }
        .new-plan {
            background-color: #D1FAE5;
            border-radius: 5px;
            padding: 15px;
            flex: 1;
            margin-left: 10px;
        }
        .plan-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .plan-price {
            font-size: 24px;
            color: #10B981;
            font-weight: bold;
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
        <h1>{{ $isUpgrade ? '🎉 Upgrade Realizado' : '📋 Alteração de Plano Agendada' }}</h1>
    </div>

    <div class="content">
        <p>Olá {{ $tenant->owner->name }},</p>

        @if($isUpgrade)
        <div class="success-box">
            <strong>O seu upgrade foi realizado com sucesso!</strong><br>
            O seu plano foi atualizado imediatamente e já pode usufruir de todas as novas funcionalidades.
        </div>
        @else
        <div class="success-box">
            <strong>A sua alteração de plano foi agendada com sucesso!</strong><br>
            A mudança para o novo plano será efetuada no próximo período de faturação.
        </div>
        @endif

        <div class="plan-comparison">
            <h3 style="text-align: center; color: #6b7280; margin-top: 0;">Alteração de Plano</h3>
            <div class="plan-row">
                <div class="old-plan">
                    <div class="plan-name">{{ $oldPlan->name }}</div>
                    <div class="plan-price">{{ number_format($oldPlan->price, 2, ',', '.') }} €</div>
                    <div style="font-size: 12px; color: #6b7280;">Plano Anterior</div>
                </div>
                <div class="arrow">→</div>
                <div class="new-plan">
                    <div class="plan-name">{{ $newPlan->name }}</div>
                    <div class="plan-price">{{ number_format($newPlan->price, 2, ',', '.') }} €</div>
                    <div style="font-size: 12px; color: #6b7280;">Novo Plano</div>
                </div>
            </div>
        </div>

        <h3>Detalhes da Alteração</h3>
        <div style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid #e5e7eb;">
            @if($isUpgrade)
            <div class="detail-row">
                <span class="detail-label">Data de Ativação:</span>
                <span class="detail-value">Imediato</span>
            </div>
            @if($subscription->prorated_amount > 0)
            <div class="detail-row">
                <span class="detail-label">Valor Pró-rata:</span>
                <span class="detail-value">{{ number_format($subscription->prorated_amount, 2, ',', '.') }} €</span>
            </div>
            @endif
            @else
            <div class="detail-row">
                <span class="detail-label">Data de Ativação:</span>
                <span class="detail-value">{{ $subscription->plan_change_scheduled_for->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Próxima Faturação:</span>
                <span class="detail-value">{{ number_format($newPlan->price, 2, ',', '.') }} €</span>
            </div>
            @endif
        </div>

        @if($isUpgrade)
        <p><strong>Novas funcionalidades disponíveis:</strong></p>
        <ul>
            @foreach($newPlan->features as $feature)
            <li>{{ ucfirst(str_replace('_', ' ', $feature)) }}</li>
            @endforeach
        </ul>
        @else
        <p><strong>Nota importante:</strong></p>
        <ul>
            <li>Continuará com o plano {{ $oldPlan->name }} até {{ $subscription->plan_change_scheduled_for->format('d/m/Y') }}</li>
            <li>A mudança para o plano {{ $newPlan->name }} será automática na data indicada</li>
            <li>Pode cancelar esta alteração a qualquer momento antes da data de ativação</li>
        </ul>
        @endif

        <p>Se tiver alguma dúvida, não hesite em contactar-nos.</p>

        <p>Com os melhores cumprimentos,<br>
        <strong>Equipa {{ config('app.name') }}</strong></p>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
    </div>
</body>
</html>
