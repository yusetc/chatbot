# Bank Operations Chatbot App
Symfony Rest API for bank operations and Vuejs SPA for chatbot interaction 

## Frontend SPA Documentation
- [Frontend SPA](front/chatbot/README.md)

## Rest API Documentation
### Windows Environment 
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

### Linux Environment
If you work with a Linux environment you can ignore the vagrant configuration and just need
installed docker & docker-compose (https://docs.docker.com/compose/install/) and finally 
in the project root folder execute:
````
sudo docker-compose up -d
````

### Symfony configuration
In the app folder you need to run:
````
composer install  
````
To install all the project dependencies.

Database configuration associated with docker mysql image is configured in the **.env** file
````
DATABASE_URL=mysql://symfony:symfony@mysql:3306/symfony?serverVersion=5.7
````
You can change it if you plan to use other database.

After you have configured database connection then you can create the tables structure 
using the migrations script inside symfony project **src/migrations** folder, for this you can 
use docker commands like this:
````
sudo docker-compose exec php php /var/www/app/bin/console doctrine:migrations:migrate
````

You don't need an initial database data, just register some new user using the chatbot commands.

#### For JWT Authentication configuration follow this steps:

Inside the symfony **app** folder generate the SSH keys, in this process you need to enter the passfrase specified in the .env file:

``` bash
$ mkdir -p config/jwt
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
