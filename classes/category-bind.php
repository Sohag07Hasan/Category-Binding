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
	}
	
	static function category_edit_form($tag, $taxonomy){
		//including the form
		include dirname(__FILE__) . '/form.php';
	}
	
	
	/*
	 * Save the extra field values
	 * */
	static function save_category_data($term_id, $tt_id){
		global $wpdb;
		$table = self::get_table_name();
		$position = (int) $_POST['html_position'];
		$html = $_POST['extra_html'];
		
		self::set_globally($term_id);
		
		if(self::term_id_exsits($term_id)){
			$wpdb->update(array('html_js'=>$html, 'position'=>$position), array('term_id'=>(int)$term_id), array('%s', '%d'), array('%d'));
		}
		else{
			$wpdb->insert(array('html_js'=>$html, 'position'=>$position, 'term_id'=>(int)$term_id), array('%s', '%d', '%d'));
		}
		
		return;
	}
	
	
	/*
	 * Globally set the category for the html
	 * */
	 static function set_globally($term_id){
		if(isset([$_POST['globally_used']) && !empty($_POST['globally_used'])) :
			update_option('global_category_binding', $term_id);
		endif;
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
