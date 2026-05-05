# 🚀 Getfy Open Source

Plataforma open source em **Laravel + Vue** para checkout, produtos digitais, área de membros, pagamentos, automações e gestão de vendas.

O Getfy foi pensado para infoprodutores, desenvolvedores e equipes que precisam de uma base extensível para vender produtos digitais, entregar conteúdo e administrar a jornada do cliente em um único sistema.

---

## ✨ Principais recursos

- 🛒 Checkout para produtos digitais e assinaturas.
- 🎓 Área de membros com módulos, aulas e progresso do aluno.
- 💳 Integrações de pagamento e configuração de gateways.
- 📧 Recursos de e-mail, recuperação de carrinho e entrega de acesso.
- 📊 Gestão de produtos, vendas, clientes e configurações do sistema.
- 🧩 Estrutura extensível para melhorias, customizações e contribuições.

---

## ⚡ Instalação rápida

Escolha o modo mais adequado para o seu ambiente:

- 🌐 **Hospedagem compartilhada:** acesse `https://SEU_DOMINIO/install`
- 🖥️ **VPS com Docker:**

```bash
bash -c "$(curl -fsSL https://raw.githubusercontent.com/getfy-opensource/getfy/main/install.sh)"
```

- 🐳 **Docker/Hostinger:** use o arquivo `docker-compose.yml` do repositório.

---

## ✅ Requisitos

### 🌐 Hospedagem compartilhada

- PHP 8.2 ou superior.
- MySQL/MariaDB, recomendado MySQL 8+.
- Extensões PHP comuns do Laravel: `pdo_mysql`, `mbstring`, `openssl`, `ctype`, `json`, `tokenizer`, `xml`, `bcmath`, `intl`, `zip`.
- Permissão de escrita em `storage/` e `bootstrap/cache/`.
- `.htaccess` habilitado em Apache/LiteSpeed ou regras equivalentes em Nginx.

> Observação: o instalador tenta rodar Composer e build do frontend automaticamente. Em hospedagens onde isso for bloqueado, ele tenta seguir usando `vendor/` e `public/build`, se esses diretórios já estiverem presentes no upload.

### 🖥️ VPS

- Ubuntu/Debian com `apt-get`.
- Acesso SSH com usuário `root` ou usuário com `sudo`.
- Docker, instalado automaticamente pelo instalador quando necessário.

---

## 🌐 Instalação em hospedagem compartilhada

Este modo é recomendado quando você não tem acesso a SSH/terminal.

### 1. Criar banco de dados

1. No painel da hospedagem, crie um banco MySQL e um usuário.
2. Anote host, porta, nome do banco, usuário e senha.

### 2. Enviar os arquivos

1. No GitHub, clique em **Code > Download ZIP**.
2. Extraia o arquivo no computador.
3. Envie todos os arquivos para o servidor, normalmente em `public_html/`.

Se o painel permitir extrair ZIP no servidor, envie o `.zip` e use a opção de extração para evitar uploads demorados.

### 3. Ajustar permissões

Garanta permissão de escrita para:

- `storage/`
- `bootstrap/cache/`

Use `775` quando possível. Use `777` apenas se a hospedagem exigir e, preferencialmente, somente durante a instalação.

### 4. Rodar o instalador

Abra no navegador:

```text
https://SEU_DOMINIO/install
```

Informe a URL do sistema, os dados do banco e o `Session driver`. Em hospedagem compartilhada, `file` costuma ser a opção mais compatível.

Ao finalizar, o instalador define `APP_INSTALLED=true` no `.env` e desativa a rota pública de instalação.

### 5. Criar o primeiro administrador

Depois da instalação, acesse:

```text
https://SEU_DOMINIO/criar-admin
```

Se já existir um administrador, essa tela redireciona para o login.

### 6. Configurar cron

Para filas e rotinas automáticas, configure uma chamada a cada minuto:

```text
https://SEU_DOMINIO/cron?token=SEU_CRON_SECRET
```

O token fica no arquivo `.env`, na variável `CRON_SECRET`.

---

