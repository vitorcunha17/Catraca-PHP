<?php 
// sistema de garagem solicitando a abertura de uma cancela para passagem de veículo - chip Henry Argos
require_once 'catraca_lib.php';
$catraca = new Catraca('192.168.0.186', 3000);
$resposta = $catraca->liberar_entrada();
if( is_string($resposta) ){
    echo "ERRO: $resposta";
    die;
}
elseif(empty($resposta) ){
    echo "mensagem de abertura enviada com sucesso";
}
?>