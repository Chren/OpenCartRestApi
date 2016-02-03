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
  
#### Register
  url: index.php?route=openapi/account/register
  
#### Login
  url: index.php?route=openapi/account/login
  
#### Logout
  url: index.php?route=openapi/account/logout

### Products
- `getproducts`
- `categories`

## License
OpenCartRestApi is released under the MIT license. See LICENSE for details.

## Contact
Please create issue for any bug. You can also contact me via email: aren372@126.com
