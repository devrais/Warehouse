Credy - Software developer position's test tasks
============================

Task 2
Write from scratch an application in PHP called “Warehouse”.

Description

There is warehouse with many employees who can add/edit/delete products to stock. Also there are
many categories of products. Such case is possible that one product belongs to many categories.
There should be possibility to add and remove new employees to the system. Product should
contain picture, description, price and whatever.

**We'd like to see from your work:**

- Yii2 Framework
- MySQL
- AJAX
- Clean HTML and CSS
- Publish on GitHub/Bitbucket

USED TOOLS
----------

- Project was done on Ubuntu operation system
- Server: XAMPP 5.6.21 / PHP 5.6.21

DOWNLOAD PROJECT
-------------------

Download url: 

~~~
https://github.com/devrais/Warehouse.git
~~~

INSTALLATION
------------

### Download files to your server

After download you will need to install "Vendor" files using the following command:

~~~
composer update
~~~

Now you should be able to access the application through the following URL, assuming basic is the directory directly under the Web root.

~~~
http://localhost/basic/web/
~~~

CONFIGURATION
-------------

### Database

You will use "warehouse" database to store your data

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;dbname=warehouse',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.

### Create tables

Create tables in this order !!!

```sql
CREATE TABLE `Employee` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL,
	`email` VARCHAR(100) NOT NULL,
	`username` VARCHAR(100) NOT NULL,
	`password` VARCHAR(100) NOT NULL,
	`position` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `Product` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	`description` TEXT NOT NULL,
	`picture` VARCHAR(200) NOT NULL,
	`price` DECIMAL(10,2) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `Category` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `CategoryMap` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`product_id` INT(11) NOT NULL,
	`category_id` INT(11) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `product_id` (`product_id`),
	INDEX `category_id` (`category_id`),
	CONSTRAINT `CategoryMap_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`),
	CONSTRAINT `CategoryMap_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `Category` (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;
```
Sql code is also available in db/sql.txt

"Product" table and "Category" table are connected by "CategoryMap" table 

### Import Employyes

In "commands" folder use "EmployeeController.php" to import employees using console commands.

If on Linux:

~~~
./yii employee/load-employees
~~~

If on Windows using XAMPP bash:

~~~
php yii employee/load-employees
~~~

Password for every employee is "123456". I suggest using "Andrei" with username "devrais" because he has the "owner" status (like admin).

STRUCTURE
-------------

### Employees

Employee Model is used for "USER AUTHENTICATION"

"Warehouse" has 3 types of employees - workers, managers and owners. Everyone has their own permissions for using the "Warehouse" (see rbac folder).
 Password for every employee is "123456". I suggest using "Andrei" with username "devrais" because he has the "owner" position (like admin).

- Workers can create, update, delete products.
- Managers can do everything that workers can and create, update categories. Cant delete categories
- Owners can do everything like super user. 

### Category

Category Model adds new categories to "warehouse". Only managers and owner can create new categories. WITHOUT categories you CANT add new products.

### CategoryMap

CategoryMap model creates relation between categories and products. This allows products to have different categories.

### Products

Products Models adds new products to "warehouse".

### Manager.php

Since every product can have different categories, product create and update require complex logic for it.
For this I created additional "helper" class in components/managers/Manager.php

Everything else is traditional MVC structure.

