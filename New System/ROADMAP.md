# Roadmap - Novo sistema em PHP puro

## 1. Visão geral

Este roadmap organiza o desenvolvimento de um sistema semelhante ao atual, mas reconstruído com uma base mais simples: PHP puro, MySQL, Bootstrap e JavaScript vanilla.

A estratégia é construir por camadas, sempre mantendo o sistema utilizável ao final de cada fase.

## 2. Princípios de execução

- Entregar primeiro o núcleo de venda e entrega de conteúdo.
- Evitar dependências pesadas antes da validação do MVP.
- Priorizar hospedagem compartilhada e instalação simples.
- Criar código claro, modular e fácil de debugar.
- Manter segurança básica desde o primeiro commit.
- Não copiar complexidade antes de existir necessidade real.

## 3. Fase 0 - Fundação técnica

Objetivo: criar a base do projeto.

Entregas:

- Estrutura de pastas.
- Front controller em `public/index.php`.
- Roteador simples.
- Controller base.
- Model base com PDO.
- Classe de conexão com banco.
- Sistema de views PHP.
- Helper de redirect, session, csrf, validation e response.
- Layout base Bootstrap.
- Página inicial simples.
- Tratamento de erro e log.

Critério de pronto:

- A aplicação abre no navegador.
- Rotas GET/POST funcionam.
- Conexão com banco funciona.
- Views carregam com layout comum.

## 4. Fase 1 - Instalação e autenticação

Objetivo: permitir instalar e acessar o painel.

Entregas:

- Arquivo `.env` ou `config/local.php` gerado pelo instalador.
- Tela `/install`.
- Teste de conexão MySQL.
- Criação das tabelas base.
- Criação do primeiro administrador.
- Login/logout.
- Recuperação de senha simples.
- Middleware de autenticação.
- Middleware de papel: admin, producer, student.

Critério de pronto:

- Usuário instala o sistema sem terminal.
- Primeiro admin é criado.
- Login e logout funcionam.
- Rotas protegidas bloqueiam visitantes.

## 5. Fase 2 - Painel administrativo mínimo

Objetivo: criar a navegação administrativa.

Entregas:

- Dashboard simples.
- Menu lateral/topbar Bootstrap.
- Página de perfil.
- Configurações gerais.
- Configuração de SMTP.
- Configuração inicial de gateway.
- Upload de logo e favicon opcional.

Critério de pronto:

- Admin acessa painel.
- Configurações são salvas e carregadas.
- Layout é responsivo.

## 6. Fase 3 - Produtos

Objetivo: cadastrar produtos vendáveis.

Entregas:

- Listagem de produtos.
- Criar produto.
- Editar produto.
- Excluir/inativar produto.
- Upload de imagem quadrada.
- Slug automático e editável.
- Status ativo/inativo.
- Tipo de produto: digital, área de membros, link externo.
- Link público de checkout.

Critério de pronto:

- Admin cadastra produto com imagem.
- Produto aparece no checkout.
- Produto inativo não vende.

## 7. Fase 4 - Checkout MVP

Objetivo: vender um produto por link público.

Entregas:

- Rota `/c/{slug}`.
- Página de checkout responsiva.
- Resumo do produto com imagem.
- Formulário de comprador.
- Criação de sessão de checkout.
- Criação de pedido.
- Cupom simples.
- Página de pagamento Pix.
- Página de obrigado.

Critério de pronto:

- Visitante abre checkout.
- Preenche dados.
- Pedido é criado.
- Página Pix/obrigado funciona.

## 8. Fase 5 - Pagamento e webhooks

Objetivo: confirmar pagamento e liberar acesso.

Entregas:

- Serviço `PaymentService`.
- Gateway Pix inicial.
- Webhook seguro por token/assinatura.
- Atualização de pedido para pago.
- Aprovação manual pelo painel.
- Registro de logs de webhook.
- Tratamento de pagamento duplicado.

Critério de pronto:

- Pedido muda de status corretamente.
- Webhook não duplica liberação.
- Admin consegue aprovar manualmente.

## 9. Fase 6 - Alunos e liberação de acesso

Objetivo: controlar quem acessa cada produto.

Entregas:

- Criar aluno automaticamente após pagamento.
- Vincular aluno ao produto comprado.
- Listar alunos.
- Editar aluno.
- Remover acesso.
- Criar acesso manual.
- Enviar e-mail básico de acesso, se SMTP estiver configurado.

Critério de pronto:

- Compra paga libera acesso.
- Aluno consegue entrar.
- Admin consegue gerenciar acessos.

