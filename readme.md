# Desafio Back-End - Forseti

> Web crawler para busca de informações no portal do ComprasNet

## 💻 Pré-requisitos

Antes de começar, verifique se você atendeu aos seguintes requisitos:

* PHP 7.4 ou superior 
* MySQL

## 🚀 Como configurar o Web crawler

Para configurar o Web crawler, siga estas etapas:

Configure o arquivo class-noticia.php e defina as variáveis
```
var $servername = "HOST";
var $database = "BANCO";
var $username = "USUARIO";
var $password = "PASSWORD";
```

## ☕ Usando Web crawler

Para usar Web crawler, siga estas etapas:

1. Execute o arquivo index.php no navegador
2. O script irá criar a tabela "noticia" onde será gravado as informações
3. Você podera definir em quantas paginas quer executar o  Web crawler.
```
var $numero_paginas = 5;
```

Caso queira imprimir na tela os registros adicione o parametro "mostar_dados" via GET.
```
https://development.divdigital.com.br/forseti/?mostar_dados=s
```

## ☕ Sobre o desafio
Gostei muito de participar desse teste foi algo muito prazerozo de se fazer.

Utilizei a classe DOMDocument para manipular o HTML e buscar os dados solicitados no teste. 

Agradeço a oportunidade.