<?php
/**
* @author		Arunoda Susiripala
* @package		jconnect
* @subpackage	elgg
* @copyright	Arunoda Susiripala
* @license 		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
*/
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
?>

<style type="text/css">
#river,
.river_item_list {
	border-top:1px solid #dddddd;
}
.river_item p {
	margin:0;
	padding:0 0 0 21px;
	line-height:1.1em;
	min-height:17px;
}
.river_item {
	border-bottom:1px solid #dddddd;
	padding:2px 0 2px 0;
}
.river_item_time {
	font-size:90%;
	color:#666666;
}
/* IE6 fix */
* html .river_item p { 
	padding:3px 0 3px 20px;
}
/* IE7 */
*:first-child+html .river_item p {
	min-height:17px;
}
.river_user_update {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_profile.gif) no-repeat left -1px;
}
.river_object_user_profileupdate {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_profile.gif) no-repeat left -1px;
}
.river_object_user_profileiconupdate {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_profile.gif) no-repeat left -1px;
}
.river_object_annotate {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_bookmarks_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_bookmarks.gif) no-repeat left -1px;
}
.river_object_bookmarks_comment {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_status_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_status.gif) no-repeat left -1px;
}
.river_object_file_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_files.gif) no-repeat left -1px;
}
.river_object_file_update {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_files.gif) no-repeat left -1px;
}
.river_object_file_comment {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_widget_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_plugin.gif) no-repeat left -1px;
}
.river_object_forums_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_forums_update {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_widget_update {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_plugin.gif) no-repeat left -1px;	
}
.river_object_blog_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_blog.gif) no-repeat left -1px;
}
.river_object_blog_update {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_blog.gif) no-repeat left -1px;
}
.river_object_blog_comment {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_forumtopic_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_user_friend {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_friends.gif) no-repeat left -1px;
}
.river_object_relationship_friend_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_friends.gif) no-repeat left -1px;
}
.river_object_relationship_member_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_thewire_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_thewire.gif) no-repeat left -1px;
}
.river_group_join {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_groupforumtopic_annotate {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_groupforumtopic_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_forum.gif) no-repeat left -1px;
}
.river_object_sitemessage_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_blog.gif) no-repeat left -1px;	
}
.river_user_messageboard {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;	
}
.river_object_page_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_pages.gif) no-repeat left -1px;
}
.river_object_page_top_create {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_pages.gif) no-repeat left -1px;
}
.river_object_page_top_comment {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}
.river_object_page_comment {
	background: url(<?php echo $CONFIG->url; ?>_graphics/river_icons/river_icon_comment.gif) no-repeat left -1px;
}

</style>

<?php
$river = elgg_view_river_items(0, 0, '', '', '', '',8,0,0,false);
    $river.="<a href='{$CONFIG->url}mod/riverdashboard/'>more..</a>";
	echo "<div style='' >$river</div>";
?>
