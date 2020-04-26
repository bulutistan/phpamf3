<?php 


function request($url, $data)
{
    $curl = curl_init();

    curl_setopt($curl, CURLAUTH_DIGEST, 1);
    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_NOBODY,0);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($curl, CURLOPT_USE_SSL,0);

    curl_setopt($curl, CURLOPT_PROXY, $proxy);

    $tmpfname = dirname(__FILE__).'/cookie.txt';
    curl_setopt($curl, CURLOPT_COOKIEJAR, $tmpfname);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $tmpfname);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

    curl_setopt($curl, CURLOPT_VERBOSE,$verb);

    if($data != "")
    {

        curl_setopt($curl,CURLOPT_POST,1);

        if($data != "-")
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
    }

    $ret = curl_exec($curl);

    curl_close($curl);

    return $ret;
}


 ?>