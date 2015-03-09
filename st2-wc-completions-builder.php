<?php
/**
 * Plugin Name: ST2 WooCommerce Auto Completions Builder
 * Plugin URI:
 * Description: Builds the auto completions files for the ST2 WooCommerce plugin
 * Version: 1.0.0
 * Author: Nicola Mustone
 * Author URI: http://nicolamustone.it
 * Requires at least: 4.0
 * Tested up to: 4.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * ST2_WC_Auto_Completion_Builder
 *
 * Stay away from this code, it is not intended to be used by everyone.
 * For who does not believe me and still wants to mess up his project,
 * do not change the code if you do not know exactly what are you doing or what the plugin is needed for.
 * Be sure that the txt files are correct or you may receive unexpected results and
 * check ALWAYS the results after running the plugin.
 *
 * Good luck
 */
class ST2_WC_Auto_Completion_Builder {

	public function __construct() {
		// Functions
		add_action( 'init', array( $this, 'build_functions' ) );
		add_filter( 'st2_wcac_builder_functions_the_match', array( $this, 'format_function_line' ), 10, 2 );

		// Actions
		add_action( 'init', array( $this, 'build_hooks' ) );
		add_filter( 'st2_wcac_builder_actions_the_match', array( $this, 'format_hook_line' ), 10, 2 );
		add_filter( 'st2_wcac_builder_filters_the_match', array( $this, 'format_hook_line' ), 10, 2 );
	}

	/**
	 * Builds the functions.sublime-completions file
	 *
	 * @return void
	 */
	public function build_functions() {
		$reg_exp = '/(function\s\w*\((.*)?\))/';
		$file    = file_get_contents( untrailingslashit( __DIR__ ) . '/functions.txt' );
		$results = preg_match_all( $reg_exp, $file, $matches );
		$fp      = fopen( untrailingslashit( __DIR__ ) . '/formatted_files/functions.sublime-completions', 'w' );

		if ( $fp ) :
			// Start printing the file contents
			ob_start(); ?>
{
	"scope": "source.php - variable.other.php",
	"completions":
	[
		"php",

		{ "trigger": "wc", "contents": "WC()" },
<?php
			$all_matches = array();
			foreach ( $matches as $match ) :
				if ( is_array( $match ) ) :
					// Ensures that the last line does not end with a comma
					$i = 1;
					$n_matches = count( $match );
					foreach ( $match as $the_match ) :
						// Skip constructors, private functions and everything that is not a function
						if ( strstr( $the_match, '__construct' ) || false === strstr( $the_match, 'function' ) || strstr( $the_match, 'function _' ) ) {
							$i++;
							continue;
						}

						$line = apply_filters( 'st2_wcac_builder_functions_the_match', $the_match, $i == $n_matches ? true : false );

						if ( ! in_array( $the_match, $all_matches ) ) :
?>
		<?php echo $line; ?>
<?php
								$all_matches[] = $the_match;
						endif;
						$i++;
					endforeach;
				endif;
			endforeach;
		?>
	]
}
<?php
			// File ready. Write it.
			fwrite( $fp, ob_get_clean() );
			fclose( $fp );
		endif;
	}

	/**
	 * Builds the hooks.sublime-completions file
	 *
	 * @return void
	 */
	public function build_hooks() {
		$actions = $this->get_actions();
		$filters = $this->get_filters();
		$fp      = fopen( untrailingslashit( __DIR__ ) . '/formatted_files/hooks.sublime-completions', 'w' );

		ob_start();
		?>
{
	"scope": "source.php - variable.other.php",
	"completions":
	[
		"php",

		// actions
<?php echo $actions; ?>

		//filters
<?php echo $filters; ?>
	]
}
<?php
		$contents = ob_get_clean();

		fwrite( $fp, $contents );
		fclose( $fp );
	}

