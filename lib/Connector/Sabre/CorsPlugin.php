<?php

declare(strict_types=1);
/**
 * Kindly adapted from https://gitlab.com/kinolaev/nextcloud-dav-cors.
 */

namespace OCA\WebAppPassword\Connector\Sabre;

use OCA\WebAppPassword\Config\Config;
use Sabre\DAV\ServerPlugin;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;
use Sabre\HTTP\Sapi;

class CorsPlugin extends ServerPlugin {
	/**
	 * @var string[]
	 */
	private $origins;

	public function __construct(Config $config) {
		$this->origins = $config->getOriginList();
	}

	/**
	 * @return void
	 */
	public function initialize(\Sabre\DAV\Server $server) {
		$server->on(\OCP\Util::getVersion()[0] <= 18 ? 'beforeMethod' : 'beforeMethod:*', [$this, 'setCorsHeaders'], 5);
	}

	/**
	 * @return void|bool
	 */
	public function setCorsHeaders(RequestInterface $request, ResponseInterface $response) {
		if ($response->hasHeader('access-control-allow-origin')) {
			return;
		}

		$origin = $request->getHeader('origin');
		if (empty($origin) || !in_array($origin, $this->origins, true)) {
			return;
		}

		$response->addHeader('access-control-allow-origin', $origin);
		$response->addHeader('access-control-allow-methods', $request->getHeader('access-control-request-method'));
		$response->addHeader('access-control-allow-headers', $request->getHeader('access-control-request-headers'));
		$response->addHeader('access-control-expose-headers', 'etag, dav');
		$response->addHeader('access-control-allow-credentials', 'true');

		if ($request->getMethod() === 'OPTIONS' && empty($request->getHeader('authorization'))) {
			$response->setStatus(204);
			Sapi::sendResponse($response);

			return false;
		}
	}
}
