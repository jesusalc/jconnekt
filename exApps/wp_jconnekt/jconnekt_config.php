<?php 
	$secret_key="";
	$app_name="";
	$joomla_url="";
	if(isset($_POST['submit'])){
		$secret_key=$_POST['jconnekt_secret_key'];
		$app_name=$_POST['jconnekt_app_name'];
		$joomla_url=$_POST['jconnekt_joomla_url'];
		update_option('jconnekt_secret_key',$secret_key);
		update_option('jconnekt_app_name',$app_name);
		update_option('jconnekt_joomla_url',$joomla_url);
	}
	else{
		$secret_key=get_option('jconnekt_secret_key');
		$app_name=get_option('jconnekt_app_name');
		$joomla_url=get_option('jconnekt_joomla_url');
		
	}
?>
		<div class="wrap">
			<?php    echo "<h2>" . __( 'JConnekt Configuration Panel', 'oscimp_trdom' ) . "</h2>"; ?>
			
			<form name="jconnekt_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="jconnekt_hidden" value="Y">
				<?php    echo "<h4>" . __( 'JConnekt Configuration Settings', 'jconnekt_trdom' ) . "</h4>"; ?>
				<p><?php _e("Secret Key: " ); ?><input type="text" name="jconnekt_secret_key" value="<?php echo $secret_key; ?>" size="80"></p>
				<p><?php _e("App Name: " ); ?><input type="text" name="jconnekt_app_name" value="<?php echo $app_name; ?>" size="20"></p>
				<p><?php _e("Joomla URL: " ); ?><input type="text" name="jconnekt_joomla_url" value="<?php echo $joomla_url; ?>" size="85"></p>
				
			
				<p class="submit">
				<input type="submit" name="submit" value="<?php _e('Update', 'jconnekt_trdom' ) ?>" />
				</p>
			</form>
		</div>
	
	