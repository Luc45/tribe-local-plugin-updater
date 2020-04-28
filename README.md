# Tribe Local Plugin Updater

This is an unofficial dirty little script to automate some borings parts of updating your local development Modern Tribe plugins to the latest version. It basically:

- Stash whatever changes you have
- Git checkout master
- Git pull
- NVM Use 8.9.4, if NVM is installed
- composer install
- npm install
- npm run build
- Pop the stash back again

On all of these folders:

- event-tickets/common
- the-events-calendar/common
- the-events-calendar
- events-pro
- event-tickets
- event-tickets-plus
- events-community
- events-community-tickets
- events-filterbar

### Requirements
- NVM (https://github.com/nvm-sh/nvm), or a node version that is compatible with what the plugin uses (eg: node 8.9.4)
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
