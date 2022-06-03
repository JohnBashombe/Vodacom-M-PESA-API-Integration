<?php

include('./sdk/PortalSDK/api.php');

class  VodacomServices
{
	protected static $port = 443;
	protected static $base_url = 'openapi.m-pesa.com';
	private static $private_key = "CsuwfFJRsoUOBpNTtdeCHg4qYRTWWEAr";
	private static $get_session = '/sandbox/ipg/v2/vodacomTZN/getSession/';
	protected static $public_key = 'MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEArv9yxA69XQKBo24BaF/D+fvlqmGdYjqLQ5WtNBb5tquqGvAvG3WMFETVUSow/LizQalxj2ElMVrUmzu5mGGkxK08bWEXF7a1DEvtVJs6nppIlFJc2SnrU14AOrIrB28ogm58JjAl5BOQawOXD5dfSk7MaAA82pVHoIqEu0FxA8BOKU+RGTihRU+ptw1j4bsAJYiPbSX6i71gfPvwHPYamM0bfI4CmlsUUR3KvCG24rB6FNPcRBhM3jDuv8ae2kC33w9hEq8qNB55uw51vK7hyXoAa+U7IqP1y6nBdlN25gkxEA8yrsl1678cspeXr+3ciRyqoRgj9RD/ONbJhhxFvt1cLBh+qwK2eqISfBb06eRnNeC71oBokDm3zyCnkOtMDGl7IvnMfZfEPFCfg5QgJVk1msPpRvQxmEsrX9MQRyFVzgy2CWNIb7c+jPapyrNwoUbANlN8adU1m6yOuoX7F49x+OjiG2se0EJ6nafeKUXw/+hiJZvELUYgzKUtMAZVTNZfT8jjb58j8GVtuS+6TM2AutbejaCV84ZK58E2CRJqhmjQibEUO6KPdD7oTlEkFy52Y1uOOBXgYpqMzufNPmfdqqqSM4dU70PO8ogyKGiLAIxCetMjjm6FCMEA3Kc8K0Ig7/XtFm9By6VxTJK1Mg36TlHaZKP6VzVLXMtesJECAwEAAQ==';

	public static function generateSessionKey()
	{
		// Create Context with API to request a SessionKey
		$context = new APIContext();
		// Api key
		$context->set_api_key(self::$private_key);
		// Public key
		$context->set_public_key(self::$public_key);
		// Use ssl/https
		$context->set_ssl(true);
		// Method type (can be GET/POST/PUT)
		$context->set_method_type(APIMethodType::GET);
		// API address
		$context->set_address(self::$base_url);
		// API Port
		$context->set_port(self::$port);
		// API Path
		$context->set_path(self::$get_session);

		// Add/update headers
		$context->add_header('Origin', '*');
		// Parameters can be added to the call as well that on POST will be in JSON format and on GET will be URL parameters
		// context->add_parameter('key', 'value');

		// Create a request object
		$request = new APIRequest($context);

		// print_r($request);
		// Do the API call and put result in a response packet
		$response = null;

		try {
			$response = $request->execute();
			// print_r($response);
		} catch (exception $e) {
			// echo 'Call failed: ' . $e->getMessage() . '<br>';
			return null;
		}

		if ($response->get_body() == null) {
			// throw new Exception('SessionKey call failed to get result. Please check.');
			return null;
		}

		return $response;
	}
}
