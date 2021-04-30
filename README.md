# Loggjafarthing
<<<<<<< HEAD


## Configuration:
The API is configures with environment variables

| name                | default                          | Options                               | description                   |
|---------------------|----------------------------------|---------------------------------------|-------------------------------|
| DB_HOST             | localhost                        | <string>                              |
| DB_PORT             | 3306                             | <number>                              |
| DB_NAME             | althingi                         | <string>                              |
| DB_USER             | root                             | <string>                              |
| DB_PASSWORD         |                                  | <string>                              |
| LOG_PATH            |                                  | <string>                              | Save logs to disk or php://stdout
| CACHE_TYPE          | none                             | file / memory / none                  |
| CACHE_HOST          |                                  | <string>                              |
| CACHE_PORT          |                                  | <number>                              |
| BROKER              | none                             | amqp / kafka                          |
| BROKER_HOST         | localhost                        | <string>                              |
| BROKER_PORT         | 5672                             | <string>                              |
| BROKER_USER         | guest                            | <string>                              |
| BROKER_PASSWORD     | guest                            | <string>                              |
| BROKER_VHOST        | /                                | <string>                              |
| BROKER_FORCED       | false                            | true / false                          | if event should be sent to queue even though no update occurred
| DOCUMENT_SERVER     |                                  | <string>                              |
| DOCUMENT_DEFINITION |                                  | <string>                              |
| INDEXER_STALL_TIME  | 150000                           | <int>                                 | How long the indexer sleeps between actions

