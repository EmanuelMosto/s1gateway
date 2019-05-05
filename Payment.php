<?php

class Payment
{
    private $token;
    private $site_transaction_id;

    /**
     * Payment constructor.
     * @param $token
     * @param $site_transaction_id
     */
    function __construct($token, $site_transaction_id)
    {
        $this->token = $token;
        $this->site_transaction_id = $this->generateDinamicId($site_transaction_id);
    }

    /**
     * Obtengo un id_site único por cada pago
     * @param $site_transaction_id
     * @return string
     */
    private function generateDinamicId($site_transaction_id)
    {
        $time = time();
        $time = substr($time, '5', 5);

        return $site_transaction_id . '-' . $time;
    }


    /**
     * Armo la Data y llamo al servicio para consumir la Api
     * @return mixed
     */
    public function executePayment()
    {
        $data = array(
            "site_transaction_id" => $this->site_transaction_id,
            "token" => $this->token,
            "customer" => array("id" => "customer", "email" => "user@mail.com"),
            "payment_method_id" => 1,
            "bin" => "450799",
            "amount" => 5.00,
            "currency" => "ARS",
            "installments" => 1,
            "description" => "",
            "establishment_name" => "S1gateway",
            "payment_type" => "single",
            "sub_payments" => array(),
            "fraud_detection" => array()
        );

        try {

            $response = $this->restService($data);

            //log success

        } catch (Exception $e) {

            //log error

        }

        return $response;

    }


    private function restService($data){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://developers.decidir.com/api/v2/payments",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "apikey: 92b71cf711ca41f78362a7134f87ff65",
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }

    }

}

