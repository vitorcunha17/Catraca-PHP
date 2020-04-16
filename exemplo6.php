<?php
    // catraca liberando ou impedindo a passagem de pedestres após a identificação através da leitura de um cartão rfid - chipe Henry Prime SF
require_once 'catraca_lib.php';
$catraca = new Catraca('192.168.0.186', 3000);
while (true) {
    $resposta = $catraca->escutar();
    if( is_string($resposta) ){
        echo "ERRO: $resposta";
        die;
    }
    elseif( is_array($resposta) ){
        // obtemos o ID do usuário - número do cartão rfid
        if( $resposta['err_or_version'] != '00' ){
            echo "recebido o código de erro [".$resposta['err_or_version']."]";
        }
        else{
            $params = explode(']', $resposta['data']);
            $usuario_id = $params[1];
            // checa sua regra de negócio
            if ( usuario_pode_passar($usuario_id) ) {
                $resposta2 = $catraca->permitir_entrada($resposta['index']);
            }
            else {
                $resposta2 = $catraca->impedir_entrada( $resposta['index'], "BLOQUEADO" );
            }
            // verifica a resposta do comando de permitir/impedir entrada
            if( empty($resposta2) ){
                echo "mensagem de liberacao enviada\n";
            }
            elseif( is_string($resposta2) ){
                echo "ERRO: $resposta2";
            }
            // recebendo a resposta da catraca após a liberação
            $resposta3 = $catraca->escutar();
            if( is_string($resposta3) ){
                echo "ERRO: $resposta3";
                die;
            }
            elseif( is_array($resposta3) ){
                if( $resposta3['err_or_version'] != '00' ){
                    echo "recebido o código de erro [".$resposta3['err_or_version']."]\n";
                    echo "data: ".$resposta3['data']."\n";
                }
                else{
                    $params3 = explode(']', $resposta3['data']);
                    if( $params3[0] == '1' ) { // a catraca deve retornar 1 para sucesso
                        echo "catraca liberada";
                    }
                    else{
                        echo "a catraca não autorizou a liberação. código retornado [".$params3[0]."]";
                    }
                }
            }
        }
    }
}
?>