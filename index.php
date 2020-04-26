<?php 


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);

define('__BASE_DIR__', __DIR__);


require 'vendor/autoload.php';

require 'vCenter/autoload.php';

include 'login.php';


use SabreAMF\Message;
use SabreAMF\InputStream;
use SabreAMF\Client;
use SabreAMF\TypedObject;
use SabreAMF\AMF3\Deserializer;


use Messages\First;
use Messages\ConfigurationService;
use Messages\BackendLogService;
use Messages\UserSessionServiceInternal;
use Messages\ExtensionService;
use Messages\PersistenceService;
use Messages\DataService;

use Messages\Bodies\CompositeConstraint;
use Messages\Bodies\Conjoiner;
use Messages\Bodies\ManagedObjectReference;
use Messages\Bodies\ObjectIdentityConstraint;
use Messages\Bodies\PersistenceScope;
use Messages\Bodies\PropertySpec;
use Messages\Bodies\QuerySpec;
use Messages\Bodies\QuerySpecOptions;
use Messages\Bodies\RequestSpec;
use Messages\Bodies\ResourceSpec;
use Messages\Bodies\ResultSpec;
use Messages\Bodies\SearchCondition;
use Messages\Bodies\SearchCriteria;

use SAML\SAMLAuth;


header("Content-Type: text/plain");

echo "ok\n";

$cookieFile = __DIR__.'/cookie.txt';

$saml = new SAMLAuth($url, $user, $pass, $cookieFile);
$saml->proxy = '10.34.21.31:8888';

$cookie  = "";
$cookies = $saml->kickStart();


function make_client($url_path, $cookies, &$endpoint, &$domain)
{
	preg_match_all("/https:\/\/(.*?)\/(.*)/", $url_path, $parsed_url);
	$endpoint = (count($parsed_url[2]) > 0) ? '/'.$parsed_url[2][0] : null;
	$domain  = (count($parsed_url[1]) > 0) ? '/'.$parsed_url[1][0] : "";


	$client = new SabreAMF_Client($url_path);
	
	$client->addHTTPHeader('Connection: keep-alive');
	$client->addHTTPHeader('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36');
	$client->addHTTPHeader('Accept: */*');
	$client->addHTTPHeader('Connection: keep-alive');
	//$client->addHTTPHeader('Content-Type: application/x-amf; charset=ISO-8859-1');
	$client->addHTTPHeader('Content-Type: application/x-amf');
	$client->addHTTPHeader('Origin: https://'.$domain);
	$client->addHTTPHeader('X-Requested-With: ShockwaveFlash/32.0.0.330');
	$client->addHTTPHeader('Sec-Fetch-Site: same-origin');
	$client->addHTTPHeader('Sec-Fetch-Mode: no-cors');
	$client->addHTTPHeader('Referer: https://'.$domain.'/vsphere-client/UI.swf/[[DYNAMIC]]/6');
	$client->addHTTPHeader('Accept-Encoding: gzip, deflate, br');
	//$client->addHTTPHeader('Content-Encoding: ISO-8859-1');
	$client->addHTTPHeader('Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7');

	if(is_array($cookies)) {
		foreach ($cookies as $value) {
			$client->addHTTPCookie($value);
		}
	} else {
		$client->addHTTPCookie($cookies);
	}

	$client->httpProxy = '10.34.21.31:8888';

	//$client->cookieFile = $cookieFile;

	return $client;
}



$endpoint1 = null;
$domain1 = null;
/* create client 1 */
$client = make_client($url."endpoints/messagebroker/amf", $cookies, $endpoint1, $domain1);


/* first message 1.1 */
$object = (new First())->makeHeader()->makeBody()->getObject();

$result = $client->sendRequest('null', [$object], "/1");

$dsId = $result->headers["DSId"];


/* first message 1.2 */
$object = (new First())->makeHeader($dsId)->makeBody()->getObject();

$result = $client->sendRequest('null', [$object], "/1");


/* info message */
$make_body  = "[Flex|AppErrorHandler]loaderURL = https://".$domain1."/vsphere-client/UI.swf/[[DYNAMIC]]/39, ";
$make_body .= "url = https://".$domain1."/vsphere-client/UI.swf/[[DYNAMIC]]/39, swfVersion = 18";

$object = (new BackendLogService("info"))->makeHeader($endpoint1, $dsId)->makeBody([$make_body])->getObject();

$result = $client->sendRequest('null', [$object], "/2");

$cookies_new = $cookies; //$client->cookies;


/* create client 2 */
$endpoint2 = null;
$domain2 = null;
$client2 = make_client($url."messagebroker/amf", $cookies_new, $endpoint2, $domain);


/* first message 2.1 */
$client2->setEncoding(0);
$object = (new First())->makeHeader()->makeBody()->getObject();

$result = $client2->sendRequest('null', [$object], "/1");


/* get configuration message */
$object = (new ConfigurationService("getConfiguration"))->makeHeader($endpoint1, $dsId)->makeBody([])->getObject();

$result = $client->sendRequest('null', [$object], "/2");


