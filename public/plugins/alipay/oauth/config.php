<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2019120669704308",
		//编码格式
		'charset' => "UTF-8",
		//签名方式
		'sign_type'=>"RSA2",
		//scope auth_user获取用户信息; auth_base静默授权
		'scope' => 'auth_user',
		//跳转地址
		'redirect_url' => 'http://'.$_SERVER['HTTP_HOST'].'/home/login/alicallback',
        //支付宝授权地址 沙箱环境
//        'oauth_url' => 'https://openauth.alipaydev.com/oauth2/publicAppAuthorize.htm',
        //支付宝授权地址 正式环境
         'oauth_url' => 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm',
        //支付宝网关 沙箱环境
//        'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",
        //支付宝网关 正式环境
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//商户私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEA0zqyeVTUJ3p/5Dc9+kFMrZ4HTeYyMSoBCjYiYtag2Ws4rZj6Hjtjq9lGpxB8cdhnSpCO2bihM6Z+JwCOnsUGLYchkLztxHdaW/+Q6dOaKnNA5piV4J04rabm6jJILKpb6u5AbdjwvwCrxYuHE2a64k5uE33TuhtLoVL94ZfK+R+4tf6sdJsY7e7PZJPEtAefijSe9Jcx4pxbJf/rUv7gwnTqQLaFHobKZEESOcZluWJNWgumP0J/CaGCABmTC5r1SI/5hSb3otrpsJLrVZ+LmQApZymwbGIJVjcZKo638cgSgQ8VMp/Wy71fSg7bk4JEsq9ZAOocXxR5EZ4DFZF6NQIDAQABAoIBAQDIxuRaO+2k0OW7sV6x/1u5M5rRytsurDRs1mP8+vHNeANZzyz/i4gEEvT7W8pOFFqqH5oJDVv80mba/8aQ18o1gFrBTzPaKXC0Pcoq2D2T2BV6mtLru8XiGeEn/z9nZU8Vkf2f7DN5+lNL5IIySNWewtoSoOmd3tAWueIkirod7PP3b7ebWt9X0e1/YCVVZm1OlGmEGQwDv6SGpowjJUPRBDZTwd+iDfyJmpbOUec47yKoeR57OINvA1NkHzyR/JdYz7iDKjNUd1A4FJP3LMC+Jr6GMCq92YvvOQrCgB22AIckxedl0WjuUo5vmKITsH0dk7cvvxX3boVZuW3AgbHBAoGBAPjoO7rXWzGJo5+OKidNChEMni3Psu2j/PXPX6CtC0NUJ2RZV3xHzHjFXQu0N159adRxxjIMmaLaUiiAR6reXKK9aZKnyn57plQQh5SnHKwjbRADKxfJaVi1GgwHQSQobriiaoEoroFtyfn7Vznp0i4TbyQB2Vuvzx5jr41u+dfZAoGBANk/mvyMqVoYNFDhk1cbNiAtk3Mn2z/xWSE1ijY905CVUZgeD2swTyoDueJvqP6G3ImLWvvm4jwg0QuRcoNp8/3hk9u0IZ9egk9BfRUYKA1iQwjEjK8docg9WJ15qEx6KlMupsOL28nEUDxG9D+mHDclDd7DjMWOczjENdHiube9AoGAJIZnpW5OuoE5GoPRGb1LWd8hIxXUatzilOueW8So33Ns6GPX26vpjFth4QLMETiUHBGqBNQmg2hIIBta6O8CZvsmj9fBdjgM208lpiGzqmr0aSId47qxk8vXi6ZQ385zGPL0cAmZOfLzbZR9Y0k1h39gkscWr0aoPQxhyX5ceVkCgYAPxNsmG7urK3iYqlb8iqAaI9TBFhKHCKi0jWNT3pb9tqjuhrgNyrrkTNLwSbSVjQpy/WeEQ1UGkkc6NLuAKG+qbPZDi0apf72rrRA8OL2ngwtkloezqk307+b/vzXlHzT1RryaEwvIEHTWezv9ZrxDkG/6TG1qZZWdeDiD3JYobQKBgHJ/kTI+4f9EBBEH2oePnsVHObDYUfud4D92gCzR4cY4wID2pnznXOJFlSZhCbpTcKAFfZw2Xgz/1boFsCNup0/UfGYIhQoOwf3ErHKFMfk1o4eVG2HSWw0vUm0Yd4PdIuaubq8tWgv6zEaumuXRjQ6HgPUSc0sSY051MVeHOKA4",

);