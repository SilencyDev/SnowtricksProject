[![Codacy Badge](https://api.codacy.com/project/badge/Grade/4fddffbc3e4342b0868fece3dbb5827d)](https://www.codacy.com/manual/SilencyDev/SnowtricksProject?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=SilencyDev/SnowtricksProject&amp;utm_campaign=Badge_Grade)

# SnowtrickProject

Snowtrick App

## Install
1.  Clone or download the project via the command below
```
    git clone https://github.com/SilencyDev/SnowtricksProject
```
2.  Go to the .env file to add your database and email settings
3.  Download Composer and install/update it via the command below [Composer](https://getcomposer.org/download/) :
```
    composer install

    composer update
```
4.  Create the database :
```
    php bin/console doctrine:database:create
```
5.  Apply the migration :
```
    php bin/console doctrine:migrations:migrate
```
6.  Register and edit the database user->roles with ["ROLE_ADMIN"]
7.  The project is ready-to-use !