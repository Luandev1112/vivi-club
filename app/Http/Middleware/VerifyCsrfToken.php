<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
	/**
	 * Indicates whether the XSRF-TOKEN cookie should be set on the response.
	 *
	 * @var bool
	 */
	protected $addHttpCookie = true;
	
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
		'*'
    ];
	
	/**
	 * Create a new middleware instance.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @param  \Illuminate\Contracts\Encryption\Encrypter  $encrypter
	 * @return void
	 */
	public function __construct(Application $app, Encrypter $encrypter)
	{
		if (config('settings.security.csrf_protection')) {
			$this->except = [];
		}
		
		parent::__construct($app, $encrypter);
	}
}
