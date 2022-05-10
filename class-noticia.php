<?php

class GetNoticias{
    var $servername = "HOST";
    var $database = "BANCO";
    var $username = "USUARIO";
    var $password = "SENHA";

    var $numero_paginas = 5;
    var $url = 'https://www.gov.br/compras/pt-br/acesso-a-informacao/noticias';


    function __construct() {
        $this->bufferExec('Iniciando tarefa');
        $this->getNoticia();



        if($_GET['mostar_dados'] == 's'){
            $this->getNoticiaDB();
        }
    }

    /*
     * Executa as verificações, busca e grava as noticias
     * Felipe Jaguszewski 09/05/2022
    */
    public function getNoticiaDB(){
        $conn = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
        $sql = "SELECT * FROM noticia";
        $result = $conn->query($sql);


        $dados = '<table class="table"><thead><tr><th scope="col">#</th><th scope="col">DATA</th><th scope="col">Título</th></tr></thead><tbody>';
        while ($row = $result->fetch_assoc()) {
            $dados .= '<tr>                      
                      <td><a target="_blank" href="'.$row['url'].'">Ver notícia</a></td>
                      <td>'.$row['data_hora'].'</td>
                      <td >'.$row['headline'].'</td>
                    </tr>';
        }

        echo $dados.'</tbody></table>';
    }

    /*
     * Verifica se a tabela noticias existe no banco de dados
     * Felipe Jaguszewski 09/05/2022
     */
    public function checkTableNoticias(){
        $conn = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
        $sql = "SELECT * FROM noticia limit 1";
        $result = $conn->query($sql);
        return $result->num_rows;
    }

    /*
     * Cria a tabela noticias no banco de dados
     * Felipe Jaguszewski 09/05/2022
     */
    public function createTableNoticias(){
        $this->bufferExec('Criando tabela');
        $conn = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $sql = "CREATE TABLE noticia (
                    id  int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    headline VARCHAR(500) NOT NULL,
                    data_hora VARCHAR(20) NOT NULL,
                    url VARCHAR(2048) NOT NULL
                )";

        $conn->query($sql);
    }

    /*
     * Executa as verificações, busca e grava as noticias
     * Felipe Jaguszewski 09/05/2022
    */
    public function getNoticia(){
        $this->bufferExec('Verificando tabelas');

        if($this->checkTableNoticias() <= 0)
            $this->createTableNoticias();

        $data = $this->getDataPage($this->url, $this->numero_paginas);

        if(!empty($data)){
            $this->insertNoticias($data);
            $this->bufferExec('Tarefa finalizada com sucesso!');
        }else{
            $this->bufferExec('Não foi possível encontrar noticias!');
        }
    }

    /*
     * Descarrega o buffer mostrando o estado atual da tarefa
     * Felipe Jaguszewski 09/05/2022
     */
    public function bufferExec($msg)
    {
        echo '<p class="text-center p-1 mb-0 alert alert-primary">'.$msg.'</p>';
        flush();
        ob_flush();
        //sleep(1);
    }

    /*
     * Busca e filtra as noticias disponíveis no portal
     * Felipe Jaguszewski 09/05/2022
     */
    public function getDataPage($url, $npage = 5)
    {

        $this->bufferExec('Acessando portal');

        $dom = new DOMDocument();
        $noticias = array();
        $count=0;
        $url_p='';


        for ($i=0; $i < $npage*30;) {
            $url_p = $url.'?b_start:int='.$i;
            @$dom->loadHTMLFile($url_p);
            $anchors = $dom->getElementsByTagName('article');

            if($count == 0)
                $this->bufferExec('Iniciando busca');

            foreach ($anchors as $element) {
                $article = $element->getElementsByTagName('a');
                $noticias[$count]['url']= $article->item(0)->getAttribute('href');

                $article = $element->getElementsByTagName('h2');
                $noticias[$count]['titulo'] = rtrim(ltrim($article->item(0)->textContent));

                $this->bufferExec('Extraindo noticia '.rtrim(ltrim($article->item(0)->textContent)).'');

                $article = $element->getElementsByTagName('span');

                if(count($article) == 6)
                    $noticias[$count]['data_hora'] = preg_replace('/\s+/', '', $element->getElementsByTagName('span')->item(3)->textContent).' '.preg_replace('/\s+/', '', $element->getElementsByTagName('span')->item(4)->textContent);
                else
                    $noticias[$count]['data_hora'] = preg_replace('/\s+/', '', $element->getElementsByTagName('span')->item(4)->textContent).' '.preg_replace('/\s+/', '', $element->getElementsByTagName('span')->item(5)->textContent);

                $count++;
            }

            $i = $i+30;
        }

        return $noticias;
    }

    /*
     * Grava noticias no banco de dados
     * Felipe Jaguszewski 09/05/2022
     */
    public function insertNoticias($data=array()){
        $conn = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $this->bufferExec('Salvando noticias');

        foreach ($data as $d) {
            if($this->checkDBNoticia($d) > 0)
                continue 1;

            $titulo = $d['titulo'];
            $data_hora = $d['data_hora'];
            $url = $d['url'];

            $this->bufferExec('Gravando noticia '.$titulo.'');

            $sql = "INSERT INTO noticia(headline, data_hora, url) VALUES ('".$titulo."', '".$data_hora."', '".$url."')";


            $conn->query($sql);
        }
    }

    public function checkDBNoticia($d){
        $conn = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $titulo = $d['titulo'];
        $data_hora = $d['data_hora'];

        $sql = "SELECT * FROM noticia WHERE headline = '".$titulo."' AND data_hora = '".$data_hora."'";

        return mysqli_num_rows($conn->query($sql));
    }
}

new GetNoticias();