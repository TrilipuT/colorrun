<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 8/20/17
 * Time: 20:46
 */

namespace modules\coupons;


use WPKit\AdminPage\CustomPage;

class GeneratorPage extends CustomPage {
	private $coupons = [];
	private $generated_codes = [];

	public function render() {
		?>
        <div class="wrap">
            <h1><?= $this->get_title() ?></h1>
			<?php $codes = apply_filters( 'generated_codes_list', [] );
			if ( $codes ):?>
                <div>
                    <h2>List of generated codes</h2>
					<?php foreach ( $codes as $code ): ?>
						<?= $code ?><br>
					<?php endforeach; ?>
                </div>
			<?php endif; ?>
            <form method="post">
				<?php //wp_nonce_field(); ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="count"><?php _e( 'Count', 'colorrun' ); ?></label></th>
                        <td><input name="count" type="number" id="count" class="regular-text" min="1" value="10"
                                   required="required">
                            <p class="description"><?php _e( 'How many coupons to generate.' ) ?></p></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="amount"><?php _e( 'Amount', 'colorrun' ); ?></label></th>
                        <td><input name="amount" type="number" id="amount" class="regular-text" min="0"
                                   required="required">
                            <p class="description" id="tagline-description"><?php _e( 'Amount of discount.' ) ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Type', 'colorrun' ) ?></th>
                        <td>
							<?php foreach ( Coupon::get_types() as $value => $type ) : ?>
                                <label><input type="radio" name="type" value="<?= $value ?>"
                                              required="required"><?= $type ?></label><br>
							<?php endforeach; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
				<?php submit_button( __( 'Generate', 'colorrun' ) ) ?>
            </form>
        </div>
		<?php
	}

	protected function _post_action() {
		$count  = (int) $_POST['count'];
		$amount = (int) $_POST['amount'];
		$type   = (int) $_POST['type'];
		if ( ! $count ) {
			$this->show_error( __( 'Could not create. Enter count of codes to generate.', 'colorrun' ) );
		}

		for ( $i = 0; $i < $count; $i ++ ) {
			$code = $this->create_coupon( $this->generate_code(), $amount, $type );
			array_push( $this->generated_codes, $code );
		}
		$codes = $this->generated_codes;
		add_filter( 'generated_codes_list', function () use ( $codes ) {
			return $codes;
		} );
		$this->show_success( sprintf( __( '%d coupons successfully generated', 'colorrun' ), $count ) );
	}

	private function show_error( string $message ): void {
		$this->show_message( 'error', $message );
	}

	private function show_message( string $type, string $message ): void {
		add_action( 'admin_notices', function () use ( $type, $message ) {
			$class = 'notice notice-' . $type;
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		} );
	}

	private function create_coupon( string $code, int $amount, int $type ): string {
		while ( true ) {
			$coupon = new Coupon( [
				'code'    => $code,
				'used'    => 0,
				'amount'  => $amount,
				'type'    => $type,
				'created' => date( "Y-m-d H:i:s" ),
				'status'  => Coupon::STATUS_ACTIVE,
			] );

			if ( $coupon->save() ) {
				return $code;
				break;
			}
		}
	}

	private function generate_code(): string {
		return strtoupper( wp_generate_password( 12, false ) );
	}

	private function show_success( string $message ): void {
		$this->show_message( 'success', $message );
	}

	protected function _ajax_action() {
		// TODO: Implement _ajax_action() method.
	}
}