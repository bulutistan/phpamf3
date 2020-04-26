<?php 

namespace SAML;


//unlink( dirname(__FILE__).'/cookie.txt');


/**
 * SAML Authorization...
 */
class SAMLAuth
{
    /**
     * SAML 2.0 Authorization for vCenter....
     */

    private $curl;
    private $tmpfname;

    private $url;
    private $referer;
    private $response;
    private $castle_session;

    private $user;
    private $pass;

    public $proxy = null;


    public function __construct($url, $user, $pass, $cookie_path='/tmp/cookie.txt')
    {
    	$this->url = $url;
    	$this->user = $user;
    	$this->pass = $pass;

        $this->tmpfname = $cookie_path;
		//unlink($this->tmpfname);

		$this->curl = curl_init();
        
    }

    public function kickStart()
    {
        $this->Start("?csp", false);
        $this->Start("logon");
        $this->Check();
        $this->Login($this->user, $this->pass);
        
        return $this->SendSaml("saml/websso/sso");
        //$this->Token("");
    }

    private function Start($path, $is_referrer=true)
    {
	    $options = array(
			CURLOPT_URL            => $this->url.$path,
	        CURLOPT_HEADER         => 1,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_FOLLOWLOCATION => 0,
	        CURLOPT_NOBODY         => 1,
	        CURLOPT_USE_SSL        => false,
	        CURLOPT_SSL_VERIFYHOST => false,
	        CURLOPT_SSL_VERIFYPEER => false,    // for https
	        CURLOPT_POSTFIELDS     => null,
	        CURLOPT_CUSTOMREQUEST  => 'GET',
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_COOKIEJAR      => $this->tmpfname,
			CURLOPT_COOKIEFILE     => $this->tmpfname,
			CURLOPT_VERBOSE        => 1,
			CURLOPT_PROXY          => $this->proxy
		);

		$ret = $this->SendCurl($options);

		//print_r($ret);

		if($is_referrer) {
			preg_match_all('/Location:\s*([^ ]*)/mi', $ret, $matches);

			//var_dump($matches);

			$retval = str_replace("Content-Length:", "", $matches[1][0]);

			$retval = str_replace("\r", "", $retval);
			$retval = str_replace("\n", "", $retval);

			$this->referer = $retval;
		}
    }

    private function Check()
    {

    	$header = array(
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)Chrome/77.0.3865.120 Safari/537.36',
			'Accept-Encoding: gzip, deflate, br',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
			'Connection: keep-alive',
			'Sec-Fetch-Mode: nested-navigate',
			'Content-type: application/x-www-form-urlencoded',
			'Sec-Fetch-Site: same-site',
			'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
			'Upgrade-Insecure-Requests: 1',
			'Referer: '.$this->url.'?csp',	
		);

	    $options = array(
			CURLOPT_URL            => $this->referer,
			CURLOPT_HTTPHEADER     => $header,
	        CURLOPT_HEADER         => 1,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_FOLLOWLOCATION => 0,
	        CURLOPT_NOBODY         => 1,
	        CURLOPT_USE_SSL        => false,
	        CURLOPT_SSL_VERIFYHOST => false,
	        CURLOPT_SSL_VERIFYPEER => false,    // for https
	        CURLOPT_POSTFIELDS     => null,
	        CURLOPT_CUSTOMREQUEST  => 'GET',
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_COOKIEJAR      => $this->tmpfname,
			CURLOPT_COOKIEFILE     => $this->tmpfname,
			CURLOPT_VERBOSE        => 1,
			CURLOPT_PROXY          => $this->proxy
		);

		$ret = $this->SendCurl($options);