/* get user session message */
$object = (new UserSessionServiceInternal("getUserSession"))->makeHeader($endpoint2, $dsId)->makeBody([])->getObject();

$result = $client2->sendRequest('null', [$object], "/2");


// parse session key...
$serverGuid = $result->body->getAMFData()["serversInfo"][0]->getAMFData()["serviceGuid"]; // ->getAMFClassName() -  getAMFData()


$sessionKey = $result->body->getAMFData()["serversInfo"][0]->getAMFData()["sessionKey"]; // ->getAMFClassName() -  getAMFData()
//$sessionKey = $result->body->getAMFData()["clientId"]; // ->getAMFClassName() -  getAMFData()

$sessionKey = strtoupper($sessionKey);

var_dump($sessionKey);

$generated_client_id = strtoupper(uuid_create());


/* info message */
$make_body  = "[Flex|AppErrorHandler]The session timeout is 120 minutes.";

$object = (new BackendLogService("info"))->makeHeader($endpoint1, $dsId)->makeBody([$make_body])
	->setClientId($generated_client_id)->getObject();

$result = $client->sendRequest('null', [$object], "/3");


/* info message */
$make_body  = "[Flex] initializeCommonApp";

$object = (new BackendLogService("debug"))->makeHeader($endpoint1, $dsId)->makeBody([$make_body])
	->setClientId($generated_client_id)->getObject();

$result = $client->sendRequest('null', [$object], "/4");


/* get user session message */
$object = (new UserSessionServiceInternal("initSession"))->makeHeader($endpoint2, $dsId)->makeBody([])->setClientId($sessionKey)->getObject();

$result = $client2->sendRequest('null', [$object], "/3");

//var_dump($result);

// "wss://vc.vpshere.local/vsphere-client/endpoints/live-updates?webClientSessionId=6c4b7a62-xxxx-4346-b7ed-xxxxxxxxxxxx"
// "wss://vc.vpshere.local/vsphere-client/endpoints/live-updates?webClientSessionId=".$sessionKey
// 2f84ac170f99cc8e7a18f95a295b4d16 : L4SsFw+ZzI56GPlaKVtNFg==


$cookies_new = $client->cookies;

/* create client 3 */
$endpoint3 = null;
$domain3 = null;
$client3 = make_client($url."endpoints/messagebroker/ds-core-amf", $cookies_new, $endpoint3, $domain);


/* first message 2.1 */
$client3->setEncoding(0);
$object = (new First())->makeHeader($dsId)->makeBody()->getObject();

$result = $client3->sendRequest('null', [$object], "/1");




/* first message 1.3 */
$client->setEncoding(0);

$object = (new First())->makeHeader($dsId)->makeBody()->getObject();

$result = $client->sendRequest('null', [$object], "/1");


/**

	TODO:
	- DataService
	- getData

 /


$generated_operation_id = strtoupper(uuid_create());

$requestSpec = (new RequestSpec(
	[
		(new QuerySpec(
			(new ResultSpec())->getObject(),
			(new QuerySpecOptions()),
			(new ResourceSpec(
				(new CompositeConstraint(
					[
						(new ObjectIdentityConstraint(
							(new ManagedObjectReference(
								$serverGuid.":Folder:group-d1",
								"Folder",
								"group-d1",
								$serverGuid
							))->getObject(),
							"Folder"
						))->getObject()
					],
					(new Conjoiner())->getObject()
				))->getObject(),
				[(new PropertySpec())->getObject()],
			))->getObject(),
			"dam-auto-generated: IMediator:dr-1"
		))->getObject()
	],
	false
))->getObject();

$client3->setEncoding(3);

$object = (new DataService("getData"))
	->makeHeader($endpoint3, $dsId, $sessionKey, $generated_operation_id."-1")
	->makeBody([$requestSpec])
	->getObject();

$result = $client3->sendRequest('null', [$object], "/2", false);

var_dump($result);

/* get plugins message /
$object = (new ExtensionService("getPluginInfos"))->makeHeader($endpoint1, $dsId, $sessionKey)->makeBody([])->getObject();

$result = $client->sendRequest('null', [$object], "/2");

var_dump($result);

/* first message 1.4 /
$client->setEncoding(0);

$object = (new First())->makeHeader($dsId)->makeBody()->getObject();

$result = $client->sendRequest('null', [$object], "/1");

/* query user data message /
$client->setEncoding(3);
$condition1 = (new SearchCondition("com.vmware.usersettings.category.uid", "com.vmware.usersettings.gettingStarted"))->getObject();
$condition2 = (new SearchCondition("com.vmware.usersettings.category.uid", "com.vmware.usersettings.toolbarActions"))->getObject();
$search = (new SearchCriteria([$condition1, $condition2], false))->getObject();

$object = (new PersistenceService("queryUserData"))->makeHeader($endpoint1, $dsId, $sessionKey)->makeBody([
	"usersettings", $search, ((new PersistenceScope())->getObject())
])->getObject();

$result = $client->sendRequest('null', [$object], "/2", false);

var_dump($result);

/* */



 ?>