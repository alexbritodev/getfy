# MVP - Novo sistema em PHP puro

## 1. Objetivo

Criar um sistema novo, inspirado no produto atual, mas com uma base mais simples de manter: **PHP puro**, **MySQL**, **Bootstrap 5**, **JavaScript vanilla** e uma arquitetura MVC leve.

O objetivo do MVP não é copiar todos os recursos existentes de uma vez. O foco é entregar um núcleo funcional para vender produtos digitais, processar pagamentos, liberar acesso e exibir uma área de membros organizada.

## 2. Nome temporário do projeto

**New System**

Nome usado apenas como pasta de planejamento. Antes de definir marca pública, validar disponibilidade de domínio, GitHub, redes sociais e registro de marca.

## 3. Mapa resumido do sistema atual

O sistema atual é uma plataforma Laravel + Vue com vários módulos:

- Painel administrativo com dashboard, produtos, vendas, alunos, relatórios e configurações.
- Checkout público com Pix, cartão, boleto, cupons, order bump, upsell, downsell e página de obrigado.
- Área de membros com seções, módulos, aulas, progresso, comentários, comunidade, certificado, gamificação e PWA.
- Produtos digitais com imagem, descrição, preço, moeda, planos, ofertas, links de entrega e área de membros.
- Gestão de alunos, acesso manual, importação CSV e envio de e-mail de acesso.
- Gateways e webhooks para processar pagamentos e atualizar pedidos.
- Integrações externas, API de pagamentos, plugins, notificações push e e-mail marketing.
- Equipe, permissões, logs, comprovantes de entrega e rotinas automáticas.

## 4. Princípio do novo sistema

O novo sistema deve ser menor, previsível e fácil de operar em hospedagem compartilhada.

Decisões do MVP:

- Sem Laravel.
- Sem Vue/Inertia.
- Sem build obrigatório de frontend.
- Sem fila obrigatória no MVP.
- Sem Redis no MVP.
- Sem plugin marketplace no MVP.
- Sem PWA no MVP inicial.
- Sem múltiplos gateways completos no primeiro ciclo.
- Sem community/gamificação/certificado no primeiro ciclo.

## 5. Stack técnica do MVP

### Backend

- PHP 8.2+ puro.
- MySQL/MariaDB.
- PDO para banco de dados.
- Sessions nativas com cookies seguros.
- Composer opcional apenas para dependências realmente úteis.
- Estrutura MVC leve criada no próprio projeto.

### Frontend

- Bootstrap 5.
- Bootstrap Icons ou Lucide via CDN, se necessário.
- JavaScript vanilla.
- Editor de texto: TinyMCE, Trix ou Quill, escolhido posteriormente.
- Sem Vite obrigatório.

### Servidor

- Apache/LiteSpeed com `.htaccess`.
- Compatível com hospedagem compartilhada.
- Opcionalmente compatível com VPS/Docker no futuro.

## 6. Arquitetura proposta

```text
/public
  index.php
  assets/
  uploads/

/app
  Controllers/
  Models/
  Services/
  Middlewares/
  Views/
  Helpers/

/config
  app.php
  database.php
  gateways.php

/database
  schema.sql
  seed.sql

/routes
  web.php
  api.php

/storage
  logs/
  cache/
  private/
```

## 7. Núcleo obrigatório do MVP

### 7.1 Autenticação e usuários

Objetivo: permitir login seguro no painel e login de aluno na área de membros.

Recursos:

- Primeiro administrador.
- Login e logout.
- Recuperação de senha simples.
- Perfis: `admin`, `producer`, `student`.
- Proteção por sessão.
- Middleware simples de autenticação e permissão.

Tabelas:

- `users`
- `password_resets`
- `user_product_access`

### 7.2 Produtos

Objetivo: cadastrar produtos digitais vendáveis.

Campos mínimos:

- Nome.
- Slug.
- Descrição.
- Imagem quadrada do produto.
- Tipo: produto digital, área de membros ou link externo.
- Preço.
- Moeda, inicialmente BRL.
- Status ativo/inativo.
- Link de entrega opcional.

