<?php

require "Payment.php";

if (isset($_POST['token']) && (trim($_POST['token']) != '')) {

    $token = $_POST['token'];

    $payment = new Payment($token, 's1Gateway');

    $response = json_decode($payment->executePayment(), true);

    /*
     * 1 - Obtengo la respuesta del pago
     * 2 - Persisto en la tabla de transacciones
     * 3 - Retorno resultado
     */

    $response = array_reverse($response);

} else {

    $response = array(
        'status' => 'Error',
        'message' => 'Error al querer obtener el Token de Pago .'
    );
}

ob_end_clean();

header('Content-Type: application/json');

$json = json_encode($response);

die($json);
