## Before You Begin ##
There are few things that you need to have |know| before you go any funther
  * A Working Joomla(Version 1.5) Site and Install Extensions - ([more info](http://www.joomla.org))
  * A Working Elgg(Version 1.5 or 1.6) Site - ([more info](http://www.elgg.org))
  * How to install and enable an **elgg plugin** ([more info](http://docs.elgg.org/wiki/Configuration/Plugins))

## What is JConnekt ##
**JConnekt** is a **Integration Engine** Which can Integrate External Application to [Joomla](http://www.joomla.org).Using JConnekt it makes easier the communication between Joomla and External Application. And also There can also be communication between External Application.

In the sense of Communication currently
  * users can be synchronized
  * activities can be shared

## What is an External Application ##
External Application is an application or a software which is going to be communicate with Joomla using JConnekt. In order to do that External Application much implement a set of methods and should give xmlrpc access to those methods...
Anyway this is not a wiki to tell you howto do that kind of work and also such a kind of document is to be arived.

> We have build a plugin which makes [Elgg](http://www.elgg.org) as an External Application and rest of this wiki will show You how to configure it with JConnekt. The configuration procedure is pretty much same for any application. (and few more External Applications are on our roadmap)

## Getting JConnekt ##
**JConnekt engine**
  * goto http://jconnekt.googlecode.com and download the latest JConnekt engine
**Elgg plugin**
  * goto http://jconnekt.googlecode.com and download the latest elgg\_JConnekt plugin

## Install JConnekt ##
Installing JConnekt is pretty much straight forward.All you have to do is goto administration section of Joomla and install downloaded JConnekt engine as an extension. **If successful you will a get a menu item called JConnekt in the Components menu**.If you click that you'll get an view like this.

![http://img150.imageshack.us/img150/5017/jconnect.jpg](http://img150.imageshack.us/img150/5017/jconnect.jpg)

## Configure JConnekt (with Elgg) ##
Actually there is no configurations you've do with JConnekt. All the things are automatically done by JConnekt.
**Only thing you've to do is configure an External Application in this case with Elgg**
In order to do that we need some information
  1. appName - A Unique name for your external Application [call this exApp](we.md)
  1. A secret key - which is used as a password to communicate with External Application
  1. Host - The host of the exApp (eg:- www.joomla.org)
  1. Path - The path to implemented methods which used to communicate (for elgg it's something like this : **/pg/JConnekt**)
  1. Port - The port used to communicate (normally it's 80)

That's all what you basically need..

### Configuring Elgg ###
OK then we'll need an ExApp in our case Elgg. but normally Elgg doesn't have built-in support for JConnekt. So need to plugin it. That's where our  [elgg\_JConnekt plugin](http://code.google.com/p/JConnekt/downloads/list?can=2&q=elgg&colspec=Filename+Summary+Uploaded+Size+DownloadCount) needs.Then install that plugin into Elgg. ([More Info](installElggPlugin.md))

Then You'll get a menu in the admin section named **JConnekt Configg** - thats where what we interested. click on that you'll get a view like this...

![http://img401.imageshack.us/img401/1702/elggjc.jpg](http://img401.imageshack.us/img401/1702/elggjc.jpg)

Now you can see basic requirement for JConnekt under the **Information for JConnekt**. copy those info.

Now goto JConnekt in Joomla and click External Applications then click addNew in the top bar... Then You'll get a view like this..

![http://img212.imageshack.us/img212/5756/addy.jpg](http://img212.imageshack.us/img212/5756/addy.jpg)

  1. Then fill appName as whatever you want
  1. generate a secret key by clicking generate button
  1. infomation next three field are the things what you've copied. Fill those;
  1. Then click apply..
  1. goto Elgg and paste the secret key for relevent textbox and do update.
  1. In the right handside of JConnekt there will be button named "Send Info"
  1. Click that also which sends necessary details to Elgg ExApp.
  1. Now You are done..

Go to the Elgg and again click the JConnekt config button..
Then You'll get a view like this.. (might be changed according to the version you are using)


![http://img135.imageshack.us/img135/2521/elggjcok.jpg](http://img135.imageshack.us/img135/2521/elggjcok.jpg)


Ok Now You've successfully,
  * downlaod and install JConnekt
  * configured an external Application with JConnekt
in future setting up an another External Application is pretty much the same.

In the next-wikies features of JConnekt will be described.. And also leaning by the scrach will be encouraged!