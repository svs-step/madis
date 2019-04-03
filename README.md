# SOLURIS - Madis

## Getting Started
These instructions will get you a copy of the project
```
git clone git@gitlab.com:soluris/madis.git
```
See deployment section below to deploy the project

### Prerequisites
* docker

### Installing
Copy `.env.dist` to `.env` and edit it with custom values

Then Connect your docker to the GitLab with your GitLab credentials
```bash
docker login registry.gitlab.com
```

Finally start project by using Docker
```bash
./docker-service initialize
```

## Basic Usages

* Project URL : http://127.0.0.1:8888

## Fixtures user

- **Admin:** admin@awkan.fr / 111111 
- **Collectivity 1:** collectivite@awkan.fr / 111111 
- **Collectivity 2:** collectivite2@awkan.fr / 111111 
- **Preview:** lecteur@awkan.fr / 111111 
- **Inactive:** inactif@awkan.fr / 111111 

## Running the tests
```
./docker-service tests              # Run quality tests, unit tests and functionnal tests
./docker-service unitTests          # Run unit tests
./docker-service qualityTests       # Run quality tests
./docker-service functionnalTests   # Run functionnal tests
```

## Deployment
* __preprod__ : http://5.39.39.104:8020
* __prod__ : https://madis.soluris.fr

## Development usages
* Git process is git flow
* Tests are written with PHP Unit
* Code and styles must be verified by CSFixer and PHPLint before being committed, see [how to configure git hooks](.git-hooks/README.md)

# Built With
This project works with :
* [Symfony 3 Flex](http://symfony.com/doc/current/index.html) - As backend framework
* [AdminLTE 2](https://adminlte.io/) - As CSS Framework

## Contributors
* __BOURLARD Donovan__ - Initial work
