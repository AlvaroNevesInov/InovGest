#!/bin/bash

echo "🔥 Forçando rebuild completo do frontend..."
echo ""

# 1. Limpar cache do Vite
echo "1️⃣ Limpando cache do Vite..."
rm -rf node_modules/.vite
rm -rf public/build
echo "✅ Cache limpo"
echo ""

# 2. Instalar dependências (se necessário)
if [ ! -d "node_modules" ]; then
    echo "2️⃣ Instalando dependências npm..."
    npm install
    echo "✅ Dependências instaladas"
    echo ""
else
    echo "2️⃣ Dependências já instaladas, pulando..."
    echo ""
fi

# 3. Build completo
echo "3️⃣ Compilando assets..."
npm run build

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Build concluído com sucesso!"
    echo ""
    echo "📝 Próximos passos:"
    echo "   1. Force refresh no browser:"
    echo "      - Chrome/Edge: Ctrl+Shift+Delete → Limpar cache"
    echo "      - Ou: Ctrl+Shift+R (hard reload)"
    echo "   2. Tente aceder à checklist novamente"
    echo ""
    echo "📂 Ficheiros gerados em: public/build/"
    ls -lh public/build/ | head -10
else
    echo ""
    echo "❌ Erro no build!"
    echo "Verifique os erros acima."
    exit 1
fi
