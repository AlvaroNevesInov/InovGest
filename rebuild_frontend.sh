#!/bin/bash

echo "🔨 Reconstruindo frontend..."
echo ""

# 1. Verificar se node_modules existe
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependências npm..."
    npm install
    echo "✅ Dependências instaladas"
    echo ""
fi

# 2. Fazer build do frontend
echo "🏗️  Compilando Vue/Vite..."
npm run build

echo ""
echo "✅ Frontend compilado com sucesso!"
echo ""
echo "📝 Próximos passos:"
echo "   1. Recarregue a página no browser (Ctrl+Shift+R ou Cmd+Shift+R)"
echo "   2. Tente aceder à checklist novamente"
echo ""
echo "💡 Alternativa: Use 'npm run dev' para compilação automática durante desenvolvimento"
