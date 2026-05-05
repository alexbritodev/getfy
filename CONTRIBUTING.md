# 🤝 Guia de Contribuição

Obrigado por contribuir com o Getfy.

Este projeto valoriza melhorias claras, seguras e úteis para a comunidade.

## ✅ Antes de começar

- Abra uma issue ou converse com os mantenedores quando a mudança for grande.
- Use uma branch descritiva, por exemplo: `fix/checkout-image`, `feature/member-area`, `docs/readme-update`.
- Mantenha o escopo pequeno sempre que possível.
- Evite misturar refatoração, nova feature e correção de bug no mesmo pull request.

## 🧪 Qualidade mínima

Antes de enviar um pull request, valide o que for aplicável:

```bash
composer test
npm run build
```

Também revise:

- Se não há erros de PHP/JavaScript.
- Se migrations são compatíveis com dados existentes.
- Se uploads, permissões e HTML renderizado são seguros.
- Se a mudança não quebra checkout, login, produtos, área de membros ou instalação.

## 🧱 Padrão de código

- Escreva código simples, legível e direto.
- Preserve o padrão visual e estrutural existente.
- Evite dependências novas sem justificativa clara.
- Não commite arquivos sensíveis, `.env`, credenciais, dumps ou uploads privados.
- Documente apenas o que ajuda manutenção real do projeto.

## 📬 Pull requests

Um bom PR deve conter:

- Título objetivo.
- Descrição do problema e da solução.
- Passos de teste realizados.
- Prints ou vídeos quando a alteração afetar interface.
- Observações sobre riscos, migrations ou compatibilidade.

## 🔒 Segurança

Se a contribuição envolve vulnerabilidade ou comportamento sensível, leia [`SECURITY.md`](SECURITY.md) antes de publicar detalhes.

## 💚 Comunidade

Seja respeitoso, objetivo e colaborativo. Críticas técnicas são bem-vindas quando ajudam o projeto a ficar mais sólido.