## Development
This repo comes with a **Dockerfile**. It is based off of a Apache/PHP image and will install all needed PHP extensions and
as well as all dependencies via [composer](https://getcomposer.org/). It can be configured at build-time for either
**production** or **development** via [--build-arg](https://docs.docker.com/engine/reference/commandline/build/#set-build-time-variables---build-arg).

| arg         | values       | description |
| ----------- | ------------ | ----------- |
| ENV         | production / development |  |


This repo also comes with **docker-compose** file that has some pre-configured environments.

host.docker.internal

#### test
The Docker Compose file has a `$ docker-compose run test` service. When run, it will spin up a MySQL instance.

## Debugging.
As stated previously, the `$ docker-compose up run` will install Xdebug and configure the _remote host_ to be the
host system. The remote port will be `9003`.


## Non Docker development
The system can be run without Docker, but that requires a lot of manual configuration. Have a look at the Dockerfile for complete
list of PHP extensions required.

Then run `$ composer i` to install dependencies.


## CommandLine


## Other useful commands
All GET requests are cached, if you need to flush the cache, it can be done via
```bash
$ docker exec -it cache-api redis-cli FLUSHALL
```

To make a dump of the database, run
```bash
docker exec CONTAINER /usr/bin/mysqldump -u root --password=example althingi > backup.sql
```
## About developing with PHPStorm.
PHPStorm is a good IDE than can run PHP inside a Docker Container and connect to its Xdebug server. A few steps need to be
completed for this to work.

Interpreter:
A Docker image needs to be available that gets spun up each time the IDE want to run some PHP code. Create this container by calling
```bash
$ docker build -t althingi-source-runtime --build-arg ENV=development .
```

Dependencies:
You also need running containers for MySQL and MongoDB
```bash
$ docker run --name dev_api_database -e MYSQL_ROOT_PASSWORD=example -d einarvalur/althingi-source-db
$ docker run --name dev_api_store -d mongo:4.2.0-bionic
```
just make sure you run the containers from the project directory, so they end up in the same network.

Now it's time to link this all together

=======

An API that manages raw Althingi/Loggjafarthing data. PHP/[ZF3](https://github.com/zendframework/zf3-web) REST API.
This API can accept and provide data.

This is a classic REST API that defined **POST/PUT/PATCH** endpoints for creating data. The system uses a relation database
to store the data. In addition, whenever an entry is created, an event is sent to a queue (RabbitMQ) with the created
payload as well as an a action (create/update/delete). Another system called the
[AlthingiAccumulator](https://github.com/fizk/AlthingiAccumulator) is listening. When it receives a message, It will store
an enhanced version of that payload in MongoDB. The **GET** endpoints in this system are then reading from MongoDB, not the
database.

Likewise, then a resource is created, updated or deleted, it gets indexed in a search-cluster (Elasticsearch) through this queue
mechanism. **GET** endpoints that deal with search are now reading from this search cluster rather than the database.

Why is the data also stored in MongoDB and what is _enhanced version of the payload_?. Let's say that we know that a give member
of Althingi had a sitting/session between Jan 1 and Jan 31 for a given party, let's then further say that they made a speech on Jan 15.

Rather that having to go into the Session table and ask which session for a give congressman overlaps with this Jan 15. date
every time we display a speech, we can calculate that upfront and store it in a secondary storage. This will never change anyway.
Once a MP has made a speech, that speech will for ever have been made by that MP while his was a member of that party.

This is just one example of many where it makes more sense to calculate upfront. The other alternative is to construct complicated
`JOIN` statement in a relation database.

This betters dramatically the performance of the system.

Every request made to the API is also cached (Redis) for a short period of time. This is to further speed up the system
and reduce load.

## Documentation:
The [root endpoint](http://loggjafarthing.einarvalur.co:8080/) will provide Swagger description of all the available endpoints.

If you prefer to get the OpenAPI description file and load that into something like Postman, it is available through the
http://loggjafarthing.einarvalur.co:8080/openapi endpoint.

## Dependencies:
This API depends on a few systems

* **Data** Data is stored in MySQL database and accumulated data is stored in MongoDB. The API uses both to provide data
* **Search** Searches are done against Elasticsearch.
* **Cache** Data is cached in Redis for quicker response-time
* **Offline processing** Messages are sent to RabbitMQ for accumulation and offline processing. Results are stored in MongoDB

## Configuration:
The API is configures with environment variables

| name                | default                          | Options                               | description                   |
|---------------------|----------------------------------|---------------------------------------|-------------------------------|
| DB_HOST             | localhost                        | <string>                              |
| DB_PORT             | 3306                             | <number>                              |
| DB_NAME             | althingi                         | <string>                              |
| DB_USER             | root                             | <string>                              |
| DB_PASSWORD         |                                  | <string>                              |
| DB_NAME_TEST        | althingi_test                    | <string>                              |
| DB_SETUP            | false                            | true / false                          |
| SEARCH              | elasticsearch                    | elasticsearch / none                  |
| LOG_PATH            |                                  | <string>                              | Save logs to disk or php://stdout
| CACHE_TYPE          | none                             | file / memory / none                  |
| CACHE_HOST          |                                  | <string>                              |
| CACHE_PORT          |                                  | <number>                              |
| ES_HOST             | localhost                        | <string>                              |
| ES_PROTO            | http                             | <string>                              |
| ES_PORT             | 9200                             | <number>                              |
| ES_USER             | elastic                          | <string>                              |
| ES_PASSWORD         | changeme                         | <string>                              |
| QUEUE               | none                             | rabbitmq / none                       |
| QUEUE_HOST          | localhost                        | <string>                              |
| QUEUE_PORT          | 5672                             | <string>                              |
| QUEUE_USER          | guest                            | <string>                              |
| QUEUE_PASSWORD      | guest                            | <string>                              |
| QUEUE_VHOST         | /                                | <string>                              |
| QUEUE_FORCED        | false                            | true / false                          | if event should be sent to queue even though no update occurred
| STORAGE_DB          | althingi                         | <string>                              | MongoDB database
| STORAGE_HOST        | localhost                        | <string>                              | MongoDB host
| STORAGE_PORT        | 27017                            | <number>                              | MongoDB port
| STORAGE_USER        |                                  | <string>                              | MongoDB user
| STORAGE_PASSWORD    |                                  | <string>                              | MongoDB password
| DOCUMENT_SERVER     |                                  | <string>                              |
| DOCUMENT_DEFINITION |                                  | <string>                              |
| INDEXER_STALL_TIME  | 150000                           | <int>                                 | How long the indexer sleeps between actions

## Development
This repo comes with a **Dockerfile**. It is based off of a Apache/PHP image and will install all needed PHP extensions and
as well as all dependencies via [composer](https://getcomposer.org/). It can be configured at build-time for either
**production** or **development** via [--build-arg](https://docs.docker.com/engine/reference/commandline/build/#set-build-time-variables---build-arg).

| arg         | values       | description |
| ----------- | ------------ | ----------- |
| WITH_XDEBUG | true / false | Will install Xdebug. Set `XDEBUG_CONFIG=remote_host=host.docker.internal remote_port=9001` at runtime to connect |
| WITH_DEV    | true / false | If composer's dev dependencies should be installed |

This repo also comes with **docker-compose** file that has some pre-configured environments.

#### run
The `$ docker-compose up run` command will start the system without any external system. It has `WITH_XDEBUG` set to **true** and
`WITH_DEV` set to **true** as well, meaning that it's good for development. The idea is that you are responsible for spinning up
all dependant systems.

One way of doing it just to use the production systems, the other way is to spin them up locally. For this, the docker-compose file
can be configured at run-time

| name                  | default               |
| --------------------- | --------------------- |
| ENV_DB_HOST           | host.docker.internal  |
| ENV_DB_PORT           | 4406                  |
| ENV_DB_NAME           | althingi              |
| ENV_DB_USER           | root                  |
| ENV_DB_PASSWORD       | example               |
| ENV_SEARCH            | none                  |
| ENV_ES_HOST           | host.docker.internal  |
| ENV_ES_PROTO          | http                  |
| ENV_ES_PORT           | 9200                  |
| ENV_ES_USER           | elastic               |
| ENV_ES_PASSWORD       | changeme              |
| ENV_STORAGE_HOST      | host.docker.internal  |
| ENV_STORAGE_DB        | althingi              |
| ENV_STORAGE_PORT      | 27017                 |
| ENV_STORAGE_USER      | wo                    |
| ENV_STORAGE_PASSWORD  | long@pass!123         |

If `host.docker.internal` is replaced with `loggjafarthing.einarvalur.co`, all the dependent system will be queried from
the production env.

#### test
The Docker Compose file also has a `$ docker-compose run test` service. When run, it will spin up a MySQL and a MongoDB instance
and run test against them before exiting. It is not configurable as it is designed to run in CI environment but can be
run on a local system as well.

This will spin up two additional containers, one for the database and the other for the store
(althingi_test_store and althingi_test_database). They will not be removes once the test has finished running.

#### Other services.
The docker-compose file also defined a MongoDB service and a MySQL service.

When the MySQL service is started, it will grab the database-schema from `./auto/db` and build it. No data will be in the database,
just the schema. Once the service goes down, so does the data. This is mostly for unit-tests to be run against a pre-defined
database structure.

This is not done for MongoDB as document-store don't require schemas in the same way as relation database. This does mean that
test can be run against MongoDB, but MongoDB indexes can not be tested.

## Debugging.
As stated previously, the `$ docker-compose up run` will install Xdebug and configure the _remote host_ to be the
host system. The remote port will be `9001`.

It sets the `PHP_IDE_CONFIG=PHPSTORM` for browser-based debugging. This should be enough to interact with the remote debugger.

## Non Docker development
The system can be run without Docker, but that requires a lot of manual configuration. Have a look at the Dockerfile for complete
list of PHP extensions required.

Then run `$ composer i` to install dependencies.


## CommandLine

This systems comes with a few command-line commands that you can run from the project's `public`
directory like so:

```sh
$ php index.php [command-name]
```

| command-name     | args                                           | description                                                   |
|------------------|------------------------------------------------|---------------------------------------------------------------|
| index:assemblies |                                                | Calls events for adding/creating assemblies                   |
| index:assembly   | --assembly= -a                                 | Calls events for adding/creating assembly (and sub items)     |
| index:issue      | --assembly= -a, --issue= -i --category= -c     | Calls events for adding/creating issues (and sub items)       |
| index:session    | --assembly= -a                                 | Calls events for adding/creating congressman sessions         |

The index:* command are to force the RabbitMQ > [AlthingiAccumulator](https://github.com/fizk/AlthingiAccumulator) > MongoDB/Elasticsearch
pipeline to be run. This is only valuable when for some reason MongoDB and Elasticsearch need to be repopulated. In which case it's better
to run this commands through the Docker container. For that, there are scripts located in the `auto` directory that simplify this process:

```bash
$ docker-compose run -d api /var/www/auto/index-assembly.sh 150
$ docker-compose run -d api /var/www/auto/index-issues.sh 150 1 A
```

These script might run several command in the correct order.

## Other useful commands
All GET requests are cached, if you need to flush the cache, it can be done via
```bash
$ docker exec -it cache-api redis-cli FLUSHALL
```

To make a dump of the database, run
```bash
docker exec CONTAINER /usr/bin/mysqldump -u root --password=example althingi > backup.sql
```
## About developing with PHPStorm.
PHPStorm is a good IDE than can run PHP inside a Docker Container and connect to its Xdebug server. A few steps need to be
completed for this to work.

Interpreter:
A Docker image needs to be available that gets spun up each time the IDE want to run some PHP code. Create this container by calling
```bash
$ docker build -t althingi-source-runtime --build-arg ENV=development .
```

Dependencies:
You also need running containers for MySQL and MongoDB
```bash
$ docker run --name dev_api_database -e MYSQL_ROOT_PASSWORD=example -d einarvalur/althingi-source-db
$ docker run --name dev_api_store -d mongo:4.2.0-bionic
```
just make sure you run the containers from the project directory, so they end up in the same network.

Now it's time to link this all together

>>>>>>> 2d4211e08601541c1302717f150f4fbe620c9180
Go into Preferences > Languages & Frameworks > PHP
under CLI Interpreter, click the ... where you are taken to a prompt to create a new Interpreter. Click the + sign on the left
hand site and select From Docker, Vagrant Remote... Select Docker from the radio buttons and find the **dev_api:latest** docker image
from the dropdown where it says Image name.

Now you should have an PHP interpreter.

From the original Preferences > Languages & Frameworks > PHP dialog, click the folder icon next to _Docker container_, here you can
configure the docker container.

The volume binding should be
* /var/www/config	| <your host path to>/config
* /var/www/module	| <your host path to>/module
* /var/www/public	| <your host path to>/public
* /var/www/phpunit.xml	| <your host path to>/phpunit.xml.dist

Links should be
* dev_api_store	    | store
* dev_api_database	| database

The env variables should be
* WITH_XDEBUG             | true
* APPLICATION_ENVIRONMENT | development
* DB_HOST                 | dev_api_database
* DB_PORT                 | 3306
* DB_NAME                 | althingi
* DB_USER                 | root
* DB_PASSWORD             | example
* CACHE_TYPE              | none
* SEARCH                  | none
* LOG_PATH                | none
* QUEUE                   | none
* QUEUE_FORCED            | false
* STORAGE_HOST            | dev_api_store
* STORAGE_DB              | althingi

Lastly under Preferences > Languages & Frameworks > PHP > Test frameworks, make sure that the **althingi-source-runtime:latest** images
has been selected
and that _Use Composer autoloader_ is also selected, the path should be `/opt/project/vendor/autoload.php` and the default
configuration file is `/opt/project/phpunit.xml.dist`
