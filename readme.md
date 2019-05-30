# tkbase  

__Project:__ tkbase    
__Web:__ <http://www.tropotek.com/>  
__Authors:__ Michael Mifsud <http://www.tropotek.com/>  

A base site using the Tk framework, use this as a starting point for your own site.

## Contents

- [Installation](#installation)
- [Introduction](#introduction)

## Installation

  1. First setup a database for the site and keep the login details handy.
  2. Make sure you have the latest version of composer [https://getcomposer.org/download/] installed.
  3. Use the following commands:  
~~~bash
# git clone https://github.com/tropotek/tkbase.git
# cd tkbase
# composer install
~~~
  4. Edit the `/src/App/config/config.php` file to your required settings.
  5. You may have to change the permissions of the `/data/` folder so apache can read and write to it.
  6. To enable debug mode and logging edit the `/src/config/config.php` file to suit your server.
     tail the log for more info on any errors.


## Upgrading

~~~bash
# git reset --hard
# git checkout master
# git pull
# composer update
~~~

__Warning:__ This could potentially break the site. Be sure to backup any DB and 
site files before running these commands


## Introduction

### Editing The HTML

In the folder `/html` you will find all the content templates for the site.


#### Template URL paths
When editing a path to a file there are 3 types of paths, use the first one to access theme files and use the
other two to access files outside of the theme. 
For example if we where editing the public.html template:

  1. Theme Relative: `./css/somefile.css` would be translated into `http://domain.com/html/public/css/somefile.css`
  2. Full Path 1: `css/somefile.css` would be translated into `http://domain.com/css/somefile.css`
  3. Full Path 2: `/css/somefile.css` would be translated into `http://domain.com/css/somefile.css`



https://github.com/ccxt/ccxt/wiki/Manual





