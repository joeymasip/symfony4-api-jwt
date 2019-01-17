Based in PHPDocker.io generated environment
===========================================

#Add to your project

Simply, unzip the file into your project, this will create `docker-compose.yml` on the root of your project and a folder named `djangodocker` containing python + django and config for it.

Note: Make sure you modify the requirements.txt for the django dockerfile.

#Setup xdebug 

Make sure this lines are present in `./docker/php-fpm/php-ini-override.ini`:
```
xdebug.remote_enable=1
xdebug.remote_autostart=1
xdebug.remote_port=9000
xdebug.remote_handler=dbgp
xdebug.remote_connect_back=0
```

Run at least once `localIp.cmd` to set local variable **localIp** to the actual local ip of the computer.

##Configure PHPStorm for remote debugging

###Add a Server

Add a Server in **Settings - Languages and Frameworks - PHP - Servers**

* Add new server
* Name = Docker
* Host = localhost
* Port = 8000
* Use path mappings = check
* In the root path of the project add in **Absolute path on the server**: `/application`

###Add Debug configuration

In **Run - Edit configurations...**

* Add new configuration
* Name = Docker 
* Filter debug connection by IDE key = check
* Server = Docker (the previous one)
* Key = PHPSTORM (or whatever configured in brower xdebug-helper)

###To debug

* **Run - Debug** (or debug in toolbar)
* **Run - Start listening...** (or turn on the phone in the toolbar)
* In the browser:
    * Turn on the xdebug-helper
    * Load the url
    * ...

#How to run

Dependencies:

  * Docker engine v1.13 or higher. Your OS provided package might be a little old, if you encounter problems, do upgrade. See [https://docs.docker.com/engine/installation](https://docs.docker.com/engine/installation)
  * Docker compose v1.12 or higher. See [docs.docker.com/compose/install](https://docs.docker.com/compose/install/)

Once you're done, simply `cd` to your project and run `docker-compose up -d`. This will initialise and start all the containers, then leave them running in the background.

##Services exposed outside your environment

You can access your application via **`127.0.0.1:8000`**, if you're running the containers directly.

##Hosts within your environment

You'll need to configure your application to use any services you enabled:

Service|Hostname |Port number
-------|---------|-----------
django |django   |8000
mysql  |mysql    |8306 (default)
mongo  |mongo    |27017 (default)

#Docker compose cheatsheet

**Note:** you need to cd first to where your docker-compose.yml file lives.

  * Start containers in the background: `docker-compose up -d`
  * Start containers on the foreground: `docker-compose up`. You will see a stream of logs for every container running.
  * Stop containers: `docker-compose stop`
  * Kill containers: `docker-compose kill`
  * View container logs: `docker-compose logs`
  * Execute command inside of container: `docker-compose exec SERVICE_NAME COMMAND` where `COMMAND` is whatever you want to run. Examples:
    * Shell into the PHP container, `docker-compose exec php-fpm bash`
    * Run symfony console, `docker-compose exec php-fpm bin/console`
    * Open a mysql shell, `docker-compose exec mysql mysql -uroot -pCHOSEN_ROOT_PASSWORD`
    * Shell into the mongo container, `docker-compose exec mongo bash`

#Recommendations

It's hard to avoid file permission issues when fiddling about with containers due to the fact that, from your OS point of view, any files created within the container are owned by the process that runs the docker engine (this is usually root). Different OS will also have different problems, for instance you can run stuff in containers using `docker exec -it -u $(id -u):$(id -g) CONTAINER_NAME COMMAND` to force your current user ID into the process, but this will only work if your host OS is Linux, not mac. Follow a couple of simple rules and save yourself a world of hurt.

  * Run composer outside of the php container, as doing so would install all your dependencies owned by `root` within your vendor folder.
  * Run commands (ie Symfony's console, or Laravel's artisan) straight inside of your container. You can easily open a shell as described above and do your thing from there.
