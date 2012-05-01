<tr class="form-field">
	<th scope="row" valign="top">
		<label for="extra_html"><?php _ex('Html / Javascript code', 'Html block'); ?></label>
	</th>
	<td>
		<textarea name="extra_html" id="extra_html" rows="10" cols="50" style="width: 97%;">
			<?php echo $extra_html; // textarea_escaped ?>
		</textarea>
		<br />
		<span class="description">
		<?php _e('The html is not prominent by default, however if this field is not empty, this code will be shown to every post of this category.'); ?>
		</span>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="html_position"><?php _ex('Html position', 'Html position'); ?></label>
	</th>
	<td>
		<select class="postform" name="html_position">
			<option <?php selected('1', ''); ?> value="1">Top</option>
			<option <?php selected('2', ''); ?> value="2">Bottom</option>
			<option <?php selected('3', ''); ?> value="3">After First Paragraph</option>
		</select>
		<br />
		<span class="description">
			<?php _e('where you want to show the html to the posts of this category'); ?>
		</span>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="globally_used"><?php _ex('Globally Used', 'globally used'); ?></label>
	</th>
	<td>
		<input type="checkbox" name="globally_used" value="1" <?php checked('1', $globally_used); ?>  />
		<br />
		<span class="description">
			<?php _e('Use this to make the html/js global for all the post.'); ?>
		</span>
	</td>
</tr>
		