		//print_r($ret);
    }

    private function Login($user,$pass)
    {
    	$unp = base64_encode($user.":".$pass);

    	$data = http_build_query(array("CastleAuthorization"=>"Basic ".$unp));
		//$data = "CastleAuthorization=Basic%20".$unp;

    	$header = array(
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)Chrome/77.0.3865.120 Safari/537.36',
			'Accept-Encoding: gzip, deflate, br',
			'Accept: */*',
			'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
			'Connection: keep-alive',
			'Authorization: Basic '.$unp,
			'Pragma: no-cache',
			'Cache-Control: no-cache',
			'Sec-Fetch-Mode: cors',
			'Content-type: application/x-www-form-urlencoded',
			'Sec-Fetch-Site: same-origin',
			'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
			//'Content-Length: 80'
		);

		$bulk = "";

		$curl_chunk = curl_init();
	    
	    $options = array(
			CURLOPT_URL            => $this->referer,
			CURLOPT_HTTPHEADER     => $header,
	        CURLOPT_HEADER         => 1,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_FOLLOWLOCATION => 0,
	        CURLOPT_NOBODY         => 0,
	        CURLOPT_USE_SSL        => false,
	        CURLOPT_SSL_VERIFYHOST => false,
	        CURLOPT_SSL_VERIFYPEER => false,    // for https
	        CURLOPT_POSTFIELDS     => $data,
			CURLOPT_TIMEOUT        => 5,
			CURLOPT_CONNECTTIMEOUT => 20,
			//CURLOPT_ENCODING       => "ISO-8859-1",
			CURLOPT_COOKIEJAR      => $this->tmpfname,
			CURLOPT_COOKIEFILE     => $this->tmpfname,
			CURLOPT_VERBOSE        => 1,
			CURLOPT_ENCODING       => "UTF-8",
			CURLOPT_PROXY          => $this->proxy,
			CURLOPT_WRITEFUNCTION  => function(&$curl_chunk, $rets) use (&$bulk) {
				$bulk .= $rets;
				return strlen($rets);
			}
		);

		curl_setopt_array($curl_chunk, $options);

		$ret = curl_exec($curl_chunk);

		$header_size = curl_getinfo($curl_chunk, CURLINFO_HEADER_SIZE);

		//curl_close($curl_chunk);

		//echo "\n";

		//var_dump($bulk);

		$body = substr($bulk, $header_size);

		preg_match_all('/value="\s*([^\"]*)"/mi', $body, $matches);

		//var_dump($matches);

		$return = $matches[1][0];

		$return = str_replace("\r", "", $return);
		$return = str_replace("\n", "", $return);

		//var_dump($return);

		$header = substr($bulk, 0, $header_size);

		preg_match_all('/Set-Cookie: CastleSessionvsphere.local=\s*([^ ]*);/mi', $header, $matchesh);

		//var_dump($matchesh);

		$returnh = $matchesh[1][0];

		$returnh = str_replace("\r", "", $returnh);
		$returnh = str_replace("\n", "", $returnh);

		//var_dump($returnh);

		$this->response = $return;
		$this->castle_session = $returnh;
    }

    private function SendSaml($path)
    {
    	$data = http_build_query(array("SAMLResponse"=>$this->response));

    	$header = array(
			'Connection: keep-alive',
			'Cache-Control: max-age=0',
			'Content-Type: application/x-www-form-urlencoded',
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
			'Accept: */*',
			'Referer: '.$this->referer,
			'Accept-Encoding: gzip, deflate, br',
			'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
			'Cookie: CastleSessionvsphere.local='.$this->castle_session
		);

		$options = array(
			CURLOPT_URL            => $this->url.$path,
			CURLOPT_HTTPHEADER     => $header,
	        CURLOPT_HEADER         => 1,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_FOLLOWLOCATION => 1,
	        CURLOPT_NOBODY         => 0,
	        CURLOPT_USE_SSL        => false,
	        CURLOPT_SSL_VERIFYHOST => false,
	        CURLOPT_SSL_VERIFYPEER => false,    // for https
	        CURLOPT_POSTFIELDS     => $data,
	        CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_TIMEOUT        => 5,
			CURLOPT_CONNECTTIMEOUT => 20,
	        //CURLOPT_POST           => 1,
			CURLOPT_COOKIEJAR      => $this->tmpfname,
			CURLOPT_COOKIEFILE     => $this->tmpfname,
  			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_0,
			//CURLOPT_COOKIESESSION  => true,
			CURLOPT_VERBOSE        => 1,
			CURLOPT_PROXY          => $this->proxy
		);

		$ret = $this->SendCurl($options);

		$header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);

		$header = substr($ret, 0, $header_size);

		preg_match_all('/Set-Cookie:\s*([^ ]*);/mi', $header, $matchesh);

		//var_dump($matchesh);

		$return = [];

		foreach ($matchesh[1] as $key => $value) {
			$returnh = str_replace("\r", "", $value);
			$returnh = str_replace("\n", "", $returnh);

			array_push($return, $returnh);
		}
		
		//var_dump($return);

		return $return;
    }

    private function Token($path)
    {
    	$header = array(
			'Connection: keep-alive',
			'Cache-Control: max-age=0',
			'Content-Type: application/x-www-form-urlencoded',
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*',
			'Referer: '.$this->referer,
			'Accept-Encoding: gzip, deflate, br',
			'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
			'Upgrade-Insecure-Requests: 1',
			
		);

		$options = array(
			CURLOPT_URL            => $this->url.$path,
	        CURLOPT_HTTPHEADER     => $header,
	        CURLOPT_HEADER         => 1,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_FOLLOWLOCATION => 1,
	        CURLOPT_NOBODY         => 0,
	        CURLOPT_USE_SSL        => false,
	        CURLOPT_SSL_VERIFYHOST => false,
	        CURLOPT_SSL_VERIFYPEER => false,    // for https
	        CURLOPT_POSTFIELDS     => null,
	        CURLOPT_CUSTOMREQUEST  => 'GET',
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_COOKIEJAR      => $this->tmpfname,
			CURLOPT_COOKIEFILE     => $this->tmpfname,
			CURLOPT_VERBOSE        => 1,
			CURLOPT_PROXY          => $this->proxy
		);

		$ret = $this->SendCurl($options);

    }

    private function SendCurl($options)
    {
    	curl_setopt_array($this->curl, $options);
	
		$ret = curl_exec($this->curl);

		$err = curl_error($this->curl);

		//var_dump($err);

		return $ret;
    }

    public function __destruct()
    {
    	//curl_close($this->curl);

    	//unset($this->curl);
    }
}


 ?>