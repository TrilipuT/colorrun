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
            <form method="post" enctype="multipart/form-data">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="count"><?php _e( 'File', 'colorrun' ); ?></label></th>
                        <td><input type="file" name="import_file" accept=".csv,text/csv"/>
                            <p class="description">CSV file.</p></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="distance_id"><?php _e( 'Distance', 'colorrun' ) ?></label></th>
                        <td>
                            <select name="distance_id" id="distance_id" class="regular-text select2"
                                    required="required">
								<?php foreach ( \modules\distance\Functions::get_distances()->posts as $distance ) : ?>
                                    <option value="<?= $distance->ID ?>"><?= get_the_title( $distance ) ?></option>
								<?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label
                                    for="send_email"><?php _e( 'Send registration email?', 'colorrun' ) ?></label></th>
                        <td>
                            <input name="send_email" id="send_email" class="regular-text select2" type="checkbox"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name='type' value="import">
				<?php submit_button( __( 'Import' ) ) ?>
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
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		} );
	}

	private function import() {
		if ( ! isset( $_FILES['import_file'] ) || ! $_FILES['import_file'] ) {
			$this->show_error( __( 'Ooops, you should add file to import!', 'colorrun' ) );

			return false;
		}
		$file = $_FILES['import_file'];
		if ( strpos( $file['type'], 'csv' ) === false && strpos( $file['type'], 'vnd.ms-excel' ) === false ) {
			$this->show_error( __( 'Ooops, file should be in CSV format (.csv)', 'colorrun' ) );

			return false;
		}
		$is_send_email = isset( $_POST['send_email'] ) ? (bool) $_POST['send_email'] : false;
		$distance_id   = (int) $_POST['distance_id'];
		if ( ( $handle = fopen( $_FILES["import_file"]["tmp_name"], 'r' ) ) !== false ) {
			// necessary if a large csv file
			set_time_limit( 600 );

			$row      = 0;
			$imported = [];
			$list     = '';
			while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
				if ( $row == 0 ) {
					$mapping = $data;
					if ( ! in_array( 'email', $mapping ) ) {
						$this->show_error( __( 'No email field in csv', 'colorrun' ) );
					}
					$row ++;
					continue;
				}
				$participant = Participant::create();
				$participant->set_distance( $distance_id );
				$participant->set_info( array_combine( $mapping, $data ) );
				\modules\registration\Functions::finish_registration( $participant->get_id(), (object) [
					'payment_id'  => 'import',
					'create_date' => time() * 1000,
					'amount'      => 0,
				], $is_send_email );
				$participant = new Participant( $participant->get_id() );
				$imported[]  = $participant;
				$list        .= $participant->bib . '&#9;' . $participant->firstname . ' ' . $participant->lastname . "<br>";
				$row ++;
			}
			fclose( $handle );
		}

		$this->show_success( 'Import finished. ' . count( $imported ) . ' participants created.<br>' . $list );
	}

	private function show_success( string $message ): void {
		$this->show_message( 'success', $message );
	}

	protected function _ajax_action() {
		// TODO: Implement _ajax_action() method.
	}
}