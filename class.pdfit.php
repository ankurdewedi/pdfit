<?php

/**
 * Loads library for pdf generation.
 */
require 'vendor/autoload.php'; 
use Dompdf\Dompdf;

class PdfIt {
	private static $initiated = false;

	/**
	 * Initiation function of the plugin that will add a shortcode and add action for pdf download.
	 * @static
	 */
	public static function init() {
		if ( ! self::$initiated ) {
			$initiated = true;
			add_shortcode('pdfit_show', array( 'PdfIt', 'form_show' ) );
			add_action('wp_ajax_post_pdfit',array( 'PdfIt', 'post_pdfit' ));
			add_action('wp_ajax_nopriv_post_pdfit',array( 'PdfIt', 'post_pdfit' ));
		}
	}

	/**
	 * POST endpoint to download the pdf form user submitted content.
	 * @static
	 */
	public static function post_pdfit(){
		try{
			if($_SERVER['REQUEST_METHOD']== 'POST'){
				if(isset($_POST['pdfit_content']) && (strlen($_POST['pdfit_content']) <= 5100)){
					// instantiate and use the dompdf class
					$dompdf = new Dompdf();
					$dompdf->loadHtml(trim($_POST['pdfit_content']));

					// (Optional) Setup the paper size and orientation
					$dompdf->setPaper('A4', 'potrait');

					// Render the HTML as PDF
					$dompdf->render();

					// Output the generated PDF to Browser
					//$dompdf->stream('content_'.time().'.pdf');
					$output = $dompdf->output();
					$file = 'content_'.time().'.pdf';
					try{
						$up = wp_get_upload_dir();
						file_put_contents($up['basedir'].'/'.$file, $output);
						echo json_encode(['success'=>true, 'url'=>$up['baseurl'].'/'.$file]);die;
					}catch(Exeption $e){
						echo json_encode(['success'=>false, 'msg'=>'Some error occurred. Please try again!']);die;
					}
				}
			}
			echo json_encode(['success'=>false, 'msg'=>'Content length should be max 5000.']);die;
		}catch(Exception $e){
			echo json_encode(['success'=>false, 'msg'=>'Some error occurred. Please try again!']);die;
		}
	}

	/**
	 * Shows form to the user to assist in generating pdf.
	 * @static
	 */
	public static function form_show(){
		
		//The view file contains HTML+CSS+jQuery code and assumes the jQuery script file is included in the head section.
		//if jQuery script is in footer section, you must use "add_action" hook to include it in the footer.
		
		$content = file_get_contents(PDFIT__PLUGIN_DIR.'views/form_show.html');
		$content = preg_replace('/{{ajaxurl}}/', '"'.admin_url('admin-ajax.php').'"', $content);
		return $content;
	}
	
	/**
	 * Plugin Activation Hook
	 * @static
	 */
	public static function plugin_activation() {
		//Do something when plugin is activated.
	}

	/**
	 * Plugin Deactivation Hook
	 * @static
	 */
	public static function plugin_deactivation( ) {
		//Do something when plugin is deactivated.
	}
	
}