	/**
	 * Get all the lines for the actions
	 *
	 * @return string
	 */
	public function get_actions() {
		$file    = file_get_contents( untrailingslashit( __DIR__ ) . '/actions.txt' );
		$reg_exp = "/(do_action\( '(wc|woocommerce)_(.*)?)/";
		$results = preg_match_all( $reg_exp, $file, $matches );

		ob_start();
		$all_matches = array();
		foreach ( $matches as $match ) :
			if ( is_array( $match ) ) :
				// Ensures that the last line does not end with a comma
				$i = 1;
				$n_matches = count( $match );
				foreach ( $match as $the_match ) :
					if ( strstr( $the_match, 'wc_' ) === false && strstr( $the_match, 'woocommerce_' ) === false ) {
						$i++;
						continue;
					}

					$line = apply_filters( 'st2_wcac_builder_actions_the_match', $the_match, false );

					if ( ! empty( $line ) && ! in_array( $the_match, $all_matches ) ) :
?>
		<?php echo $line; ?>
<?php
						$all_matches[] = $the_match;
					endif;

					$i++;
				endforeach;
			endif;
		endforeach;

		return ob_get_clean();
	}

	/**
	 * Get all the lines for the filters
	 *
	 * @return string
	 */
	public function get_filters() {
		$file    = file_get_contents( untrailingslashit( __DIR__ ) . '/filters.txt' );
		$reg_exp = "/(apply_filters\( '(wc|woocommerce)_(.*)?)/";
		$results = preg_match_all( $reg_exp, $file, $matches );

		ob_start();
		$all_matches = array();
		foreach ( $matches as $match ) :
			if ( is_array( $match ) ) :
				// Ensures that the last line does not end with a comma
				$i = 1;
				$n_matches = count( $match );
				foreach ( $match as $the_match ) :
					if ( strstr( $the_match, 'wc_' ) === false && strstr( $the_match, 'woocommerce_' ) === false ) {
						$i++;
						continue;
					}

					$line = apply_filters( 'st2_wcac_builder_filters_the_match', $the_match, $i == $n_matches ? true : false );

					if ( ! empty( $line ) && ! in_array( $the_match, $all_matches ) ) :
?>
		<?php echo $line; ?>
<?php
						$all_matches[] = $the_match;
					endif;

					$i++;
				endforeach;
			endif;
		endforeach;

		return ob_get_clean();
	}

	/**
	 * Formats each line for the functions auto completions
	 *
	 * @param string $line
	 * @param bool $last
	 * @return string
	 */
	public function format_function_line( $line, $last ) {
		$line = str_replace( 'function ', '', $line ); //Remove the word function

		list( $function, $params ) = explode( '(', $line );

		$params = trim( rtrim( $params, ')' ) );
		$params = explode( ',', $params );

		if ( ! empty( $params[0] ) ) {

			$i = 1;
			foreach ( $params as $param ) {
				if ( strpos( $param, '=' ) !== false ) {
					$param = substr( $param, 0, strpos( $param, '=' ) );
					$param = trim( $param );
				}

				// Format params for ST2 file
				$new_params[] = preg_replace( '/(\$\w+)/', '${' . $i . ':\\\\\\\\$1}', $param );
				$i++;
			}

			$params = implode( ', ', $new_params );
			$params = ' ' . $params . ' ';
		} else {
			$params = '';
		}

		$line = '{ "trigger": "' . $function . '", "content": "' . $function . '(' . $params . ')" }';

		if ( ! $last ) {
			$line .= ",\n";
		} else {
			$line .= "\n";
		}

		return $line;
	}

	/**
	 * Formats each line for the hooks auto completions
	 *
	 * @param string $line
	 * @param bool $last
	 * @return string
	 */
	public function format_hook_line( $line, $last ) {
		$line = preg_replace( "/(do_action|apply_filters)\s\(/", '', $line );
		$line = trim( $line );
		$line = ltrim( $line, "'" );

		$res = explode( "'", $line );

		if ( substr( $res[1], 0, 3 !== 'wc_' && substr( $res[1], 0, 12 ) !== 'woocommerce_' ) ) {
			return '';
		}

		$line = '{ "trigger": "' . $res[1] . '", "content": "' . $res[1] . '" }';

		if ( ! $last ) {
			$line .= ",\n";
		} else {
			$line .= "\n";
		}

		return $line;
	}
}

new ST2_WC_Auto_Completion_Builder();
