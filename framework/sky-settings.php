<table class="widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="5" data-export-label="<?php esc_html_e( 'Home Page', 'sky-seo' ); ?>">
				<?php esc_html_e( 'Home Page', 'sky-seo' ); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="20%"><?php esc_html_e( 'Title', 'sky-seo' ); ?></td>
			<td width="60%"><input type="text" name="sky_seo[title_home]" value="<?php echo $title_home; ?>"></td>
		</tr>
		<tr>
			<td width="20%"><?php esc_html_e( 'Description', 'sky-seo' ); ?></td>
			<td width="60%"><input type="text" name="sky_seo[description_home]" value="<?php echo $description_home; ?>"></td>
		</tr>
		<tr>
			<td width="20%"><?php esc_html_e( 'Background Sharing', 'sky-seo' ); ?></td>
			<td width="50%">
				<div id="images_home"><img width="100px" height="60px" src="<?php echo $images_home_url; ?>" /></div>
				<input type="hidden" id="images_homes" name="sky_seo[images_home]" value="<?php echo $images_home_url; ?>" />
			</td>
			<td width="30%">
				<button type="button" class="upload_image_button button" data-id="images_home"><?php esc_html_e('Upload/Add image', 'sky-game'); ?></button>
				<button type="button" class="remove_image_button button" data-id="images_home"><?php esc_html_e('Remove image', 'sky-game'); ?></button>
			</td>
		</tr>
	</tbody>
</table>