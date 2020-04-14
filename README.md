# Bank Operations Chatbot App
Symfony Rest API for bank operations and Vuejs SPA for chatbot interaction 

## Frontend SPA Documentation
- [Frontend SPA](front/chatbot/README.md)

## Rest API Documentation 


## Documentation For Authorization

### For JWT configuration follow this steps:

#### Generate the SSH keys :

``` bash
$ mkdir -p config/jwt
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
