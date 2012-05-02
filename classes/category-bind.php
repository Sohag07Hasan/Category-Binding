<?php

/*
 * Controls all the stuff
 * */
 
class Category_Binding_With_Html{
	public static function init(){
		//hooks from wp-admin/edit-tag-form.php
		//creates 3 extra fields 
		add_action('category_edit_form_fields', array(get_class(), 'category_edit_form'), 100, 2);
		
		//save these extra three field data
		add_action('edited_category', array(get_class(), 'save_category_data'), 100, 2);
		
		//creating table to store the data
		register_activation_hook(HTMLBINDINGCATEGORY_FILE, array(get_class(), 'create_the_table'));
		
		//adding custom column with the category table
		//add_filter('manage_category__custom_column', array(get_class(), 'add_custom_column'), 10, 3);
		
		//category delte hook
		add_action('delete_category', array(get_class(), 'delete_category'), 10, 2);
		
		// filter the content 
		add_filter('the_content', array(get_class(), 'content_filter'));
	}
	
	
	/*
	 * Add the extra html or js from the category with the post
	 * */
	 static function content_filter($content){
		 global $post;
		 $global_cat = self::get_global_term();
		 
		 if($global_cat){
			$category = $global_cat;
		 }
		 else{
			$categories = get_the_category($post->ID);
			$category = $categories[0]->term_id;
		 }
		 
		return self::sanitized_content($content, $category);	
		 
	 }
	 
	 
	 
	 /*
	  *sanitize the content 
	  * */
	  static function sanitized_content($content, $category=null){
			if(empty($category)) return $content;
			
		//var_dump($category);	
			global $wpdb;
			$table = self::get_table_name();
			$options = $wpdb->get_row("SELECT * FROM `$table` WHERE `term_id` = '$category'");
						
			
			if(strlen($options->html_js) < 5) return $content;
			
			if($options->position == 1){
				return stripslashes($options->html_js) . $content;
			}
			if($options->position == 2){
				return $content . stripslashes($options->html_js);
			}
			if($options->position == 3){
				preg_match('%(<p[^>]*>.*?</p>)%i', $content, $matches);
				if(empty($matches)) return $content;
				
				$first_portion = $matches[0];
				$last_portion = preg_replace('%(<p[^>]*>.*?</p>)%i', stripslashes($options->html_js), $content, 1);
				
				return $first_portion . $last_portion;
			}
			
			return $content;			 
			 
	  }
	 
	 
	
	/*
	 * Deletes form the custom table
	 * */
	 static function delete_category($term, $tt_id){
		 $table = self::get_table_name();
		 global $wpdb;
		 $wpdb->query("DELETE FROM `$table` WHERE `term_id` = '$term'");
		 return;
	 }
	
	
	/*
	 * Extends the category form
	 * */
	static function category_edit_form($tag, $taxonomy){		
		$term_meta = self::get_term_meta($tag->term_id);
		$global_term = self::get_global_term();		
		
		//including the form
		include dirname(__FILE__) . '/form.php';
	}
	 
	 
	
	/*
	 * get Term meta data from custom table
	 * */
	 static function get_term_meta($term_id){
		$table = self::get_table_name();
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM `$table` WHERE `term_id` = '$term_id'");
	 }
	 
	
	
	/*
	 * Save the extra field values
	 * */
	static function save_category_data($term_id, $tt_id){
		//skip for the ajax
		if(self::ajax_enabled()) return ;						
				
		global $wpdb;
		$table = self::get_table_name();
		$position = (int) $_POST['html_position'];
		//$html =  htmlentities($_POST['extra_html']);
		$html =  $_POST['extra_html'];
		
		self::set_global_term($term_id);
		
		if(self::term_id_exsits($term_id)){
			$wpdb->update($table, array('html_js'=>$html, 'position'=>$position), array('term_id'=>(int)$term_id), array('%s', '%d'), array('%d'));
		}
		else{
			$wpdb->insert($table, array('html_js'=>$html, 'position'=>$position, 'term_id'=>(int)$term_id), array('%s', '%d', '%d'));
		}
		
		return;
	}
	
	
	/*
	 * if ajax action is occured simply return true
	 * */
	 static function ajax_enabled(){		 		 
		return (isset($_POST['ajax_enalbed']) &&  $_POST['ajax_enalbed'] == 'n') ? false : true; 
	 }
	 
	
	
	/*
	 * Globally set the category for the html
	 * */
	 static function set_global_term($term_id){
		 
		if($_POST['globally_used'] == '1') :
			update_option('global_category_binding', $term_id);
		else :
			delete_option('global_category_binding');
		endif;
		
	 }
	 
	 
	 /*
	 *get the global term 
	 * */
	 static function get_global_term(){
		return get_option('global_category_binding', false);
	 }
	
	
	/*
	 * creates the table while the plugin is activated
	 * */
	 static function create_the_table(){
		$table = self::get_table_name();
		$sql = "CREATE TABLE IF NOT EXISTS $table(
			`id` bigint unsigned NOT NULL AUTO_INCREMENT,
			`term_id` bigint unsigned NOT NULL,
			`position` tinyint NOT NULL,
			`html_js` longtext DEFAULT NULL,
			PRIMARY KEY(id),
			UNIQUE(term_id)	 
		)";
		
		if(!function_exists('dbDelta')) :
				include ABSPATH . 'wp-admin/includes/upgrade.php';
		endif;
		dbDelta($sql);
	 }
	 
	 
	 /*
	  * returns table name
	  * */
	  
	  static function get_table_name(){
		global $wpdb;
		return $wpdb->prefix . 'category_binding'; 
	  }
	  
	  
	  
	  /*
	   * returns true if term id exists
	   * */
	   static function term_id_exsits($term_id){
			global $wpdb;
			$table = $table = self::get_table_name();
			return $wpdb->get_var("SELECT `id` FROM `$table` WHERE `term_id` = '$term_id'");
	   }
}
