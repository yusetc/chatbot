# Bank Operations Chatbot App

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
