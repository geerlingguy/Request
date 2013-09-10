<img src="http://github.com/geerlingguy/Request/raw/1.x/Resources/Request-Logo.png" alt="Request for PHP Logo" />

# Request

A simple PHP HTTP request class.

This class includes many convenience methods to help take the headache out of
dealing with HTTP requests in PHP.

## Usage

Include the class (`\JJG\Request`) using an autoloader, then build a new Request
object, execute the request, and get the response.

```php
$request = new Request('http://www.example.com/');
$request->execute();
$response = $request->getResponse();
```

Other parameters you can retrieve after executing a request include:

```php
// The full headers from the response.
$request->getHeaders();
// The latency for this response, in ms.
$request->getLatency();
// The HTTP status code (e.g. 200 for 200 OK).
$request->getHttpCode();
// Empty if no error present, otherwise shows any cURL errors.
$request->getError();
```

There are also other convenient methods included for other purposes.

```php
// Returns TRUE if 'string' exists in the response.
$request->checkResponseForContent('string');
```

You can also make requests with basic HTTP authentication:

```php
// Execute a request with HTTP basic authentication.
$request = new Request('http://www.example.com/secure-page');
$request->setBasicAuthCredentials('username', 'password');
$request->execute();
```

Other options include enabling or disabling SSL, using cookies, and setting cURL
timeout values:

```php
// Enable SSL/TLS.
$request->enableSSL = TRUE;
// Set the user agent string.
$request->userAgent = 'User agent string here.';
// Set the initial connection timeout (default is 10 seconds).
$request->connectTimeout = 5;
// Set the timeout (default is 15 seconds).
$request->timeout = 10;
```

See the Request class variable definitions and methods for more
details and documentation.

## Why Request?

I've used other HTTP request libraries for PHP before, but often fall back to
using cURL directly, because the libraries I've used are too complicated for my
needs. This library aims to be a very simple and easy-to-use wrapper around
cURL, and should be easy to pick up for anyone familiar with cURL usage in PHP.

Some other recommended HTTP libraries for PHP include:

  - [Guzzle](http://guzzlephp.org/)
  - [Httpful](http://phphttpclient.com/)
  - [Zend_Http](http://framework.zend.com/manual/1.12/en/zend.http.html)
  - [Unirest](https://github.com/mashape/unirest-php)
  - [Requests](https://github.com/rmccue/Requests)

## License

Imap is licensed under the MIT (Expat) license. See included LICENSE.md.
