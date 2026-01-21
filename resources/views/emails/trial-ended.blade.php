<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Período de Teste Terminado</title>
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
            background-color: #EF4444;
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
            background-color: #FEE2E2;
            border-left: 4px solid #EF4444;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .cta-button {
            display: inline-block;
            background-color: #EF4444;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }
        .features-list {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
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
        <h1>⚠️ Período de Teste Terminado</h1>
    </div>

    <div class="content">
        <p>Olá {{ $tenant->owner->name }},</p>

        <div class="alert-box">
            <strong>O seu período de teste terminou.</strong><br>
            Para continuar a usar o {{ config('app.name') }}, escolha um dos nossos planos.
        </div>

        <p>Obrigado por experimentar o {{ config('app.name') }}! O seu período de teste do plano <strong>{{ $plan->name }}</strong> terminou.</p>

        <p><strong>Não perca o acesso às funcionalidades que ama:</strong></p>

        <div class="features-list">
            <ul>
                <li>✓ Gestão completa de clientes e fornecedores</li>
                <li>✓ Faturação e propostas profissionais</li>
                <li>✓ Calendário de eventos e tarefas</li>
                <li>✓ Ordens de trabalho e acompanhamento</li>
                <li>✓ Relatórios avançados</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/plans') }}" class="cta-button">Escolher um Plano Agora</a>
        </div>

        <p><strong>O que acontece agora?</strong></p>
        <ul>
            <li>O seu acesso está limitado até escolher um plano</li>
            <li>Os seus dados estão seguros e não serão apagados</li>
            <li>Pode reativar a qualquer momento</li>
            <li>Oferecemos planos a partir de apenas 29€/mês</li>
        </ul>

        <p>Tem dúvidas? A nossa equipa está pronta para ajudar!</p>

        <p>Com os melhores cumprimentos,<br>
        <strong>Equipa {{ config('app.name') }}</strong></p>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
    </div>
</body>
</html>
