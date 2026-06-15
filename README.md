# Trackle 🎧

### Um jogo de adivinhação musical em formato de webapp

Baseado no finado **Heardle**, o objetivo do jogo é acertar a música em no máximo 10 tentativas, onde cada tentativa libera +1 segundo do trecho da música. O jogo dispõe de um modo diário, onde uma nova música aleatória é liberada a cada dia, e um modo livre, que pode ser jogado infinitas vezes dentro de uma playlist predefinida.

> Este jogo é gratuito e utiliza dados fornecidos pela API do Deezer. Todos os direitos sobre as obras pertencem aos seus respectivos artistas e gravadoras.

## Jogar

Jogue online em [https://gabrielsilva.dev.br/trackle](https://gabrielsilva.dev.br/trackle)

## Tecnologias

- PHP (backend)
- jQuery + Bootstrap + FontAwesome (frontend)
- SQLite (banco de dados)
- Deezer (API de dados)

## Instalação

### Instalação com Docker (recomendado)

Para rodar com Docker no seu próprio servidor, inicie o container usando o comando:

```bash
docker compose up -d --build
```

O servidor estará pronto e rodando na porta `8080`.

### Instalação manual

Para rodar no seu próprio servidor, é necessário possuir o Apache com `mod_rewrite` habilitado e PHP na versão 8.0 com a extensão `pdo_sqlite` habilitada.

Basta clonar o repositório para dentro da pasta `www` ou `public_html` (dependendo da sua instalação do Apache) e acessar a URL ou IP do servidor no browser.

## Comandos administrativos

Abra um terminal na pasta `commands` para executar um dos comandos disponíveis:

- `php add <deezer_id>` - Adiciona uma playlist do Deezer e suas músicas ao banco de dados
- `php update <deezer_id?>` - Atualiza uma playlist do Deezer e suas músicas no banco de dados (se `deezer_id` não for informado, atualiza todas as playlists do banco)
- `php remove <deezer_id>` - Remove uma playlist do Deezer e suas músicas do banco de dados

## Créditos

Desenvolvido por [Gabriel Silva](https://gabrielsilva.dev.br).

### Não é permitido reproduzir ou distribuir este jogo com fins lucrativos, anúncios ou qualquer forma de monetização.