## 🖥️ Instalação em VPS com Docker

Este modo sobe a aplicação com Docker, banco, Redis e scheduler.

### 1. Rodar o instalador

Conecte via SSH e execute:

```bash
bash -c "$(curl -fsSL https://raw.githubusercontent.com/getfy-opensource/getfy/main/install.sh)"
```

O instalador:

1. Instala dependências básicas e Docker, se necessário.
2. Clona o repositório em `/opt/getfy`, por padrão.
3. Sobe os containers com `docker compose`.

### 2. Finalizar configuração no navegador

Ao final, o terminal mostra uma URL parecida com:

```text
http://SEU_IP/docker-setup
```

Abra essa URL, informe o domínio público e finalize a configuração inicial.

### 3. Criar administrador

```text
https://SEU_DOMINIO/criar-admin
```

### 4. Personalizar porta ou diretório

Opcionalmente, defina variáveis antes de instalar:

```bash
GETFY_HTTP_PORT=8080 GETFY_DIR=/opt/getfy bash -c "$(curl -fsSL https://raw.githubusercontent.com/getfy-opensource/getfy/main/install.sh)"
```

---

## 🐳 Docker na Hostinger ou painel com Git

Use este modo quando o painel permite criar uma aplicação a partir de um repositório Git.

1. Crie uma nova aplicação a partir da URL do repositório.
2. Prefira `docker-compose.yml` quando o painel suportar Docker Compose.
3. Garanta exposição da porta 80 ou da porta exigida pelo painel.
4. Garanta persistência para `storage/` e `.docker/`.

Depois do deploy, acesse:

```text
https://SEU_DOMINIO/docker-setup
```

Em seguida, crie o primeiro administrador:

```text
https://SEU_DOMINIO/criar-admin
```

> Atenção: se o painel não oferecer volumes persistentes, uploads, logs e estado de configuração podem ser perdidos em um redeploy.

---

## 🔄 Atualização

### VPS

Conecte via SSH e execute:

```bash
bash -c "$(curl -fsSL https://raw.githubusercontent.com/getfy-opensource/getfy/main/update.sh)"
```

### Hospedagem compartilhada

1. Baixe o ZIP de atualização da versão desejada.
2. Extraia o conteúdo na pasta do projeto.
3. No painel do sistema, acesse **Configurações > Update > Rodar migration**.

---

## 🧯 Solução de problemas

- **Erro 500 ao abrir o site:** verifique PHP 8.2+, permissões de `storage/` e `bootstrap/cache/`, e se o `.env` existe.
- **Instalador falha no Composer:** algumas hospedagens bloqueiam `proc_open`. Nesse caso, envie o projeto já com `vendor/` gerado localmente por `composer install`.
- **Arquivos em `public/storage` não aparecem:** o `storage:link` pode falhar em hospedagens sem suporte a symlink. Crie manualmente o link de `public/storage` para `storage/app/public` ou use um painel que suporte symlink.
- **Rotinas automáticas não rodam:** configure o cron usando `/cron?token=...` a cada minuto.
- **Build frontend não atualiza:** confirme se `public/build` foi gerado corretamente ou rode `npm install && npm run build` em ambiente compatível.

---

## 🤝 Contribuições

Contribuições são bem-vindas. Antes de enviar alterações, leia o arquivo [`CONTRIBUTING.md`](CONTRIBUTING.md).

Para vulnerabilidades ou problemas sensíveis de segurança, leia [`SECURITY.md`](SECURITY.md) antes de abrir uma issue pública.

---

## 💚 Apoie o desenvolvimento

Se você deseja apoiar o desenvolvimento diretamente:

| Pix | Chave |
|---|---|
| Aleatória | `ce05f7d1-27db-4d46-bca5-0a80c621349a` |

---

## 🔗 Links oficiais

**Perfil oficial do sistema Getfy:**

https://github.com/getfy-opensource

**Autor/Desenvolvedor:**

https://github.com/LeonardoIsrael0516

**Contribuidor/Desenvolvedor:**

https://github.com/alexbritodev
