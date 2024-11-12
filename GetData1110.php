<?php
class GetData1110 {
    /**
     * Http post
     * @param  $url
     * @param  $data
     * @param  $useAgent
     */
    private function post($url, $data)
    {
        if (!is_array($data))
        {
            $data = array($data);
        }
        $data = http_build_query($data);

        if (!function_exists("curl_init"))
        {
            die('undefined function curl_init');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);

        /**************測試環境先不驗證ssl準確性**************/
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        /**************測試環境先不驗證ssl準確性**************/
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36");
        $rs = curl_exec($ch);
        curl_close($ch);
        return $rs;
    }

    private function enCode($str,$privateFilePath,$publicFilePath) {

        $privateKeyContent = file_get_contents($privateFilePath);
        $privateKeyContent = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($privateKeyContent, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        $privateKey = openssl_pkey_get_private($privateKeyContent);

        $publicKeyContent = file_get_contents($publicFilePath);
        // $publicKeyContent = "-----BEGIN RSA PUBLIC KEY-----\n" .
        //     wordwrap($publicKeyContent, 64, "\n", true) .
        //     "\n-----END RSA PUBLIC KEY-----";

        $publicKey = openssl_pkey_get_public($publicKeyContent);

        $keyLen = openssl_pkey_get_details($publicKey)['bits'];

        $encrypted = '';
        $part_len = $keyLen / 8 - 11;
        $parts = str_split($str, $part_len);

        foreach ($parts as $part) {
            $encrypted_temp = '';
            openssl_private_encrypt($part, $encrypted_temp, $privateKey);
            $encrypted .= $encrypted_temp;
        }

        return base64_encode($encrypted);

    }

    private function getSignStr($data) {
        ksort($data);
        $result = array();
        foreach ($data as $key => $value) {
            $result []= $key.'='.$value;
        }
        return implode('&',$result);
    }

    private function sign($str,$privateFilePath) {
        $privateKey = file_get_contents($privateFilePath);
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($privateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        $piKey = openssl_pkey_get_private($privateKey);
        if(!$piKey) {
            return '';
        }
        openssl_sign($str, $signature, $piKey, "SHA256");
        $sign = base64_encode($signature);
        return $sign;
    }

    private function deCode($str,$privateFilePath,$publicFilePath) {
        $privateKeyContent = file_get_contents($privateFilePath);
        $privateKeyContent = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($privateKeyContent, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        $privateKey = openssl_pkey_get_private($privateKeyContent);

        $publicKeyContent = file_get_contents($publicFilePath);
        // $publicKeyContent = "-----BEGIN RSA PUBLIC KEY-----\n" .
        //     wordwrap($publicKeyContent, 64, "\n", true) .
        //     "\n-----END RSA PUBLIC KEY-----";
        //     print_r($publicKeyContent);
        try{
            $publicKey = openssl_pkey_get_public($publicKeyContent);
            // var_dump($publicKey);
        }catch (Exception $e) {
            print_r($e);
            
        }
        //echo openssl_error_string();
         
        $keyLen = openssl_pkey_get_details($publicKey)['bits'];

        $decrypted = "";
        $part_len = $keyLen / 8;
        $base64_decoded = base64_decode($str);
        $parts = str_split($base64_decoded, $part_len);

        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_private_decrypt($part, $decrypted_temp,$privateKey);
            $decrypted .= $decrypted_temp;
        }
        return $decrypted;

    }


    public function getData($privateKeyFilePath,$publicKeyFilePath,$envMark,$day = '') {
        $urlArr = array(
            'zc_dev' => 'http://10.1.237.214:9991/engine/rest/getZcToday', //招租-电子政务网
            'zc_prod' => 'https://www.cdggzy.com/engine/rest/getZcToday',//招租-互联网
            'zs_dev' => 'http://10.1.237.214:9991/engine/rest/getZsToday', //招商-电子政务网
            'zs_prod' => 'https://www.cdggzy.com/engine/rest/getZsToday', //招商-互联网
        );
        if (!isset($urlArr[$envMark])) {
            return array();
        }
        $url = $urlArr[$envMark];
        $bizContent = array(
            'pbtime' => $day,
            'paging' => '0',
            'access_key' => 'c06bb1b995385544f761b3036f1aceb8'
        );
        if (empty($bizContent['pbtime'])) {
            $bizContent['pbtime'] = date('Y-m-d',time() - 86400);
        }
        
        $bizContent = $this->enCode(json_encode($bizContent),$privateKeyFilePath,$publicKeyFilePath);
        $requestId = uniqid('', true);
        $requestId = str_replace('.','',$requestId);
        $requestId = substr($requestId,0,20);
        $params = array(
            'biz_content' => $bizContent,
            'access_key' => 'c06bb1b995385544f761b3036f1aceb8',
            'format' => 'json',
            'request_id' => $requestId,
            'timestamp' => time() * 1000,
            'version' => '1.0',
        );
        $sign = $this->getSignStr($params);
        //签名
        $params['sign'] = $this->sign($sign,$privateKeyFilePath);
        $response = $this->post($url,$params);
 
        $response = $this->deCode($response,$privateKeyFilePath,$publicKeyFilePath);
        
        $result = json_decode($response,true);
        if ($result['code'] == '1') {
            return $result['biz_data']['rows'];
        }
        return array();
    }
}