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
installed **docker & docker-compose** (https://docs.docker.com/compose/install/) and finally 
in the project root folder execute:
````
sudo docker-compose up -d
````

### MacOSX environment
Since shared folders in Docker with MacOSX need to be defined (https://docs.docker.com/docker-for-mac/osxfs) or use defaults ones in order to persist data from containers like MySQL you need to modify this configuration for docker environment.
By default, you can share files in /Users/, /Volumes/, /private/, and /tmp directly, so the easy way is to create a subfolder inside this shared folder, Ex.
````
mkdir /Users/[your_username]/mysql
````
Then, modify the file [docker-compose.override.yaml](docker-compose.override.yaml) changing the **volumes** directive in mysql container according the folder create above:
````
volumes:
  - /Users/[your_username]/mysql:/var/lib/mysql
````

Also, if you don't want to persist mysql data you just need to create a temporal volume and the [docker-compose.override.yaml](docker-compose.override.yaml) will be like this:
````
version: '3.4'
services:
  apache:
    volumes:
      - ./infrastructure/containers/apache/config/vhost:/etc/apache2/sites-enabled
      - ./app:/var/www/app
  php:
    build:
      target: development
    image: php:local
    volumes:
      - ./app:/var/www/app
      - /home/vagrant:/home/vagrant
    environment:
      - APP_ENV=dev
    depends_on:
      - mysql
  composer:
    build:
      context: ./
      dockerfile: ./infrastructure/containers/php/Dockerfile
      target: vendor
    container_name: composer
    volumes:
      - ./app:/var/www/app
  mysql:
    image: mariadb:10.0.17
    container_name: mysql
    networks:
      - app
    ports:
      - 3306:3306
    volumes:
      - mysql_volume:/var/lib/mysql
    command:
      - "--default-authentication-plugin=mysql_native_password"
      - "--lower_case_table_names=1"
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: symfony
      MYSQL_USER: symfony
      MYSQL_PASSWORD: symfony
networks:
  app:
    driver: bridge
volumes:
  mysql_volume:
````

### Symfony configuration
In the **app** folder you need to run:
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
