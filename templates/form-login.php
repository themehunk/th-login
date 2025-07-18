<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get settings
$form_fields_settings = json_decode( get_option( 'thlogin_form_fields_settings', '{}' ), true );
$general_settings     = json_decode( get_option( 'thlogin_general_settings', '{}' ), true );
$login_fields         = $form_fields_settings['login'] ?? array();
?>

<div class="thlogin-form thlogin-form--login" data-form-type="login">
	<?php require THLOGIN_PATH . 'templates/parts/form-header.php'; ?>

	<form class="thlogin-ajax-form" data-form-type="login">
		<div class="thlogin-messages" aria-live="polite"></div>

		<h3><?php esc_html_e( 'Login', 'th-login' ); ?></h3>

		<?php foreach ( $login_fields as $field ) :
			if ( ! empty( $field['hidden'] ) ) {
				continue;
			}

			$type        = sanitize_text_field( $field['type'] ?? 'text' );
			$name        = sanitize_key( $field['name'] ?? '' );
			$id          = sanitize_key( $field['id'] ?? 'field_' . wp_generate_uuid4() );
			$label       = sanitize_text_field( $field['label'] ?? '' );
			$placeholder = sanitize_text_field( $field['placeholder'] ?? '' );
			$required    = ! empty( $field['required'] );
			$icon        = sanitize_text_field( $field['icon'] ?? '' );

			$field_class = 'thlogin-form-field';
			if ( $type === 'checkbox' ) {
				$field_class .= ' thlogin-form-field--checkbox';
			}

			$autocomplete = '';
			if ( stripos( $name, 'user' ) !== false ) {
				$autocomplete = 'username';
			} elseif ( stripos( $name, 'email' ) !== false ) {
				$autocomplete = 'email';
			} elseif ( stripos( $name, 'pass' ) !== false ) {
				$autocomplete = 'current-password';
			}
			?>
			<div class="<?php echo esc_attr( $field_class ); ?>">
				<?php if ( $type === 'checkbox' ) : ?>
					<input
						type="checkbox"
						name="<?php echo esc_attr( $name ); ?>"
						id="<?php echo esc_attr( $id ); ?>"
						value="1"
						<?php echo $required ? 'required' : ''; ?>
					/>
					<label for="<?php echo esc_attr( $id ); ?>">
						<?php echo esc_html( $label ); ?>
						<?php if ( $required ) : ?><span class="th-required">*</span><?php endif; ?>
					</label>
				<?php else : ?>
					<label for="<?php echo esc_attr( $id ); ?>" class="thlogin-label-with-icon">
						<?php if ( $icon ) : ?>
							<span class="thlogin-label-icon"><?php echo wp_kses_post( th_login_get_icon_svg( $icon ) ); ?></span>
						<?php endif; ?>
						<span class="thlogin-label-text">
							<?php echo esc_html( $label ); ?>
							<?php if ( $required ) : ?><span class="th-required">*</span><?php endif; ?>
						</span>
					</label>

					<input
						type="<?php echo esc_attr( $type ); ?>"
						name="<?php echo esc_attr( $name ); ?>"
						id="<?php echo esc_attr( $id ); ?>"
						placeholder="<?php echo esc_attr( $placeholder ); ?>"
						<?php echo $required ? 'required' : ''; ?>
						<?php if ( $autocomplete ) : ?>autocomplete="<?php echo esc_attr( $autocomplete ); ?>"<?php endif; ?>
					/>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		<div class="thlogin-form-submit">
			<button type="submit" class="thlogin-button thlogin-button--primary">
				<?php esc_html_e( 'Log In', 'th-login' ); ?>
			</button>
		</div>

		<div class="thlogin-form-links">
			<a href="#" class="thlogin-link" data-th-popup-action="forgot-password"><?php esc_html_e( 'Forgot Password?', 'th-login' ); ?></a>
			<?php if ( ! empty( $general_settings['form_type'] ) && $general_settings['form_type'] === 'double' ) : ?>
				<span class="thlogin-link-separator">|</span>
				<a href="#" class="thlogin-link" data-th-popup-action="register"><?php esc_html_e( 'Register', 'th-login' ); ?></a>
			<?php endif; ?>
		</div>
	</form>

	<?php require THLOGIN_PATH . 'templates/parts/form-footer.php'; ?>
</div>
