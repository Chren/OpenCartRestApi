# OpenCartRestApi
This is restful api for opencart. It also extends OpenCart to support some features like register and login using mobile phone number. More features will be added in the future.
  
## Howto Install

- Copy and replace all of file to your Opencart project folder.
- Enable OpenCartRestApi by go to admin page->Feeds

## Requirements

- Compatible with Opencart 2.0.3.1, may also compatible with other versions(not test yet).
- mcrypt
- RNCryptor

## Support Apis
### Account
#### RegisterDevice
  url: index.php?route=openapi/account/registerdevice
  
| Param Name | Type  | Required  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|
| bundleid | string  | YES  | bundleid for app  |
| appversion | string  | YES  | appversion  |
| uuid | string  | YES  | uuid to identify device |
| sysname | string  | NO  | eg:iOS |
| sysversion | string  | NO  | eg:9.1 |

#### Register
  url: index.php?route=openapi/account/register
  
| Param Name | Type  | Required  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|
| param | string  | YES  | RNEncryptor(telephone=[telephone]&password=[password]).base64Encoding |

#### Login
  url: index.php?route=openapi/account/login
  
| Param Name | Type  | Required  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|
| param | string  | YES  | RNEncryptor(telephone=[telephone]&password=[password]).base64Encoding |

#### Logout
  url: index.php?route=openapi/account/logout

| Param Name | Type  | Required  | Description  |
|:--------------------:|:---------------------------:|:----------------------------:|:--------------------------------:|

### Products
- `getproducts`
- `categories`

## License
OpenCartRestApi is released under the MIT license. See LICENSE for details.

## Contact
Please create issue for any bug. You can also contact me via email: aren372@126.com
