# Tela de Login e Registro

Este é um projeto de tela de login e registro desenvolvido com uma arquitetura Orientada a Objetos e seguindo o padrão de design MVC (Model-View-Controller).

## Tecnologias utilizadas
- **PHP**
- **JavaScript**
- **HTML**
- **MySQL**

## Bibliotecas / Dependências
- [**Bootstrap**](https://getbootstrap.com/) — para estilização responsiva.
- [**Composer**](https://getcomposer.org/) — para gerenciamento de dependências PHP.
- [**vlucas/phpdotenv**](https://github.com/vlucas/phpdotenv) — para gerenciamento de variáveis de ambiente (`.env`).
- [**league/oauth2-google**](https://github.com/thephpleague/oauth2-google) — para autenticação via Google (OAuth 2.0).
- [**twig/twig**](https://twig.symfony.com/) — motor de templates para PHP.
- [**FontAwesome**](https://fontawesome.com/) — para a biblioteca de ícones.

## Funcionalidades
- Cadastro de usuários
- Login com validação
- Login com o Google
- Separação clara entre Model, View e Controller

## Estrutura do Projeto

A arquitetura do projeto está organizada em diretórios que seguem o padrão MVC, com pastas adicionais para bibliotecas e funcionalidades específicas.

-   `Config/` - Arquivos de configuração da aplicação.
-   `Control/` e `ControlPage/` - Contêm a lógica de controle (`Controller`) para lidar com formulários, ações, rotas e páginas.
-   `Core/` - Classes principais e a lógica central da aplicação.
-   `Database/` - Classes relacionadas à conexão e manipulação do banco de dados.
-   `Model/` - Modelos e manipulação de dados (`Model`).
-   `Templates/` - Arquivos de interface e templates (`View`) da aplicação.
-   `Utils/` - Classes utilitárias e funções auxiliares.
-   `Widgets/` - Classes para a criação de elementos de interface reutilizáveis.
-   `vendor/` - Dependências instaladas via Composer.
-   `index-login.php`, `index-subscribe.php`, `index.php` - Arquivos de entrada (entry points) da aplicação.