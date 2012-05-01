<?php
/*
 * Plugin Name: HTML binding with a Category
 * Author: Mahibul Hasan Sohag
 * Description: This plugin binds some html, jquery, css codes with a certain category. It has a great Admin UI
 * 
 * */
 
 define('HTMLBINDINGCATEGORY_DIR', dirname(__FILE__));
 define('HTMLBINDINGCATEGORY_FILE', __FILE__);
 include HTMLBINDINGCATEGORY_DIR . '/classes/category-bind.php';

 Category_Binding_With_Html :: init();
