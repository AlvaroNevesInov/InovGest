#!/bin/bash

# Script para preparar ambiente para demonstração em vídeo
# Execute antes de gravar o vídeo para ter um ambiente limpo

echo "🎬 Preparando ambiente para demonstração..."
echo ""

# 1. Limpar cache
echo "1️⃣ Limpando cache..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo "✅ Cache limpo"
echo ""

# 2. Resetar base de dados (CUIDADO: apaga todos os dados!)
echo "2️⃣ Resetando base de dados..."
read -p "⚠️  ATENÇÃO: Isto vai apagar TODOS os dados. Continuar? (s/N): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Ss]$ ]]
then
    php artisan migrate:fresh
    echo "✅ Base de dados resetada"
else
    echo "❌ Operação cancelada"
    exit 1
fi
echo ""

# 3. Criar planos
echo "3️⃣ Criando planos de exemplo..."
php artisan db:seed --class=PlanSeeder
echo "✅ Planos criados:"
echo "   - Free (€0/mês)"
echo "   - Pro (€29/mês)"
echo "   - Pro Anual (€278/ano)"
echo "   - Enterprise (€99/mês)"
echo "   - Enterprise Anual (€950/ano)"
echo ""

# 4. Criar outros dados necessários (se existirem seeders)
echo "4️⃣ Criando dados adicionais..."

# Verificar e executar seeders se existirem
if php artisan db:seed --class=CountrySeeder 2>/dev/null; then
    echo "✅ Países criados"
fi

if php artisan db:seed --class=VatRateSeeder 2>/dev/null; then
    echo "✅ Taxas de IVA criadas"
fi

if php artisan db:seed --class=ContactFunctionSeeder 2>/dev/null; then
    echo "✅ Funções de contacto criadas"
fi

echo ""

# 5. Limpar storage de uploads
echo "5️⃣ Limpando uploads antigos..."
if [ -d "storage/app/public/logos" ]; then
    rm -rf storage/app/public/logos/*
    echo "✅ Logos removidos"
fi
if [ -d "storage/app/public/uploads" ]; then
    rm -rf storage/app/public/uploads/*
    echo "✅ Uploads removidos"
fi
echo ""

# 6. Criar link simbólico de storage (se não existir)
echo "6️⃣ Verificando storage link..."
php artisan storage:link 2>/dev/null
echo "✅ Storage link verificado"
echo ""

# 7. Verificar se aplicação está funcional
echo "7️⃣ Verificando aplicação..."

# Testar ligação à BD
if php artisan migrate:status > /dev/null 2>&1; then
    echo "✅ Base de dados acessível"
else
    echo "❌ Erro ao aceder à base de dados!"
    exit 1
fi

# Contar planos
PLAN_COUNT=$(php artisan tinker --execute="echo \App\Models\Plan::count();")
echo "✅ Planos na BD: $PLAN_COUNT"

echo ""
echo "🎉 Ambiente preparado com sucesso!"
echo ""
echo "📝 Próximos passos:"
echo "   1. Iniciar servidor: php artisan serve"
echo "   2. Abrir browser em: http://localhost:8000"
echo "   3. Registar novo utilizador para começar demo"
echo "   4. Seguir guia: GUIA_APRESENTACAO_VIDEO.md"
echo ""
echo "💡 Dados de exemplo sugeridos:"
echo "   Utilizador: joao.santos@example.com"
echo "   Tenant 1: InovSolutions Lda"
echo "   Tenant 2: TechStart Unipessoal"
echo "   Convidados: maria.silva@example.com, joao.costa@example.com"
echo ""
echo "🎬 Boa sorte com a gravação!"
