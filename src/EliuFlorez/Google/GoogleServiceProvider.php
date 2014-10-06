<?php namespace Eliuflorez\Google;

use Illuminate\Auth\AuthServiceProvider;

class GoogleServiceProvider extends AuthServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('Eliuflorez/google', null, realpath(__DIR__.'/../../'));
        parent::boot();

        $this->app['auth']->extend('google', function($app) {
            return new Google(new GoogleProvider(), $app['session.store']);
        });
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        parent::register();

        $app = $this->app;

        $app['google-client'] = $app->share(function($app)
        {
            $client = new \Google_Client();
            $client->setApplicationName($app['config']->get('google::clientId'));
            $client->setClientId($app['config']->get('google::clientId'));
            $client->setClientSecret($app['config']->get('google::clientSecret'));
            $client->setRedirectUri($app['config']->get('google::redirectUri'));
            $client->setDeveloperKey($app['config']->get('google::developerKey'));
            $client->setScopes($app['config']->get('google::scopes'));
            $client->setAccessType($app['config']->get('google::access_type'));

            return $client;
        });

        $app['router']->filter('google-finish-authentication', function($route, $request) use ($app) {
            return $app['auth']->finishAuthenticationIfRequired();
        });
	}
	
}
