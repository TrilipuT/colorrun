<?php
/**
 * Created by PhpStorm.
 * User: vitaly
 * Date: 8/20/17
 * Time: 20:46
 */

namespace modules\participant;


use SimpleExcel\SimpleExcel;
use WPKit\AdminPage\CustomPage;

class ImportExportPage extends CustomPage {

	public function render() {
		wp_enqueue_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js' );
		wp_enqueue_style( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );

		?>
        <div class="wrap">
            <h1><?= $this->get_title() ?></h1>
            <h2 class="title"><?= __( 'Export' ) ?></h2>
            <form method="post">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label
                                    for="export_id"><?php _e( 'Choose what to export', 'colorrun' ); ?></label>
                        </th>
                        <td>
                            <select name="export_id" id="export_id" class="regular-text select2" required="required">
								<?php foreach ( \modules\event\Functions::get_all_events()->posts as $event ) : ?>
                                    <optgroup label="<?= get_the_title( $event ) ?>">
										<?php foreach ( \modules\distance\Functions::get_distances( $event->ID )->posts as $distance ) : ?>
                                            <option value="<?= $distance->ID ?>"><?= get_the_title( $distance ) ?></option>
										<?php endforeach; ?>
                                    </optgroup>
								<?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name='type' value="export">
				<?php submit_button( __( 'Export' ) ) ?>
            </form>
            <h2 class="title"><?= __( 'Import' ) ?></h2>
            <form method="post">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="count"><?php _e( 'File', 'colorrun' ); ?></label></th>
                        <td><input type="file" name="import_file">
                            <p class="description">Archive could contain any amount of files, could have
                                subdirectories.</p></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="export_id"><?php _e( 'Distance', 'colorrun' ) ?></label></th>
                        <td>
                            <select name="import_id" id="import_id" class="regular-text select2" required="required">
								<?php foreach ( \modules\distance\Functions::get_distances()->posts as $distance ) : ?>
                                    <option value="<?= $distance->ID ?>"><?= get_the_title( $distance ) ?></option>
								<?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name='type' value="import">
				<?php // submit_button( __( 'Import' ) ) ?>
            </form>
            <script type="text/javascript">jQuery(function () {
                    jQuery(".select2").select2();
                });</script>
        </div>
		<?php
	}

	protected function _post_action() {
		if ( isset( $_POST['type'] ) ) {
			if ( $_POST['type'] == 'export' ) {
				$this->export();
			} else if ( $_POST['type'] == 'import' ) {
				$this->import();
			}
		}
	}

	private function export() {
		$export_id = (int) $_POST['export_id'];
		if ( ! $export_id ) {
			$this->show_error( __( 'Nothing selected', 'colorrun' ) );
		}
		$participants = \modules\distance\Functions::get_participants( $export_id );
		$excel = new SimpleExcel( 'csv' );                    // instantiate new object (will automatically construct the parser & writer type as XML)
		$excel->writer->addRow( array_keys( $participants[0] ) );
		foreach ( $participants as $participant ) {
			$excel->writer->addRow( array_values( $participant ) );
		}
		$excel->writer->saveFile( sanitize_file_name( 'export_' . get_the_title( $export_id ) . '_' . date_i18n( 'Y-m-d-His' ) ) );
		exit();
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

	private function import() {

	}

	protected function _ajax_action() {
		// TODO: Implement _ajax_action() method.
	}

	private function show_success( string $message ): void {
		$this->show_message( 'success', $message );
	}
}