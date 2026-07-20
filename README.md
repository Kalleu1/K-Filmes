# 🎬 K-Filmes

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-Framework-red?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/PHP-8.x-blue?style=for-the-badge&logo=php">
  <img src="https://img.shields.io/badge/MySQL-Database-orange?style=for-the-badge&logo=mysql">
  <img src="https://img.shields.io/badge/TMDB-API-green?style=for-the-badge">
</p>

## 🌐 Demonstração

O K-Filmes está disponível online em produção:

🔗 **Acesse:** 195.201.226.244

Para testar a aplicação, utilize o usuário:

```
Usuário:
teste@kfilmes.com

Senha:
12345678
```

> O usuário de demonstração possui dados preparados para explorar as funcionalidades da plataforma.

---

# 📌 Sobre o projeto

O **K-Filmes** é uma plataforma web para descoberta, organização e acompanhamento de filmes.

A aplicação foi desenvolvida utilizando **Laravel**, com integração à API do **TMDB (The Movie Database)** para obter informações dinâmicas sobre filmes, incluindo posters, backdrops, avaliações, gêneros e detalhes das obras.

O projeto busca proporcionar uma experiência semelhante às grandes plataformas de streaming, com foco em uma interface cinematográfica, responsiva e otimizada para diferentes dispositivos.

---

# ✨ Funcionalidades

## 🎥 Descoberta de filmes

- Catálogo dinâmico utilizando a API TMDB.
- Categorias como:
  - Populares
  - Em alta
  - Mais votados
  - Por gênero
- Página de detalhes dos filmes.
- Carrosséis cinematográficos.

---

## 👤 Usuários

- Sistema de autenticação.
- Ambiente personalizado por usuário.
- Organização individual de filmes.
- Dados isolados utilizando autenticação Laravel.

---

## 🎨 Interface

- Design inspirado em plataformas de streaming.
- Tema escuro.
- Hero section dinâmica.
- Layout responsivo.
- Experiência otimizada para mobile e desktop.
- Animações e transições para melhorar a navegação.

---

# 🛠️ Tecnologias utilizadas

## Backend

- PHP
- Laravel
- Laravel Breeze
- MySQL
- Eloquent ORM

## Frontend

- Blade Templates
- HTML5
- CSS3
- JavaScript
- Vite

## Integrações

- TMDB API

## Infraestrutura

- Deploy em VPS Linux
- Hospedagem Hetzner Cloud
- Nginx
- SSL

---

# 🏗️ Arquitetura

O projeto utiliza a arquitetura MVC padrão do Laravel:

```
app/
 ├── Models/
 ├── Services/
 │    └── TMDBService.php
 ├── Http/
 │    └── Controllers/

resources/
 ├── views/
 │    └── Blade Templates
 ├── css/
 │    └── Estilos separados por página
 └── js/
```

A integração com o TMDB é realizada através de uma camada de serviço dedicada, mantendo a separação de responsabilidades.

---

# 📷 Screenshots

Adicionar imagens da aplicação:

- Dashboard
- Página Descobrir
- Detalhes do filme
- Versão mobile

---

# 🎯 Objetivos do projeto

O desenvolvimento do K-Filmes teve como objetivos:

- Aplicar desenvolvimento Full Stack com Laravel.
- Trabalhar integração com APIs REST.
- Desenvolver uma aplicação escalável.
- Aplicar conceitos de UX/UI.
- Criar uma experiência semelhante a serviços reais de streaming.

---

# 🔮 Próximos passos

- [ ] Aplicativo mobile.
- [ ] Sistema de recomendações personalizado.
- [ ] Avaliação e comentários.
- [ ] Listas públicas de usuários.
- [ ] Melhorias de cache e performance.

---

# 👨‍💻 Desenvolvedor

**Kalleu Borges Queiroz**

Estudante de Sistemas de Informação | Desenvolvedor Back-end

GitHub:
https://github.com/Kalleu1

LinkedIn:
https://www.linkedin.com/in/kalleu-queiroz-8643a7210/

---

# 📄 Licença

Projeto desenvolvido para fins acadêmicos e de portfólio.
