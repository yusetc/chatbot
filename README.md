# Bank Operations Chatbot App
Symfony Rest API for bank operations and Vuejs SPA for chatbot interaction 

## Frontend SPA Documentation
- [Frontend SPA](front/chatbot/README.md)

## Rest API Documentation 
This project use Vagrant + Docker to deploy the App. 
If you work in a Windows Environment this is the best option to use.
In this case you just need to setup your Windows credentials 
in the Vagrant file in order to sync you App folders. Install VirtualBox and 
Vagrant and using the command line interface execute:
````
vagrant up
vagrant ssh
````
and you'll have a docker ready linux environment and the App running dockerized
in the IP: 192.168.33.10 and with an apache virtual host: http://api.chatbox.local
that can be changed [here](infrastructure/containers/apache/config/vhost/symfony.conf).
In your local environment you need add this virtual host domain like this:
````
192.168.33.10 api.chatbot.local
````  
 
 




#### For JWT configuration follow this steps:

##### Inside the symfony app folder generate the SSH keys, in this process you need to enter the passfrase specified in the .env file:

``` bash
$ mkdir -p config/jwt
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
