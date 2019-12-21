<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016101600697018",

		//商户私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEArRq7YKVFoogrTUQ1iLws4NZcau0tM+26iqZys9YytHdMK97ZklfE8kFM7PO0Z/YD9HYllf6vdD7SxGsM8Yl0lmldW2FI+/+WcEPcGRASQDGLrOzA5XJQA9dIsELJ8TDm349Jc2+SLWWTFhzoaAxA995VyQJ8GRjuKqzTq+DOR3lP8r2f/V7hha+uEduoDUOUOnCZBZNPNu3DJWqA6XD4CD/c0GGgOKI9FZapPY3fwqZX2MwNsI6giwqUUoQAXV1rKaYppr6T4aXHzBm6spuZXy2Qh7trOe9XyVFVLpbvcknyaO8zWR7Tf7jKyAgv2ViCwPJwZuaQ82vx3pW4KCtA9QIDAQABAoIBAGaIlgK6ApQyOgvrYhZa9+45qsY/NG402zCzRO/W6XPR0hGOT6uKR4MDQPB9rheCNnd/3+WH1R9y8t8bVbBgLenGAWFWL6fsjYxz9ZP23AigEE7ecae23URZKZoWBY3S3H4BXo18wcYYC1amytaOo5DuZjvJN/6ZsMEgW8TMRQM6EwIH4D0l/Y7gpwGFbNiLmixKDtUWxj+eZmYxC2Fg1ZdTn77hm0MdMWE8iiL1S1dnfMTUyBQk/hHpHFZJrSFaweUTe71A2OMu3eWaeDXSYdETHtYoYxzo1tRfcyBWO6rN6iTfyLNvqKJAO+BDoW8LFZOjfirhoXrbN3XpLmE8FoUCgYEA49UGTN7ez5tFQXZc+6ZHe8eM46kQWEjJ7Kyf4QJKjSbj4/nF40FCyJP2N011Q14GaDhiV8sSjIho2t8T+F1gWAGoVoBmXb8vOXmI86r0rZHnmYkmWj+uuYPNazf+OfsrP89uAHSVWWn/I1+FdB9/L8Z0W3pHgb6jHIv9yAn3W+8CgYEAwoGN3lpio8WQJ0Gc2TGY/fjEY62nzOZJuxmS/OYS3vj11tvqLmdayGUSenLH1/H9dFfQk00S6uLllRQXWyVVzGO17+XZ3n2hRqQ7Y/FFtEw6e+izFxohrXbf9aaHHTg46yaQ4/It2XwJUTLs4jwQi0yTji3CM0GZm0MQuYfknVsCgYB+VeEBfVCGRK55p13WOZVIARaSI2yp1+sIr4yUZAXdEaSrBRUDeGvE968/aVdN/PSGlEWMM1Jc1UN3ot3bCkRjaL/k/5xD/cD9GPSlUbYODWsky1WWE9waiQi+nLT/h9SqtKZl4D/07f8JqW1CQDjx0BUzqLHAM2sVaFdYtpYQxwKBgQCJJJHkQrSkJsvCcifmmGr4P+wxICNdfM5l5t1WBd8uajnkugC/oU5IV8OUpNO1tkFwQ+6jvcdl6H/aAmWJdaSuTWvd76ITl18ckPXzPQ5Z1xmxflywIFO4nEXDThychKTVYMbugjbDzwo+v3p49cLwhRjROgBg5ZMp/zqOpnuSWwKBgElJl+IbstjsxAgk8L362IPXfbfuexEpCUcx0T8C8948TN/Cp4wTzByEPYa38y80qU8G2k5hcaPtwEtrXMNBqw1CR76uGxQ69zp+qQDZkhkFbsFspfoQSiCwL6iwKnuF8btQVAOPT7vwS/GJr3S0GppWQ05Fe/YmDspNRH0bMv+k",
		
		//异步通知地址
		'notify_url' => "http://www.tpshop.com/home/order/notify",
		
		//同步跳转
		'return_url' => "http://www.tpshop.com/home/order/callback",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjkCIYm/RVxSQIPuWeVE2bH6esPXaBEIHDkoWzkNCZucSFIAUlGoKoBWY/t9sVaWitN6VP0emGub+b5iIk8dNpzqa1/NqyR7HjFLbAqcRirQNdFlQv031mPknM2kfEzQy6Wk3ZAXYjQxgOVf+O8NI4Kf+fcTiwZrrFgMH7bhoDMb+8mxscbM0W0MHAn8E/a3t3aGctz4YkfZEnKahn4eXRcpJzLUAMLGcXz5GDw35EEzdhNdQhweaZDjQznFlQecq9fIcWkLgjbcTwfoB8tVwt4ltlTAKnjOheTTrGFFnTM2uM1b0juD3FjGiKCmUI/KJ7fh1VjP35BJ6J+UTgTz1RQIDAQAB",
);