# Nome do Projeto

Descrição breve do projeto.

## Tecnologias Utilizadas

- **FrankenPHP**: Servidor web.
- **Laravel Octane**: Otimização de desempenho para Laravel.
- **PostgreSQL**: Banco de dados.

## Instalação

### Pré-requisitos

- Docker

### Passos

1. Clone o repositório:
    ```sh
    git clone https://github.com/falves97/vacation-plan.git vacation-plan
    cd vacation-plan
    ```

2. Inicie o ambiente Docker:
    ```sh
    docker compose up -d
    ```

3. Build do projeto, para compilar os assets:

    - Entre no container PHP:
    ```sh
    docker compose exec -it vacation-plan-webserver bash
    ```

    - Instale as dependências do PHP:
    ```sh
    npm run build
    ```

4. Acesse o projeto em [https://localhost](https://localhost).

## Licença

Este projeto está licenciado sob a Licença GPL 3 - veja o arquivo [LICENSE](LICENSE) para mais detalhes.