Tabelas:

- `products`

### 7.3 Checkout público

Objetivo: vender um produto por link público.

Recursos mínimos:

- URL pública: `/c/{slug}`.
- Resumo do produto com imagem, nome, descrição e preço.
- Formulário de comprador: nome, e-mail, telefone e CPF opcional.
- Cupom simples.
- Pagamento Pix no primeiro ciclo.
- Página Pix aguardando pagamento.
- Página de obrigado.
- Registro de pedido.

Tabelas:

- `orders`
- `order_items`
- `checkout_sessions`
- `coupons`

### 7.4 Pagamentos

Objetivo: ter uma camada simples para gateway.

MVP recomendado:

- Começar com **Pix manual/simulado** ou **um gateway Pix real**.
- Abstrair gateway em serviço para trocar depois.
- Webhook para confirmação de pagamento.
- Aprovação manual no painel.

Serviços:

- `PaymentService`
- `PixGatewayInterface`
- `WebhookService`

Tabelas:

- `gateway_credentials`
- `payment_webhooks`

### 7.5 Liberação de acesso

Objetivo: ao pagamento aprovado, liberar produto ao aluno.

Recursos:

- Criar aluno automaticamente se e-mail não existir.
- Associar produto ao aluno.
- Enviar ou exibir link de acesso.
- Login sem senha via token pode entrar na fase 2; no MVP, login com senha é suficiente.

Tabelas:

- `user_product_access`
- `access_tokens`, se usar link mágico.

### 7.6 Área de membros

Objetivo: entregar conteúdo comprado.

MVP mínimo:

- Página inicial da área de membros.
- Listagem de produtos liberados.
- Página de um produto/curso.
- Seções.
- Módulos.
- Aulas.
- Progresso de aula concluída.

Tipos de aula no MVP:

- Texto rico seguro.
- Vídeo por URL/embed.
- Arquivo para download.

Tabelas:

- `member_sections`
- `member_modules`
- `member_lessons`
- `member_lesson_progress`

### 7.7 Builder simples da área de membros

Objetivo: o admin montar o curso sem depender de Vue.

Recursos:

- CRUD de seções.
- CRUD de módulos.
- CRUD de aulas.
- Ordenação simples por posição numérica.
- Campos de âncora opcionais para seção/módulo.
- Upload de capa/banner do módulo.
- Preview simples em uma aba ou painel lateral.

Interface:

- Bootstrap cards.
- Modais para criar/editar.
- Requisições POST tradicionais.
- Ajax apenas onde melhorar muito a experiência.

### 7.8 Vendas

Objetivo: o produtor acompanhar pedidos.

Recursos:

- Lista de vendas.
- Filtros por status, produto, período e busca.
- Detalhe básico do pedido.
- Aprovação manual.
- Reenvio de acesso, se houver e-mail configurado.
- Exportação CSV simples.

Tabelas:

- `orders`
- `order_items`

### 7.9 Alunos

Objetivo: gerenciar acesso manual.

Recursos:

- Listar alunos.
- Criar aluno.
- Editar nome/e-mail/senha.
- Vincular produto.
- Remover acesso.
- Importação CSV pode ficar para fase 2.

### 7.10 Configurações

Objetivo: manter o sistema instalável e configurável.

Recursos:

- Configurações gerais do sistema.
- Dados de e-mail SMTP.
- Dados do gateway Pix.
- Logo/nome do painel.
- URL base do sistema.

Tabelas:

- `settings`

## 8. Fora do MVP

Não implementar no primeiro ciclo:

- API pública de pagamentos.
- Marketplace de plugins.
- PWA completo.
- Notificações push.
- Gamificação.
- Comunidade interna.
- Certificados.
- Múltiplos gateways simultâneos.
- Order bump, upsell e downsell.
- E-mail marketing completo.
- Relatórios avançados.
- Equipe com permissões granulares.
- Comprovante de entrega em PDF.
- Assinaturas recorrentes.
- Integrações Cademi, Utmify, Spedy e similares.

