<?php

class Binance
{

    private $URL = 'https://api.binance.com';

    private $API_KEY = '';
    private $SECRET = '';

    public function __construct()
    {
    }

    private function simpleGetQuery($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->URL.$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = json_decode(curl_exec($ch));

        curl_close($ch);

        return json_encode($output, JSON_PRETTY_PRINT);
    }

    private function postSignedQuery($url, array $params)
    {
    }

    private function getQuery($url, $params)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->URL.$url.'?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-MBX-APIKEY: '.$this->API_KEY
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = json_decode(curl_exec($ch));

        curl_close($ch);

        return json_encode($output, JSON_PRETTY_PRINT);

    }


    private function getSignedQuery($url, $params)
    {

        $signature = hash_hmac("sha256", http_build_query($params), $this->SECRET);

        array_push($params, ['signature' => $signature]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->URL.$url.'?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-MBX-APIKEY: '.$this->API_KEY
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = json_decode(curl_exec($ch));

        curl_close($ch);

        return json_encode($output, JSON_PRETTY_PRINT);

    }

    public function test()
    {
        return $this->simpleGetQuery("/api/v1/ping");
    }

    public function time()
    {
        return $this->simpleGetQuery("/api/v1/time");
    }

    public function exchangeInfo()
    {
        return $this->simpleGetQuery("/api/v1/exchangeInfo");
    }

    public function order()
    {
        return $this->getSignedQuery("/api/v3/order/test", [
            'symbol' => 'BTC',
            'side' => 'SELL',
            'type' => 'MARKET',
            'quantity' => '0.0001',
            'timestamp' => time()
        ]);
    }

    public function accountInfo()
    {
        return $this->getQuery("/api/v3/account", [
            'timestamp' => time()
        ]);
    }

    public function tradeHistory()
    {
        return $this->getQuery("/api/v1/historicalTrades", [
            'symbol' => "LTCBTC"
        ]);
    }
}

$binance = new Binance();

echo $binance->order();
