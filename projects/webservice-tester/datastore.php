<?php

/*
 * json_incoming message
 * {
 *  method:'store_send',
 *  value:'the value'
 *  id:'id',
 *  message:''
 * }
 * 
 * {
 * 	method:'store_recieve',
 *  value:'the value'
 * }
 * 
 * //this will return all the data available in the store and reset it.
 * {
 * 	method:'store_pop'
 * }
 * 
 * json outgoing message for store_pop
 * [
 * 	{
 * 		id:'the id',
 * 		value_send:'the value'
 * 		value_recieve:'the_value'
 * 		message:'the_message'
 * ]
 * 
 */