## 10. Fase 7 - Área de membros MVP

Objetivo: entregar conteúdo ao aluno.

Entregas:

- Login do aluno.
- Página de produtos liberados.
- Página do curso/produto.
- Listagem de seções e módulos.
- Página de aula.
- Progresso concluído/não concluído.
- Conteúdo texto rico.
- Vídeo embed responsivo.
- Arquivo para download.

Critério de pronto:

- Aluno acessa conteúdo comprado.
- Aula de texto, vídeo e arquivo funcionam.
- Progresso é salvo.

## 11. Fase 8 - Builder da área de membros

Objetivo: permitir que o admin monte cursos.

Entregas:

- CRUD de seções.
- CRUD de módulos.
- CRUD de aulas.
- Ordenação por posição.
- Campo de âncora em seção e módulo.
- Upload de capa/banner.
- Editor de texto rico seguro.
- Preview simples.

Critério de pronto:

- Admin cria uma estrutura completa de curso.
- Aluno visualiza a estrutura corretamente.
- HTML salvo é seguro contra XSS.

## 12. Fase 9 - Vendas e relatórios simples

Objetivo: dar controle operacional ao produtor.

Entregas:

- Listagem de vendas.
- Filtros por status, período, produto e busca.
- Detalhe do pedido.
- Exportação CSV.
- Cards básicos no dashboard: vendas, receita, pedidos pagos, pendentes.

Critério de pronto:

- Admin acompanha vendas sem acessar banco.
- Exportação CSV funciona.

## 13. Fase 10 - Melhorias pós-MVP

Entram depois do núcleo estável:

- Order bump.
- Upsell e downsell.
- Planos e assinaturas.
- Boleto e cartão.
- Recuperação de carrinho.
- E-mail marketing.
- Certificados.
- Comunidade.
- Comentários em aulas.
- Gamificação.
- PWA.
- Push notifications.
- Integrações externas.
- API pública.
- Multi-gateway com fallback.
- Equipe e permissões granulares.
- Logs avançados.
- Comprovante de entrega em PDF.

## 14. Ordem recomendada de commits iniciais

1. `chore: create project skeleton`
2. `feat: add router and view renderer`
3. `feat: add database layer`
4. `feat: add installer`
5. `feat: add authentication`
6. `feat: add admin layout`
7. `feat: add product management`
8. `feat: add public checkout`
9. `feat: add order processing`
10. `feat: add payment webhook`
11. `feat: add student access`
12. `feat: add member area`
13. `feat: add member builder`
14. `feat: add sales panel`

## 15. Riscos técnicos

### HTML rico e XSS

Risco: editor de texto permitir scripts maliciosos.

Mitigação:

- Sanitizar HTML no backend.
- Usar whitelist de tags e atributos.
- Bloquear `script`, `style`, eventos inline e URLs suspeitas.

### Uploads

Risco: upload de arquivo executável ou malicioso.

Mitigação:

- Validar MIME real.
- Renomear arquivos.
- Guardar uploads fora de pasta executável quando necessário.
- Bloquear execução de PHP em uploads.

### Webhooks

Risco: pagamento falso por chamada externa.

Mitigação:

- Token secreto por gateway.
- Assinatura quando o gateway suportar.
- Idempotência por `gateway_reference`.

### Hospedagem compartilhada

Risco: limitações de terminal, Composer, symlink e cron.

Mitigação:

- Não exigir build frontend.
- Não exigir fila no MVP.
- Usar cron por URL opcional.
- Evitar dependências pesadas.

## 16. Padrão visual recomendado

- Bootstrap 5 como base.
- Layout limpo com sidebar no desktop e offcanvas no mobile.
- Cards para produtos, módulos e métricas.
- Tabelas responsivas.
- Modais apenas onde simplificam o fluxo.
- Alertas claros de sucesso/erro.
- Estados vazios bem escritos.

## 17. Métrica de sucesso do MVP

O MVP é considerado validado quando:

- Um produto pode ser criado.
- Um checkout pode vender esse produto.
- Um pedido pago libera acesso automaticamente ou manualmente.
- O aluno consegue acessar a área de membros.
- O admin consegue montar conteúdo e acompanhar vendas.
- O sistema roda sem Node, sem Laravel, sem Redis e sem fila obrigatória.

## 18. Decisão final de escopo

O novo sistema deve nascer menor que o atual.

A meta não é ter todos os recursos no primeiro lançamento. A meta é criar uma base limpa, compreensível e robusta para evoluir sem depender de uma stack pesada.
