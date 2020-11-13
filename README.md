# Validate Apple User Code with Laravel

![N|Solid](https://media.licdn.com/dms/image/C4D0BAQEvcWyGMS9Y4g/company-logo_200_200/0?e=2159024400&v=beta&t=vl8MQYN2beakdmB0GW7886EUSTwzV0oAKDm4V5MgPhU)

# Getting Start!

 ```sh
 composer require apple-laravel-authorization-code
```

Add Service Provider in config/app.php

```
'providers' => [
   ...
    AnimusCoop\AppleTokenAuth\AppleTokenAuthServiceProvider::class,
]
```

# Use

In your controller export.

```
use AnimusCoop\AppleTokenAuth\Classes\AppleAuth;
```

```
$data = [
   "client_id" => "",
   "team_id"   => "",
   "key_id"    => "",
   "key"       => storage_path('AuthKey.p8'), //path where is your p8 key example if your key is in storage
   "code"      => "" //code sended by your front end guy
];

$appleAuth = new AppleAuth($data);

// if you need only the jwt signed with your p8 key file

$jwt = $appleAuth->getJwtSigned();

// Refresh Token and user Data
$user = $appleAuth->getUserData();


/*
 
 
 
 */

```
