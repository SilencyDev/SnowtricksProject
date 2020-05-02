# SnowtrickProject

Snowtrick App

## Install
1.  Clone or download the project via the command below
```
    git clone https://github.com/SilencyDev/SnowtricksProject
```
2.  Go to the .env file to add your database and email settings
3.  Download Composer and install it via the command below [Composer](https://getcomposer.org/download/) :
```
    composer install
```
4.  Create the database :
```
    php bin/console doctrine:database:create
```
5.  Apply the migration :
```
    php bin/console doctrine:migrations:migrate
```
6.  The project is ready-to-use !