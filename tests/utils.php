<?php
if (!function_exists('request')) {
    function request($url, $data = [], $method = 'POST', $cookies = '') {
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => false,
        ];
        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;
        }
        if ($cookies) $options[CURLOPT_COOKIE] = $cookies;

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        curl_close($ch);

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
        $newCookies = $matches[1] ?? [];

        return [
            'headers' => $headers,
            'cookies' => implode('; ', $newCookies),
            'body' => $body,
            'json' => json_decode($body, true),
        ];
    }
}

if (!function_exists('check')) {
    function check($res, $name) {
        if ($res['json']['success'] ?? false) {
            echo "✅ $name PASÓ\n";
        } else {
            echo "❌ $name FALLÓ\n";
            echo "   Respuesta: " . json_encode($res['json']) . "\n";
        }
    }
}