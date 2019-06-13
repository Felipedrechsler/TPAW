<?php
//
// A very simple PHP example that sends a HTTP POST to a remote site
//
header('content-type: application/json');

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"https://balneabilidade.ima.sc.gov.br/relatorio/historico");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"municipioID=23&localID=39&ano=2019&redirect=true");

// In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// Receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$html = curl_exec($ch);

//echo $html;

$doc = new DOMDocument();

$dados = $doc->loadHtml($html);

$tables = $doc->getElementsByTagName('table');


$pontos = [];

foreach ($tables as $key=>$table) {
    if($key>0){
        if ($key % 2 != 0){
        //    print_r('Tabela '.$key.'<br>');
            //somente funciona nas tabelas impares
            $labels = $table->getElementsByTagName('label');
            $ponto = [];	


            foreach ($labels as $label){
      //   		echo $label->nodeValue . ' | ';         // Município: ITAJAÍ
         											//					0			1
         		$pedacos = explode(': ', $label->nodeValue);    // $pedacos = array(0 => 'Município', 1 => 'ITAJAÍ')   

         																	// $ponto = array('Municipio' => 'ITAJAI')
         		$ponto[$pedacos[0]] = $pedacos[1];							// $ponto['Municipio'] = 'Itajai'
            }
            $pontos[$key] = $ponto; // armazeno o arry ponto dentro de pontos
            

        }else{ //tabelas pares

            $linhas = $table->getElementsByTagName('tr');
            $coletas = [];
            foreach ($linhas as $i=> $linha) {
                if($i>0){
                    //somente funciona nas tabelas pares
                    $celulas = $linha->getElementsByTagName('td');
                    $coleta = [];
                    foreach ($celulas as $celula){
                        $coleta[$celula->getAttribute('class')] = $celula->textContent;
        //                echo ' |    '. $celula->getAttribute('class') . ' - '.$celula->textContent .' ';         // Município: ITAJAÍ
                                                    //                  0           1
                    }    
                    $coletas[] = $coleta;
                }
            }


           $pontos[$key-1]['coletas'] = $coletas; 
           //$pontos[$key] = array_filter($pontos);
        }
    }
}

// echo '<pre>';
// var_dump($pontos);

echo json_encode($pontos);


//echo $html

//curl_close ($ch);

// Further processing ...
//if ($server_output == "OK") { ... } else { ... }
?>