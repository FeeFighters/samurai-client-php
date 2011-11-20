<?php

/* 
 * The `Samurai_Connection` class is responsible for making requests to 
 * the Samurai API and parsing the returned response.
 */
class Samurai_Connection 
{
	private static $instance;

	public static function instance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
	}

	/*
   * Convenience method for making GET requests.
	 */
	public function get($path, $data = array()) {
		return $this->request('GET', $path, $data);
	}

	/*
   * Convenience method for making POST requests.
	 */
	public function post($path, $data = array()) {
		return $this->request('POST', $path, $data);
	}

	/*
   * Convenience method for making PUT requests.
	 */
	public function put($path, $data = array()) {
		return $this->request('PUT', $path, $data);
	}

	/*
   * Performs a GET/POST/PUT request to the Samurai API, parses the returned response
	 * and then returns it.
	 */
  private function request($method, $path, $data = array()) {
		$headers = array();
		$headers[] = 'Accept: application/json,text/json,application/xml,text/xml';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, Samurai::$site . $path);
    curl_setopt($ch, CURLOPT_USERAGENT, "FeeFighters Samurai PHP Client v" . Samurai::VERSION);
    curl_setopt($ch, CURLOPT_USERPWD, Samurai::$merchantKey . ':' . Samurai::$merchantPassword);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

		$dataString = self::prepareData($data);
		switch($method) {
			case 'POST':
				$headers[] = 'Content-Length: ' . strlen($dataString);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
				break;
			case 'PUT':
				$headers[] = 'Content-Length: ' . strlen($dataString);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
				break;
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//echo "-- request with data: \n", $dataString;
		//echo "-- request to: \n", Samurai::$site . $path;

    $res = curl_exec($ch);
		list($header, $body) = explode("\r\n\r\n", $res, 2);

		//echo "\n--------- Response ----------\n";
		//print_r($header);
		//print_r($body);
		//echo "\n--------- /Response ----------\n";

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = self::errorFromStatus($statusCode);

		$match = null;
		preg_match('/Content-Type: ([^;]*);/i', $header, $match);
		$contentType = $match[1];

		// Parse response, depending on value of the Content-Type header.
		$response = null;
		if (preg_match('/json/', $contentType)) {
			$response = json_decode($body, true); 
		} elseif (preg_match('/xml/', $contentType)) {
			$response = Samurai_XmlParser::parse($body);
		}

		return array($error, $response);
  }

	/*
	 * Prepares a `$data` array for posting to the API. Flattens the array 
	 * in such a way that nested arrays beyond the first nesting level 
	 * will be converted to JSON strings. The resulting array will be 
	 * converted to query string form.
	 */
	private static function prepareData($data = array()) {
		// JSON encode arrays more than one level deep
		foreach ($data as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $kk => $vv) {
					if (is_array($vv)) {
						$data[$k][$kk] = json_encode($data[$k][$kk]);
					}
				}
			}
		}

		$dataString = http_build_query($data, null, '&');
		return $dataString;
	}

	/*
   * Returns an error object, corresponding to the HTTP status code returned by Samurai.
	 */
	private static function errorFromStatus($status) {
    switch ($status) {
			case '200': 
				return null;
			case '400':
				return new Samurai_BadRequestError();
			case '401': 
				return new Samurai_AuthenticationRequiredError();
			case '403': 
				return new Samurai_AuthorizationError();
			case '404': 
				return new Samurai_NotFoundError();
			case '406': 
				return new Samurai_NotAcceptableError();
			case '500': 
				return new Samurai_InternalServerError();
			case '503': 
				return new Samurai_DownForMaintenanceError();
			default: 
				return new Samurai_UnexpectedError('Unexpected HTTP response: ' . $status);
		}
	}
}