## 9. Banco de dados mínimo

### `users`

- `id`
- `name`
- `email`
- `password_hash`
- `role`
- `status`
- `created_at`
- `updated_at`

### `products`

- `id`
- `name`
- `slug`
- `description`
- `image_path`
- `type`
- `price`
- `currency`
- `deliverable_url`
- `is_active`
- `created_at`
- `updated_at`

### `orders`

- `id`
- `product_id`
- `user_id`
- `buyer_name`
- `buyer_email`
- `buyer_phone`
- `buyer_document`
- `amount`
- `currency`
- `payment_method`
- `payment_gateway`
- `gateway_reference`
- `status`
- `paid_at`
- `created_at`
- `updated_at`

### `member_sections`

- `id`
- `product_id`
- `title`
- `anchor`
- `position`
- `created_at`
- `updated_at`

### `member_modules`

- `id`
- `section_id`
- `title`
- `description`
- `anchor`
- `cover_path`
- `position`
- `created_at`
- `updated_at`

### `member_lessons`

- `id`
- `module_id`
- `title`
- `type`
- `content_html`
- `content_url`
- `file_path`
- `position`
- `is_free_preview`
- `created_at`
- `updated_at`

### `member_lesson_progress`

- `id`
- `user_id`
- `lesson_id`
- `completed_at`
- `created_at`

### `settings`

- `id`
- `key`
- `value`
- `created_at`
- `updated_at`

## 10. Rotas mínimas

### Públicas

- `GET /`
- `GET /login`
- `POST /login`
- `POST /logout`
- `GET /c/{slug}`
- `POST /checkout`
- `GET /checkout/pix/{order}`
- `GET /checkout/obrigado/{order}`
- `POST /webhook/payment/{gateway}`

### Painel

- `GET /admin`
- `GET /admin/products`
- `GET /admin/products/create`
- `POST /admin/products`
- `GET /admin/products/{id}/edit`
- `POST /admin/products/{id}`
- `GET /admin/products/{id}/members`
- `POST /admin/products/{id}/sections`
- `POST /admin/products/{id}/modules`
- `POST /admin/products/{id}/lessons`
- `GET /admin/orders`
- `POST /admin/orders/{id}/approve`
- `GET /admin/students`
- `GET /admin/settings`

### Área de membros

- `GET /members`
- `GET /members/product/{slug}`
- `GET /members/module/{id}`
- `GET /members/lesson/{id}`
- `POST /members/lesson/{id}/complete`

## 11. Segurança mínima obrigatória

- Senhas com `password_hash()` usando `PASSWORD_DEFAULT`.
- Login com proteção contra brute force simples.
- CSRF token em todos os formulários POST.
- Validação server-side em todos os inputs.
- Escape de saída com `htmlspecialchars()`.
- Sanitização de HTML rico com whitelist de tags permitidas.
- Upload com validação de MIME, extensão e tamanho.
- Arquivos privados fora de `/public`, quando necessário.
- Webhooks com assinatura ou token secreto.
- Sessão com `httponly`, `secure` em HTTPS e `samesite=Lax`.

## 12. Critério de pronto do MVP

O MVP estará pronto quando for possível:

1. Instalar o sistema em uma hospedagem com PHP 8.2 e MySQL.
2. Criar um administrador.
3. Cadastrar produto com imagem.
4. Criar checkout público para o produto.
5. Gerar pedido.
6. Aprovar pedido manualmente ou por webhook Pix.
7. Criar/liberar acesso ao aluno.
8. Montar área de membros com seção, módulo e aula.
9. Aluno acessar e concluir aula.
10. Admin visualizar venda e aluno no painel.

## 13. Diretriz principal

Se uma funcionalidade exigir muita infraestrutura, dependências pesadas ou reatividade complexa, ela fica fora do MVP.

A prioridade é: **funcionar bem, ser fácil de instalar, fácil de debugar e fácil de evoluir**.
