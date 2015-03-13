

## Intro ##

This is an introduction of Features available in JConnekt currently and upcoming features end of the GSOC 2009 time period

## Currently Available Features ##
  * Fully User Synchronization between joomla and ExApp
  * Bulk Synchronization
  * Sync User Mangement
  * Central Authentication System (Joomla based but flexible)
  * Cross Domain Single Sign On
  * Cross Domain Single Sign Out
  * Activity Module inside Joomla
  * Simple External Application Setup/Configuration
  * Elgg External Application Plugin
  * User Groups Mapping

## Detailed Overview ##
### Fully User Synchronization between joomla and ExApp ###
Once You've installed and configured JConnekt correctly with an External Application (ExApp) You can use this feature. and it's automatically activated!.
What this does is
  * If you create a new user Joomla that user can be used to logged into ExApp
  * And JConnekt make sure that user is synced with ExApp
  * So that you can update user-details from ExApp and chages will apply to all the ExApps and to the Joomla
  * And the vise-versa of this  case is also the true

### Bulk Synchronization ###
  * efore you installed JConnekt there could be users available in both Joomla and in the ExApp.
  * o this will help you to synchronize existing users in both ExApps and Joomla.
  * nd it's possible to have username conflicts and way to manage those conflicts is also provided.

![http://img5.imageshack.us/img5/576/bulksync.gif](http://img5.imageshack.us/img5/576/bulksync.gif)

### Sync User Management ###
  * All the times you have to control your users... And JConnekt supports that also
  * You can ban users from certail ExApp to login with Joomla and stop synchronization
  * The interface provided is very helpful and using existing Joomla admin controls.. So using it'll be very easy

![http://img198.imageshack.us/img198/6025/syncuser.gif](http://img198.imageshack.us/img198/6025/syncuser.gif)

### Cross Domain Single SignIn and SignOut ###
  * It's always tricky and hard to work with Cross domain stuff...
  * JConnekt Support Single SignnOn and Single SignOut between JConnekt ExApps and Joomla
  * In order to achieve that when you are login or logout in ExApps you've to use customized login system which is shown in the section below
  * These SSO is achieved using OpenID like technology

### Central Authentication System ###
  * JConnekt has a Joomla based Central Authenticating System but it's flexible enough allow users from the ExApps to authenticating using this system
  * That means authentication is based Joomla but you can login to ExApp1 with the user-account at ExApp2 with this system

![http://img5.imageshack.us/img5/5624/centrallogin.gif](http://img5.imageshack.us/img5/5624/centrallogin.gif)

### Activity Module inside Joomla ###
  * JConnekt provide a module for show Activities inside ExApp, in the Joomla.
  * It has 2 views
    * one for public users
    * other for logged in users
  * the activity module for Elgg is shown below..

![http://img5.imageshack.us/img5/204/activitymodule.gif](http://img5.imageshack.us/img5/204/activitymodule.gif)

### Simple External Application Setup/Configuration ###
  * JConnekt provides you to setup and configure ExApps in a minimal effort..
  * And Managment and Configuration of those application can be done in single view..
  * JConnekt provide ExApp to ExApp configuration system. means what are the rules active for ExApp1 doesn't valid for ExApp2

![http://img41.imageshack.us/img41/6431/simpleconfig.gif](http://img41.imageshack.us/img41/6431/simpleconfig.gif)

### Elgg External Application Plugin ###
  * I've mentioned several times Elgg in this wiki...
  * Yes, currently we have developed a Elgg plugin to convert Elgg into a JConnekt ExApp.
  * In future more to come.. and you can create your own using JConnekt ExApp API .. (which will be comming soon)

### User Groups Mapping ###
![http://img370.imageshack.us/img370/3391/screenshot.gif](http://img370.imageshack.us/img370/3391/screenshot.gif)

we do the user-group mapping by divinding them into two groups.
  1. incoming - for the incoming users (like update/create from exApp)
  1. outgoing - for the outgoing users (when existing joomla users logged-into into exApp)

NOTE: Owner of the user's user group for particular user cannot be changed by other exApp or Joomla!