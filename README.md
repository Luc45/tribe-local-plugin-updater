# Tribe Local Plugin Updater

This is an unofficial little script to automate some borings parts of updating your local development Modern Tribe plugins to the latest version. It basically does this:

- Stash whathever changes you have
- Git checkout master
- Git pull
- NVM Use 8.9.4
- composer install
- npm install
- npm build
- Apply the stash back again

On all of these folders:

- event-tickets
- event-tickets-plus
- events-community
- events-community-tickets
- events-filterbar
- events-pro
- event-tickets/common
- the-events-calendar/common

### Requirements
- NVM (https://github.com/nvm-sh/nvm)
- A local environment that has all of the Modern Tribe plugins listed above installed from Github 

### How to use this
- cd into your plugins folder `cd your-main-site/wp-content/plugins`
- clone this repo in a subfolder, as if it were just another plugin
- run `composer install`
- run `php updater.php`
- grab a cup of coffee while it runs

```
   ( (
    ) )
  ........
  |      |]
  \      / 
   `----'
```
