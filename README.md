## Lumen - Saml2

This repository started as a clone of the [Laravel 5 - Saml2](https://github.com/aacotroneo/laravel-saml2) based upon the [OneLogin](https://github.com/onelogin/php-saml) toolkit offered as an easier more lightweight approach to simplesamlphp.

This documentation focuses on the differences between this package and the Laravel 5 - Saml2 package. 




## Installation - Composer

You can install the package via composer but first you need to add the following to your composer.jsin:
```json
 "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:sugarfixx/lumen-saml2.git"
        }
    ],
```
Then you can go ahead with:
```
composer require sugarfixx/lumen-saml2
```
Or manually add this to your composer.json:

```json
"sugarfixx/lumen-saml2": "*"
```

You need to register the service provider in bootstrap/app.php

```php
/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(Sugarfixx\Saml2\Saml2ServiceProvider::class);
```


### Configuration

To use configurations from the configurations folder you first need to create a folder called config.
Then you need to manually copy the files from this packages config folder to the newly created config folder.

The test_idp_settings.php config is handled almost directly by  [OneLogin](https://github.com/onelogin/php-saml) so you should refer to that for full details, but we'll cover here what's really necessary. There are some other config about routes you may want to check, they are pretty strightforward.

Since Lumen do not automatically load config files you need to add the config files to bootstrap/app.php:

```php
   $app->configure('saml2_settings');
   $app->configure('test_idp_settings');
   $app->configure('<idp_name>_idp_settings');
```

#### Define the IDPs
Define names of all the IDPs you want to configure in saml2_settings.php. Optionally keep 'test' as the first IDP if you want to use the simplesamlphp demo, and add real IDPs after that. The name of the IDP will show up in the URL used by the Saml2 routes this library makes, as well as internally in the filename for each IDP's config.


```php
    'idpNames' => ['test', 'myidp1', 'myidp2'],
```

#### Configure lumen-saml2 to know about each IDP

You will need to create a separate configuration file for each IDP under `app/config/` folder. e.g. `myidp1_idp_settings.php`. You can use `test_idp_settings.php` as the starting point; just copy it and rename it.

Configuration options are note explained in this project as they come from the [OneLogin project](https://github.com/onelogin/php-saml), please refer there for details.

The only real difference between this config and the one that OneLogin uses, is that the SP entityId, assertionConsumerService url and singleLogoutService URL are injected by the library. If you don't specify those URLs in the corresponding IDP config optional values, this library provides defaults values: the metadata, acs, and sls routes that this library creates for each IDP. If specify different values in the config, note that the acs and sls URLs should correspond to actual routes that you set up that are directed to the corresponding Saml2Controller function.

If you want to optionally define values in ENV vars instead of the \*\_idp_settings file, you'll see in there that there is a naming pattern you can follow for ENV values. For example, if in myipd1_idp_settings.php you set `$this_idp_env_id = 'MYIDP1';`, and in myidp2_idp_settings.php you set it to `'SECONDIDP'`, then you can set ENV vars starting with `SAML2_MYDP1_` and `SAML2_SECONDIDP_`, e.g.
```env
SAML2_MYIDP1_SP_x509="..."
SAML2_MYIDP1_SP_PRIVATEKEY="..."
// Other  SAML2_MYIDP1_* values

SAML2_SECONDIDP_SP_x509="..."
SAML2_SECONDIDP_SP_PRIVATEKEY="..."
// Other SAML2_SECONDIDP_* values
```

#### URLs To Pass to The IDP configuration
As mentioned above, you don't need to implement the SP entityId, assertionConsumerService url and singleLogoutService routes, because Saml2Controller already does by default. But you need to know these routes, to provide them to the configuration of your actual IDP, i.e. the 3rd party you are asking to authenticate users.

You can check the actual routes in the metadata, by navigating to 'http(s)://lumen_url/myidp1/metadata', which incidentally will be the default entityId for this SP.

If you configure the optional `routesPrefix` setting in saml2_settings.php, then all idp routes will be prefixed by that value, so you'll need to adjust the metadata url accordingly. For example, if you configure routesPrefix to be `'single_sign_on'`, then your IDP metadata for myidp1 will be found at http://lumen_url/single_sign_on/myidp1/metadata.

#### Example: simplesamlphp IDP configuration
If you use simplesamlphp as a test IDP, and your SP metadata url is `http://lumen_url/myidp1/metadata`, add the following to /metadata/sp-remote.php to inform the IDP of your lumen-saml2 SP identity:

```php
$metadata['http://lumen_url/myidp1/metadata'] = array(
    'AssertionConsumerService' => 'http://lumen_url/myidp1/acs',
    'SingleLogoutService' => 'http://lumen_url/myidp1/sls',
    //the following two affect what the $Saml2user->getUserId() will return
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
    'simplesaml.nameidattribute' => 'uid' 
);
```


### Usage

Where you want your user to login.
```php
public function saml2( $redirectUrl)
 {
    $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('<idp_name>'));
    return $saml2Auth->login($redirectUrl);    
 };
```

Where you want to process acs

```php
public function processAcs(Request $request)
{
    $saml2Auth = new Saml2Auth(Saml2Auth::loadOneLoginAuthFromIpdConfig('<idp_name>'));
    $errors = $saml2Auth->acs();
    
    // Get assertion data
    $federatedId =  $samlUser->getUserId();
    $attributes = $samlUser->getAttributes();
    $rawAssertion = $samlUser->getRawSamlAssertion();
}
```

For more inspiration on how this package can be used see the original Laravel5 - Saml2 readme at github.

And that's it!
