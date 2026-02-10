# AGENTS.md — K-Filmes (Laravel + Blade + TMDB)

Você é um agente de desenvolvimento trabalhando no projeto K-Filmes.

## Objetivo do produto
- Gerenciador pessoal de filmes (assistidos e não assistidos).
- Experiência premium, cinematográfica, com identidade visual forte por filme.
- Sistema multiusuário REAL (todas as consultas e escritas devem respeitar `user_id`.

## Stack e regras
- Backend: Laravel (Controllers + Services).
- Frontend: Blade + CSS custom (SEM Tailwind).
- Build: Vite.
- DB: MySQL.
- Integração externa: TMDB API.
- Imagens share: Browsershot (Puppeteer) e/ou GD nativo, dependendo do fluxo.

## Estrutura importante do projeto (paths de referência)

### Layout base (onde tudo é importado)
- resources/views/layouts/base.blade.php

### Views (Blade)
- Dashboard:
  - resources/views/pages/dashboard.blade.php
- Filmes:
  - resources/views/filmes/
  - resources/views/filmes/show.blade.php
  - resources/views/filmes/show_tmdb.blade.php
  - resources/views/filmes/biblioteca.blade.php
  - resources/views/filmes/busca.blade.php
- Componentes reutilizáveis:
  - resources/views/components/

### CSS
- Principal:
  - resources/css/app.css
  - resources/css/base.css
- Por página:
  - resources/css/pages/dashboard.css
  - resources/css/pages/biblioteca.css
  - resources/css/pages/filme-do-dia.css
  - resources/css/pages/busca.css
  - resources/css/pages/createForms.css
  - resources/css/pages/login.css
  - resources/css/pages/share.css
  - resources/css/pages/show_tmdb.css
  - resources/css/pages/show.css
- Componentes:
  - resources/css/components/filme-card.css
  - resources/css/components/back-button.css
  - resources/css/components/sidebar.css
  - resources/css/components/toast.css

⚠️ Atenção:
- `.filme-card` é reutilizado globalmente.
- Alterações visuais específicas devem ser feitas via escopo:
  `.dashboard-page ...` ou por modificador `.filme-card--dashboard`.
- Evite mudanças globais em CSS que afetem library/show sem necessidade.

### Backend
- Controllers:
  - app/Http/Controllers/DashboardController.php
  - app/Http/Controllers/FilmeController.php
  - app/Http/Controllers/FilmeDoDiaController.php
  - app/Http/Controllers/ShareController.php
- Services:
  - app/Services/TmdbService.php
  - app/Services/ColorThemeService.php

### JavaScript
- Principal:
  - resources/js/app.js
- Por página:
  - resources/js/pages/dashboard.js
  - resources/js/pages/biblioteca.js
  - resources/js/pages/filmedodia.js
  - resources/js/pages/share.js
  - resources/js/pages/show.js
  - resources/js/pages/showTmdb.js

### Rotas
- routes/web.php

### Build / Assets
- vite.config.js
- public/storage/

## Regras obrigatórias (NÃO QUEBRAR)
1) Multiusuário
- Toda query de dados locais deve filtrar por `user_id = auth()->id()`.
- Nunca vazar dados de outro usuário.
- Em migrations/constraints: uniqueness deve ser por `(user_id, tmdb_id)` quando aplicável.

2) Separação de responsabilidades
- Controllers: finos (validação + chamada de services + retorno).
- Services: concentram regras (ex.: `TmdbService`, `ColorThemeService`).
- Views: sem lógica pesada; use helpers/formatters quando necessário.

3) Dados locais vs TMDB
- Dados locais: notas, comentários, status assistido, data assistida, plataforma, tema salvo etc.
- Dados TMDB: título, sinopse, poster/backdrop, gêneros, créditos etc.
- Não duplicar dados TMDB no banco sem necessidade (apenas o mínimo para uso local).

4) CSS e componentes
- `.filme-card` é reutilizado em várias telas.
- Se precisar alterar visual para uma tela específica (ex.: dashboard), use:
  - escopo: `.dashboard-page .filme-card { ... }`, ou
  - modificador: `.filme-card--dashboard`.
- Evite mudanças globais que afetem outras páginas.

## Padrões de UI (importante)
- Desktop já está bom: evite mudanças visuais em `>= 1024px`.
- Mobile deve ser mobile-first: não fazer “desktop encolhido”.
  - Hero compacto (altura fixa menor)
  - Seções em carrossel horizontal (scroll-x) quando fizer sentido
  - Tap targets grandes, tipografia legível, menos decoração vertical
- Performance no mobile:
  - quando possível, usar tamanhos de imagem menores (ex.: TMDB `w342` para cards, e um tamanho adequado para o hero).

## Convenções de mudanças
- Antes de criar um novo arquivo CSS/JS, procure se já existe um arquivo por página em:
  - `resources/css/pages/`
  - `resources/js/pages/`
- Reuse classes/componentes existentes; só crie novas classes quando necessário e com escopo de página.
- Prefira diffs pequenos e fáceis de revisar.

## Como trabalhar (processo do agente)
Antes de editar qualquer arquivo:
1) Identifique arquivos relevantes e resuma o que encontrou.
2) Proponha um plano em 3–7 passos, com diffs pequenos.
3) Liste critérios de validação (o que confirmar no mobile/desktop).

Durante as mudanças:
- Faça alterações com escopo.
- Prefira commits pequenos e fáceis de reverter.
- Não introduza novas dependências sem necessidade.

## Testes e validação
Sempre que possível:
- Rodar comandos básicos (quando existirem):
  - `php artisan test` (se houver testes)
  - `npm run build` ou `npm run dev` (verificar build)
- Validar manualmente:
  - mobile 360px e 414px
  - desktop >= 1024px inalterado

## Entrega esperada em cada tarefa
Ao finalizar, informe:
1) Arquivos alterados
2) Resumo do que mudou
3) Como testar
4) Como reverter (git checkout / git revert / commit)
