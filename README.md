# OpenCartRestApi
This is restful api for opencart. It also extends OpenCart to support some features like register and login using mobile phone number. More features will be added in the future.
  
## Howto Install

- Copy and replace all of file to your Opencart project folder.
- Enable OpenCartRestApi by go to admin page->Feeds

## Requirements

- Compatible with Opencart 2.0.3.1, may also compatible with other versions(not test yet).
- mcrypt
- RNCryptor

## Usage
  Header for any api required login:
  
| Param Name | Type  | Required  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|
| platform | int  | YES  | client app platform, eg:0 for iOS, 1 for Android |
| appversion | string  | YES  | client app version|
| deviceid | string  | YES  | return after register device|
| token | string  | YES  | return after login|
  
  Response data format:
  ```ruby
  {
	"code":200,
	"message":"",
	"data":{

	}
}
```
## Support Apis

### Account
#### RegisterDevice
  url: index.php?route=openapi/account/registerdevice
  
  Request:
  
| Param Name | Type  | Required  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|
| bundleid | string  | YES  | bundleid for app  |
| appversion | string  | YES  | appversion  |
| uuid | string  | YES  | uuid to identify device |
| sysname | string  | NO  | eg:iOS |
| sysversion | string  | NO  | eg:9.1 |

  Response:
  
| Param Name | Type  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|
| deviceid | string | deviceid create by server side  |
| aeskey | string | aeskey create by server used as password for RNEncryptor AES encoding |

  eg:
  ```ruby
{
	"code":200,
	"message":"",
	"data":{
		"deviceid":"8089C375236193A6DB",
		"aeskey":"01B6036F37AA5161CD2611F0E42985FD7CC34FC208A1A9377F43F66EA40763E3"
	}
}
```

#### Register
  url: index.php?route=openapi/account/register
  
  Request:
  
| Param Name | Type  | Required  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|
| param | string  | YES  | RNEncryptor(telephone=[telephone]&password=[password]).base64Encoding (use aeskey as password which get from RegisterDevice)|

#### Login
  url: index.php?route=openapi/account/login
  
  Request:
  
| Param Name | Type  | Required  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|
| param | string  | YES  | RNEncryptor(telephone=[telephone]&password=[password]).base64Encoding |
  
  Response:
  
| Param Name | Type  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|
| uid | long | user id  |
| telephone | string | telephone number |
| avatar | string | user head portrait |
| token | string | unique voucher after user login,  |

  eg:
  ```ruby
{
	"code":200,
	"message":"",
	"data":{
		"uid":100,
		"telephone":"18267767776",
		"avatar":"http://www.example.com/test.png",
		"token":"3789AFC19645EA9A65"
	}
}
```
#### Logout
  url: index.php?route=openapi/account/logout

  Request:
  
| Param Name | Type  | Required  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|

### Products
- `getproducts`
- `categories`

## License
OpenCartRestApi is released under the MIT license. See LICENSE for details.

## Contact
Please create issue for any bug. You can also contact me via email: aren372@126.com
