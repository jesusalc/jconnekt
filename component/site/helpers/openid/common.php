<?php
include_once "openid/consumer.php"; 

function &getConsumer() {
    /**
     * Create a consumer object using the store object created
     * earlier.
     */
    $store = getStore();
    $consumer =& new Auth_OpenID_Consumer($store);
    return $consumer;
}

function &getStore() {

    $store_path = "/tmp/_jc_openid_consumer_generic";

    if (!file_exists($store_path) &&
        !mkdir($store_path)) {
        print "Could not create the FileStore directory '$store_path'. ".
            " Please check the effective permissions.";
        exit(0);
    }

    return new Auth_OpenID_FileStore($store_path);
}