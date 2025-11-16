# 🚀 Comandos para Subir no Git

## Passo 1: Inicializar o repositório local

```bash
cd c:\xampp\htdocs\projeto_base
git init
```

## Passo 2: Adicionar todos os arquivos

```bash
git add .
```

## Passo 3: Fazer o primeiro commit

```bash
git commit -m "🎉 Initial commit - Projeto Base Dashboard Administrativo v1.0.0"
```

## Passo 4: Adicionar o repositório remoto

```bash
git remote add origin https://github.com/doisrsis/projeto_base.git
```

## Passo 5: Verificar a branch

```bash
git branch -M main
```

## Passo 6: Enviar para o GitHub

```bash
git push -u origin main
```

---

## ⚠️ Se der erro de autenticação:

### Opção 1: Usar Personal Access Token (Recomendado)

1. Acesse: https://github.com/settings/tokens
2. Clique em "Generate new token (classic)"
3. Marque: `repo` (acesso completo)
4. Gere o token e copie
5. Use o token como senha quando pedir

### Opção 2: Configurar SSH

```bash
# Gerar chave SSH
ssh-keygen -t ed25519 -C "seu-email@exemplo.com"

# Adicionar ao ssh-agent
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519

# Copiar chave pública
cat ~/.ssh/id_ed25519.pub

# Adicionar no GitHub: Settings → SSH Keys → New SSH Key
```

Depois use:
```bash
git remote set-url origin git@github.com:doisrsis/projeto_base.git
git push -u origin main
```

---

## 📝 Commits Futuros

Após fazer alterações:

```bash
git add .
git commit -m "Descrição das alterações"
git push
```

---

## 🏷️ Criar Tags de Versão

```bash
# Criar tag
git tag -a v1.0.0 -m "Versão 1.0.0 - Release inicial"

# Enviar tag
git push origin v1.0.0

# Enviar todas as tags
git push --tags
```

---

## 🔄 Atualizar do GitHub

```bash
git pull origin main
```

---

## 📊 Ver status

```bash
git status
git log --oneline
```
