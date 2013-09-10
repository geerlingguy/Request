<?php

/**
 * The Request class provides a simple HTTP request interface.
 *
 * Usage examples can be found in the included README file, and all methods
 * should have adequate documentation to get you started.
 *
 * Minimum requirements: PHP 5.3.x, cURL.
 *
 * @version 1.0
 * @author Jeff Geerling (geerlingguy).
 */

namespace JJG\Request;

class Request {
  // You can set the address when creating the Request object, or using the
  // setAddress() method.
  private $address;

  // Variables used for the request.
  public $enableSSL = FALSE;
  public $userAgent = 'Mozilla/5.0 (compatible; PHP Request library)';
  public $connectTimeout = 10;
  public $timeout = 15;

  // If you enable cookies, be sure to set a path to the cookies TXT file.
  private $enableCookies = FALSE;
  private $cookiePath;

  // Request type.
  public $requestType;
  // Userpwd value used for basic HTTP authentication.
  private $userpwd;
  // Latency, in ms.
  private $latency;
  // HTTP response body.
  private $responseBody;
  // HTTP response header.
  private $responseHeader;
  // HTTP response status code.
  private $httpCode;
  // cURL error.
  private $error;

  /**
   * Called when the Request object is created.
   */
  public function __construct($address) {
    if (!isset($address)) {
      throw new Exception("Error: Address not provided.");
    }
    $this->address = $address;
  }

  /**
   * Set the address for the request.
   *
   * @param string $address
   *   The URI or IP address to request.
   */
  public function setAddress($address) {
    $this->address = $address;
  }

  /**
   * Set the username and password for HTTP basic authentication.
   *
   * @param string $username
   *   Username for basic authentication.
   * @param string $password
   *   Password for basic authentication.
   */
  public function setBasicAuthCredentials($username, $password) {
    $this->userpwd = $username . ':' . $password;
  }

  /**
   * Enable cookies.
   *
   * @param string $cookie_path
   *   Absolute path to a txt file where cookie information will be stored.
   */
  public function enableCookies($cookie_path) {
    $this->enableCookies = TRUE;
    $this->cookiePath = $cookie_path;
  }

  /**
   * Disable cookies.
   */
  public function disableCookies() {
    $this->enableCookies = FALSE;
    $this->cookiePath = '';
  }

  /**
   * Set a request type (by default, cURL will send a GET request).
   *
   * @param string $type
   *   GET, POST, DELETE, PUT, etc. Any standard request type will work.
   */
  public function setRequestType($type) {
    $this->requestType = $type;
  }

  /**
   * Getters.
   *
   * @todo - Document each getter.
   */
  public function getResponse() {
    return $this->responseBody;
  }
  public function getHeader() {
    return $this->responseHeader;
  }
  public function getHttpCode() {
    return $this->httpCode;
  }
  public function getLatency() {
    return $this->latency;
  }
  public function getError() {
    return $this->error;
  }

  /**
   * Check for content in the HTTP response body.
   *
   * This method should not be called until after execute(), and will only check
   * for the content if the response code is 200 OK.
   *
   * @param string $content
   *   String for which the response will be checked.
   *
   * @return bool
   *   TRUE if $content was found in the response, FALSE otherwise.
   */
  public function checkResponseForContent($content = '') {
    if ($this->httpCode == 200 && !empty($this->response)) {
      if (strpos($this->responseBody, $content) !== FALSE) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Check a given address with cURL.
   *
   * After this method completes, the following variables will be populated:
   *   - $this->response
   *   - $this->httpCode
   *   - $this->latency
   */
  public function execute() {
    // Set a default latency value.
    $latency = 0;

    // Set up cURL options
    $ch = curl_init();
    // If there are basic authentication credentials, use them.
    if (isset($this->userpwd)) {
      curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
    }
    // If cookies are enabled, use them.
    if ($this->enableCookies) {
      curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiePath);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiePath);
    }
    // Send a custom request if set (instead of standard GET).
    if (isset($this->requestType)) {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->requestType);
    }
    // Don't print the response; return it from curl_exec().
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $this->address);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    // Follow redirects (maximum of 5).
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    // SSL support.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->enableSSL);
    // Set a custom UA string so people can identify our requests.
    curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
    // Output the header in the response.
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    curl_close($ch);

    // Set the header, response, error and http code.
    $this->responseHeader = substr($response, 0, $header_size);
    $this->responseBody = substr($response, $header_size);
    $this->error = $error;
    $this->httpCode = $http_code;

    // Convert the latency to ms.
    $this->latency = round($time * 1000);
  }
}
