# ProjectONE

**Primeiro projeto de front-end do autor: um site de cadastro, login e consulta de usuários em HTML, CSS, PHP e MySQL.**

![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL%2FMariaDB-mysqli-4479A1?logo=mysql&logoColor=white)

## Sobre

Este repositório nasceu como estudo: depois de escrever soluções em C++ e integrá-las a um site em PHP, o autor decidiu aprender front-end para conseguir entregar um visual mais agradável. O resultado é um site pequeno com quatro telas e quatro scripts PHP que gravam e consultam usuários numa base MySQL.

O que existe hoje:

- uma página de menu (`index.html`);
- cadastro de usuário com e-mail, senha, nome, sobrenome, RG e CPF (`register.html` + `register.php`);
- login com sessão PHP (`login.html` + `login.php`);
- um painel de busca de cadastros (`users.html` + `users.php`);
- um endpoint de busca avulso (`search.php`);
- uma página pessoal estática (`quemsou.html`).

É um projeto de estudo, não um produto. A seção [Estado atual e limitações](#estado-atual-e-limitações) descreve com precisão o que funciona e o que não funciona.

## Como funciona

```
navegador
  │
  ├─ index.html ─── menu (Registro, Login, Users, Quem sou?, Mentoria*, Contato*)
  │
  ├─ register.html ──POST──▶ register.php ──INSERT──▶ MySQL (base users, tabela dados)
  │                                                       │
  ├─ login.html ─────POST──▶ login.php ─────SELECT───────▶┘
  │                              │
  │                              └─ grava $_SESSION e redireciona para index.html
  │
  ├─ users.html ─────POST──▶ users.php ─────SELECT───────▶ MySQL
  │                              │
  │                              └─ devolve texto puro (o painel não exibe o resultado)
  │
  ├─ search.php ─────POST──▶ busca por e-mail, nome, sobrenome, RG ou CPF (LIMIT 5)
  │
  └─ quemsou.html ─── página pessoal, só HTML e CSS

* itens de menu sem destino (apontam para "#")
```

O fluxo é o clássico de PHP procedural: cada página HTML tem um formulário que faz `POST` para um arquivo `.php` irmão. O `.php` abre a conexão com o MySQL, executa a consulta e redireciona o visitante. Não há framework, camada de rotas, ORM nem API JSON.

Detalhe importante de configuração: as credenciais do banco são lidas de variáveis de ambiente (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`) e caem no padrão do WAMP local quando não estão definidas (`localhost`, `root`, senha vazia, base `users`). Isso permite rodar o projeto tanto no WAMP original quanto em qualquer outro ambiente sem editar código.

## Stack

| Camada | Escolha |
|---|---|
| Marcação | HTML5 estático, sem template engine |
| Estilo | CSS3 puro, um arquivo por página (`style/`) |
| Backend | PHP procedural, sem framework e sem Composer |
| Banco | MySQL / MariaDB via extensão `mysqli` |
| Sessão | `$_SESSION` nativa do PHP |
| JavaScript | Praticamente nenhum (ver limitações) |
| Servidor de desenvolvimento | Servidor embutido do PHP (`php -S`) |
| Deploy atual | Vercel, apenas da parte estática |

## Requisitos

- **PHP 8.1 ou superior** com a extensão `mysqli` habilitada. Testado em PHP 8.3.33.
- **MySQL 5.7+ ou MariaDB 10.4+**. Testado em MariaDB 11.8.
- Nenhuma dependência de Composer, npm ou build. Não há etapa de compilação.

O projeto original rodava em WAMP (Windows + Apache + MySQL + PHP). Há um resquício disso em `_notes/dwsync.xml`.

## Início rápido

Os comandos abaixo foram executados e conferidos durante a verificação deste README.

```bash
# 1. Crie a base e a tabela (o schema está versionado em sql/schema.sql)
mysql -u root < sql/schema.sql

# 2. Suba o servidor embutido do PHP na raiz do projeto
php -S localhost:8000

# 3. Abra http://localhost:8000/ no navegador
```

Se o seu MySQL não for `root` sem senha, informe as credenciais por variável de ambiente:

```bash
DB_HOST=127.0.0.1 DB_USER=meuusuario DB_PASS=minhasenha DB_NAME=users php -S localhost:8000
```

Fluxo mínimo para conferir que está tudo ligado:

```bash
# cadastra um usuário (responde 302 e redireciona para login.html)
curl -i -X POST http://localhost:8000/register.php \
  -d 'email_name=teste@example.com&senha_name=123&nome_name=Ana&sobrenome_name=Silva&rg_name=1234567&cpf_name=98765432100'

# confere que gravou
curl -X POST http://localhost:8000/search.php -d 'search-field=teste@example.com'
# esperado: teste@example.com<br>
```

No WAMP ou em qualquer Apache/nginx com PHP, basta apontar o document root para a raiz do repositório. Não é preciso configurar nada além do banco.

## Uso

### Páginas

| Página | O que faz |
|---|---|
| `index.html` | Menu com links para as demais telas. Os itens Mentoria e Contato ainda apontam para `#`. |
| `register.html` | Formulário de cadastro: e-mail, senha, nome, sobrenome, RG e CPF. Envia `POST` para `register.php`. |
| `login.html` | Formulário de login (e-mail e senha). Envia `POST` para `login.php`. |
| `users.html` | Painel administrativo com campo de busca. Envia `POST` para `users.php`. |
| `quemsou.html` | Página pessoal estática com texto de apresentação, tecnologias e links para portfólio e LinkedIn. |

### Endpoints

Todos recebem `POST` de formulário e devolvem HTML ou texto puro, não JSON.

| Endpoint | Entrada | Comportamento |
|---|---|---|
| `register.php` | `email_name`, `senha_name`, `nome_name`, `sobrenome_name`, `rg_name`, `cpf_name` | Insere uma linha em `dados` e redireciona (302) para `login.html`. Se o banco estiver fora do ar, responde 503. |
| `login.php` | `email`, `senha` | Confere as credenciais em `dados`. Em caso de sucesso grava `$_SESSION['UsuarioNome']` e `$_SESSION['UsuarioNivel']` e redireciona para `index.html`; em caso de falha imprime `Login inválido!`. Campos vazios redirecionam para `login.html`. |
| `users.php` | `search-field` | Busca por `email`, `nome`, `sobrenome`, `rg` ou `cpf` com `LIKE`, `LIMIT 1`, e imprime os seis campos em texto puro separados por `<br>`. |
| `search.php` | `search-field` | Igual ao anterior, mas com `LIMIT 5` e imprimindo apenas o e-mail. Nenhuma página chama este endpoint. |

## Produção / Deploy

Não há build. O artefato é o próprio código-fonte.

**Aplicação completa (PHP + MySQL):** publique a raiz do repositório em um servidor com PHP e `mysqli` (Apache, nginx + php-fpm, ou o WAMP original) e aponte as variáveis `DB_*` para o banco de produção. Não há arquivo de configuração de servidor versionado.

**Parte estática:** o repositório tem o campo homepage apontando para `https://project-one-three.vercel.app`, que serve as páginas HTML. **Atenção:** o Vercel trata arquivos `.php` como estáticos e os devolve como texto puro, ou seja, `https://project-one-three.vercel.app/login.php` expõe o código-fonte do arquivo. Os endpoints PHP não executam nesse deploy. Isso está registrado nas limitações abaixo.

## Estrutura do projeto

```
.
├── index.html                 menu principal
├── register.html              formulário de cadastro
├── register.php               INSERT do cadastro
├── login.html                 formulário de login
├── login.php                  SELECT + sessão
├── users.html                 painel de busca
├── users.php                  busca que alimenta o painel (LIMIT 1)
├── search.php                 busca avulsa (LIMIT 5), endpoint órfão
├── quemsou.html               página pessoal estática
├── sql/
│   └── schema.sql             base "users" e tabela "dados"
├── style/                     CSS por página (index, login, register, users, quemsou)
├── style_bkp/                 CSS antigo, não referenciado por nenhuma página
├── script/                    arquivos JS vazios ou com uma única palavra
├── image/                     imagens usadas pelas páginas
└── _notes/dwsync.xml          resquício do Dreamweaver/WAMP, sem uso
```

## Verificação

**Não há suíte de testes automatizada neste repositório, nem CI configurada.** O que foi verificado manualmente na última revisão:

- `php -l` nos quatro arquivos PHP: sem erros de sintaxe;
- `php -S localhost:8000` servindo todas as páginas e assets (HTTP 200);
- cadastro, login (válido e inválido), busca e o cenário de banco fora do ar, exercitados com `curl`;
- `sql/schema.sql` aplicado com `mysql -u root < sql/schema.sql`, sem erro.

Adicionar uma suíte de fumaça automatizada é a pendência mais óbvia do projeto.

## Estado atual e limitações

Este é um projeto de estudo e o código reflete isso. O que está resolvido e o que não está:

**Segurança**

- As senhas são gravadas em **texto puro** na coluna `senha`, sem hash. Qualquer pessoa com acesso à tabela lê todas as senhas.
- O painel `users.html` / `users.php` **não tem autenticação nem autorização**: quem abrir a página pode buscar e ler nome, sobrenome, RG, CPF e senha de qualquer cadastro.
- RG e CPF são coletados, armazenados e exibidos sem nenhum controle de acesso nem finalidade declarada. São dados pessoais e não deveriam estar em um projeto público de estudo.
- O formulário de cadastro não valida nada no servidor: aceita e-mail inválido, senha vazia e CPF inexistente.
- Sem proteção CSRF, sem limite de tentativas de login, sem HTTPS forçado.

As consultas SQL usam `prepared statements` desde a revisão atual, então injeção de SQL por `search-field`, `email`, `senha` e os campos do cadastro está bloqueada.

**Funcionalidade**

- O painel `users.html` **não exibe os resultados**. Os `div`s de resultado existem, mas não há JavaScript que os preencha; `users.php` devolve texto puro numa página em branco. `script/users.js` está vazio.
- `search.php` é um endpoint órfão: nenhuma página o chama.
- Busca vazia retorna registros em vez de não retornar nada.
- Não há edição nem exclusão de cadastro, apesar do botão "DELETAR CADASTRO" e dos ícones de edição no painel.
- Não há logout, nem qualquer uso real da sessão gravada no login.
- Os itens de menu Mentoria e Contato apontam para `#`.
- O botão PORTIFOLIO em `quemsou.html` aponta para `https://github.com/truuta`, que responde 404.

**Código e estrutura**

- `script/index.js` e `script/register.js` contêm apenas a palavra `class`; `script/quemsou.js` e `script/users.js` estão vazios. O jQuery é carregado em `users.html` e nunca usado.
- `style_bkp/` não é referenciado por nenhuma página.
- As imagens `arrow.png`, `download-direto.png`, `js.png` e `react.png` não são usadas.
- Em `login.html`, `register.html` e `users.html` o `<!DOCTYPE html>` e o `<html>` estão dentro do `<head>`. Os navegadores corrigem isso na marra, mas a marcação é inválida.
- `_notes/dwsync.xml` é resquício do Dreamweaver e aponta para um caminho `C:/wamp64`.
- Não há `.gitignore`.

**Infraestrutura**

- Sem CI, sem testes automatizados e sem licença definida.
- O deploy no Vercel expõe o código-fonte dos arquivos `.php` como texto puro (ver a seção de deploy).
- Os branches `bugs` e `features` no remoto estão parados em commits anteriores ao `main`.

## Documentação

| Arquivo | Conteúdo |
|---|---|
| [`sql/schema.sql`](sql/schema.sql) | Schema da base `users` e da tabela `dados` usada pelas consultas PHP |

Não há outra documentação no repositório.

## Licença

Nenhuma licença definida. O repositório não tem arquivo `LICENSE` e o GitHub não identifica licença. Sem uma licença explícita, o código permanece com todos os direitos reservados ao autor.
