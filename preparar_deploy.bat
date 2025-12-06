@echo off
echo ========================================
echo   AgendaPro - Preparar Deploy cPanel
echo   Autor: Rafael Dias - doisr.com.br
echo ========================================
echo.

REM Criar pasta de deploy
if not exist "deploy" mkdir deploy

echo [1/4] Exportando banco de dados...
echo.
echo ATENCAO: Abra o phpMyAdmin e exporte o banco manualmente
echo Salve o arquivo como: deploy\agendapro_database.sql
echo.
pause

echo.
echo [2/4] Criando arquivo ZIP do projeto...
echo.

REM Criar arquivo ZIP (requer PowerShell)
powershell -Command "Compress-Archive -Path * -DestinationPath deploy\agendapro.zip -Force -CompressionLevel Optimal"

if %ERRORLEVEL% EQU 0 (
    echo ✓ Arquivo ZIP criado com sucesso!
) else (
    echo ✗ Erro ao criar ZIP. Crie manualmente.
)

echo.
echo [3/4] Criando arquivo de configuracao...
echo.

REM Criar arquivo de configuração de exemplo
(
echo # Configuracoes para Deploy - cPanel
echo.
echo ## Banco de Dados
echo HOSTNAME=localhost
echo USERNAME=usuario_agendapro_user
echo PASSWORD=SENHA_AQUI
echo DATABASE=usuario_agendapro
echo.
echo ## URL Base
echo BASE_URL=https://iafila.doisr.com.br/
echo.
echo ## Mercado Pago
echo MP_ACCESS_TOKEN=APP_USR-...
echo MP_PUBLIC_KEY=APP_USR-...
echo MP_SANDBOX=0
echo.
echo ## Webhook
echo WEBHOOK_URL=https://iafila.doisr.com.br/webhook/mercadopago
) > deploy\config_producao.txt

echo ✓ Arquivo de configuracao criado!

echo.
echo [4/4] Gerando checklist...
echo.

REM Criar checklist
(
echo # Checklist de Deploy - AgendaPro
echo.
echo ## Pre-Deploy
echo [ ] Banco de dados exportado
echo [ ] Arquivo ZIP criado
echo [ ] Credenciais de producao do Mercado Pago obtidas
echo.
echo ## Deploy
echo [ ] Subdominio criado no cPanel
echo [ ] SSL ativado
echo [ ] Banco de dados criado no cPanel
echo [ ] Usuario do banco criado e vinculado
echo [ ] Arquivos enviados via FTP/Gerenciador
echo [ ] Arquivos extraidos
echo.
echo ## Configuracao
echo [ ] database.php configurado
echo [ ] config.php - base_url configurada
echo [ ] config.php - environment = production
echo [ ] Permissoes de pastas configuradas
echo [ ] Banco de dados importado
echo.
echo ## Testes
echo [ ] Site acessivel via HTTPS
echo [ ] Login funcionando
echo [ ] CRUD de estabelecimentos OK
echo [ ] CRUD de profissionais OK
echo [ ] CRUD de servicos OK
echo [ ] CRUD de clientes OK
echo [ ] CRUD de agendamentos OK
echo [ ] Disponibilidade OK
echo [ ] Bloqueios OK
echo.
echo ## Mercado Pago
echo [ ] Credenciais de producao configuradas
echo [ ] Webhook configurado no painel MP
echo [ ] Teste de pagamento PIX realizado
echo.
echo ## Pos-Deploy
echo [ ] Backup do banco configurado
echo [ ] Logs de erro verificados
echo [ ] Performance testada
echo [ ] Documentacao atualizada
) > deploy\checklist.md

echo ✓ Checklist criado!

echo.
echo ========================================
echo   Arquivos prontos em: deploy\
echo ========================================
echo.
echo Arquivos criados:
echo   - agendapro.zip
echo   - agendapro_database.sql (manual)
echo   - config_producao.txt
echo   - checklist.md
echo.
echo Proximo passo:
echo   1. Leia o arquivo: docs\DEPLOY_CPANEL.md
echo   2. Siga as instrucoes passo a passo
echo   3. Use o checklist.md para acompanhar
echo.
pause
