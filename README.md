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
demo@kfilmes.com'

Senha:
kfilmes123
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

## 📸 Capturas da aplicação

A interface do K-Filmes foi desenvolvida com foco em uma experiência cinematográfica, responsiva e intuitiva, adaptando a navegação para desktop e dispositivos móveis.

---

### 🏠 Dashboard

Tela principal da aplicação, apresentando filmes em destaque, recomendações e conteúdos dinâmicos integrados com a API do TMDB.

<img width="1874" height="808" alt="Dashboard" src="https://github.com/user-attachments/assets/06fde062-12c7-4cf5-ac2b-5b12f8adb24c" />

---

### 📱 Dashboard Mobile

Versão responsiva do dashboard, adaptada para telas menores mantendo a experiência de navegação e apresentação dos filmes.

<img width="346" height="775" alt="Dashboard Mobile" src="https://github.com/user-attachments/assets/29cf033b-9f57-426f-8fa0-8c8bc3ef9b7a" />

---

### 🎞️ Biblioteca

Área destinada à organização dos filmes do usuário, permitindo visualizar e gerenciar sua coleção pessoal.

<img width="1434" height="851" alt="Biblioteca" src="https://github.com/user-attachments/assets/03eec5ba-916f-4f92-a253-cb9f87638912" />

---

### 🎬 Detalhes do filme

Página com informações completas da obra, incluindo dados obtidos através da API TMDB, como sinopse, avaliação, gênero e informações adicionais.

<img width="1888" height="906" alt="Detalhes do filme" src="https://github.com/user-attachments/assets/dd828bc9-2331-49f9-a0cb-8a107400505a" />

---

### 🎲 Sorteio de filme

Funcionalidade para auxiliar o usuário na escolha de um filme, gerando uma sugestão aleatória baseada no catálogo disponível.

<img width="676" height="731" alt="Sorteio de filme" src="https://github.com/user-attachments/assets/9d01663b-89ff-4f7a-aeae-3f0fd86e9a4a" />


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
