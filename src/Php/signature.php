<?php
/**
* Compute headers.signature of an HTTPS request
* @param $retailerSecretKey retailer Secret API Key 
* @param $endpoint called sample: "/api/v1/pay/Order"
* @param $payload  body of POST request
* @returns signature
*/
function signature(string $retailerSecretKey, string $endpoint, array $payload) {
        $time = (string)time(); 
        // Unix timestamp convert to string : https://www.unixtimestamp.com/
        // https://www.php.net/manual/fr/function.time.php
        $data = [
            'date' => $time ,
            'method' => 'POST',
            'endpoint' => "/api/v1/{$endpoint}",
            'payload' => $payload
        ];
        // https://stackoverflow.com/questions/18441180/phps-json-encode-and-jss-json-stringify
        $json_data = json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        // https://www.php.net/manual/en/function.hash-hmac.php
        $generated_hash = hash_hmac('sha256', $json_data, $retailerSecretKey, true);
        // https://www.php.net/manual/en/function.base64-encode.php
        $encode = base64_encode($generated_hash);
        return "{$time}:{$encode}";
}
