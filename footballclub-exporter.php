<?php
/**
 * @package Football Club Exporter
 * @version 1.0
 */
/*
Plugin Name: Football Club Exporter
Plugin URI: http://themeboy.com
Description: Export Football Club data to CSV files.
Author: ThemeBoy
Author URI: http://themeboy.com
Version: 1.0
*/

load_plugin_textdomain( 'footballclub', false, basename( dirname( __FILE__ ) ) . '/languages' );

/**
 * Main plugin class
 *
 * @since 0.1
 **/
class Football_Club_Exporter {

	/**
	 * Class contructor
	 *
	 * @since 0.1
	 **/
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
		add_action( 'init', array( $this, 'generate_csv' ) );
	}

	/**
	 * Add administration menus
	 *
	 * @since 0.1
	 **/
	public function add_admin_pages() {
		add_management_page( __( 'Export Football Club', 'footballclub' ), __( 'Export Football Club', 'footballclub' ), 'manage_options', 'footballclub', array( $this, 'export_page' ) );
	}

	/**
	 * Process content of CSV file
	 *
	 * @since 0.1
	 **/
	public function generate_csv() {
		if ( isset( $_POST['_wpnonce-pp-eu-export-footballclub-teams-page_export'] ) ) {
			check_admin_referer( 'fc-e-export-footballclub-teams-page_export', '_wpnonce-pp-eu-export-footballclub-teams-page_export' );

			$args = array(
				'posts_per_page'   => -1,
				'post_type' => 'tb_club',
			);

			$posts = get_posts( $args );

			if ( ! $posts ) {
				$referer = add_query_arg( 'error', 'empty', wp_get_referer() );
				wp_redirect( $referer );
				exit;
			}

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) )
				$sitename .= '.';
			$filename = $sitename . 'teams.' . date( 'Y-m-d-H-i-s' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
			$headers = array( 'Name', 'Leagues', 'Seasons' );
			echo implode( ',', $headers ) . "\n";

			foreach ( $posts as $post ) {
				$data = array();
				
				// Name
				$data[] = $post->post_title;

				// Leagues
				$leagues = array();
				$terms = get_the_terms( $post->ID, 'tb_comp' );
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$leagues[] = $term->name;
					}
				}
				$data[] = implode( '|', $leagues );

				// Seasons
				$seasons = array();
				$terms = get_the_terms( $post->ID, 'tb_season' );
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$seasons[] = $term->name;
					}
				}
				$data[] = implode( '|', $seasons );

				foreach ( $data as $key => $value ) {
					$data[ $key ] = str_replace( array( '"', '&#039;' ), "'", $value );
				}

				echo '"' . implode( '","', $data ) . '"' . "\n";
			}

			exit;
		} elseif ( isset( $_POST['_wpnonce-pp-eu-export-footballclub-players-page_export'] ) ) {
			check_admin_referer( 'fc-e-export-footballclub-players-page_export', '_wpnonce-pp-eu-export-footballclub-players-page_export' );

			$args = array(
				'posts_per_page'   => -1,
				'post_type' => 'tb_player',
			);

			$posts = get_posts( $args );

			if ( ! $posts ) {
				$referer = add_query_arg( 'error', 'empty', wp_get_referer() );
				wp_redirect( $referer );
				exit;
			}

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) )
				$sitename .= '.';
			$filename = $sitename . 'players.' . date( 'Y-m-d-H-i-s' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
			$headers = array( 'Number', 'Name', 'Positions', 'Teams', 'Leagues', 'Seasons', 'Nationality' );
			echo implode( ',', $headers ) . "\n";

			foreach ( $posts as $post ) {
				$data = array();

				// Number
				$number = get_post_meta( $post->ID, 'tb_number', true );
				if ( ! $number ) $number = null;
				$data[] = $number;
				
				// Name
				$data[] = $post->post_title;

				// Position
				$positions = array();
				$terms = get_the_terms( $post->ID, 'tb_position' );
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$positions[] = $term->name;
					}
				}
				$data[] = implode( '|', $positions );

				// Team
				$team = null;
				$team_id = get_post_meta( $post->ID, 'tb_club', true );
				$team = get_post( $team_id );
				if ( $team ) {
					$team = $team->post_title;
				}
				$data[] = $team;

				// Leagues
				$leagues = array();
				$terms = get_the_terms( $post->ID, 'tb_team' );
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$leagues[] = $term->name;
					}
				}
				$data[] = implode( '|', $leagues );

				// Seasons
				$seasons = array();
				$terms = get_the_terms( $post->ID, 'tb_season' );
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$seasons[] = $term->name;
					}
				}
				$data[] = implode( '|', $seasons );

				// Nationality
				$nationality = get_post_meta( $post->ID, 'tb_natl', true );
				if ( ! $nationality ) $nationality = null;
				$data[] = strtoupper($nationality);

				foreach ( $data as $key => $value ) {
					$data[ $key ] = str_replace( array( '"', '&#039;' ), "'", $value );
				}

				echo '"' . implode( '","', $data ) . '"' . "\n";
			}

			exit;
		} elseif ( isset( $_POST['_wpnonce-pp-eu-export-footballclub-staff-page_export'] ) ) {
			check_admin_referer( 'fc-e-export-footballclub-staff-page_export', '_wpnonce-pp-eu-export-footballclub-staff-page_export' );

			$args = array(
				'posts_per_page'   => -1,
				'post_type' => 'tb_staff',
			);

			$posts = get_posts( $args );

			if ( ! $posts ) {
				$referer = add_query_arg( 'error', 'empty', wp_get_referer() );
				wp_redirect( $referer );
				exit;
			}

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) )
				$sitename .= '.';
			$filename = $sitename . 'staff.' . date( 'Y-m-d-H-i-s' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
			$headers = array( 'Name', 'Teams', 'Leagues', 'Seasons', 'Nationality' );
			echo implode( ',', $headers ) . "\n";

			foreach ( $posts as $post ) {
				$data = array();
				
				// Name
				$data[] = $post->post_title;

				// Team
				$team = null;
				$team_id = get_post_meta( $post->ID, 'tb_club', true );
				$team = get_post( $team_id );
				if ( $team ) {
					$team = $team->post_title;
				}
				$data[] = $team;

				// Leagues
				$leagues = array();
				$terms = get_the_terms( $post->ID, 'tb_team' );
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$leagues[] = $term->name;
					}
				}
				$data[] = implode( '|', $leagues );

				// Seasons
				$seasons = array();
				$terms = get_the_terms( $post->ID, 'tb_season' );
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$seasons[] = $term->name;
					}
				}
				$data[] = implode( '|', $seasons );

				// Nationality
				$nationality = get_post_meta( $post->ID, 'tb_natl', true );
				if ( ! $nationality ) $nationality = null;
				$data[] = strtoupper($nationality);

				foreach ( $data as $key => $value ) {
					$data[ $key ] = str_replace( array( '"', '&#039;' ), "'", $value );
				}

				echo '"' . implode( '","', $data ) . '"' . "\n";
			}

			exit;
		} elseif ( isset( $_POST['_wpnonce-pp-eu-export-footballclub-events-page_export'] ) ) {
			check_admin_referer( 'fc-e-export-footballclub-events-page_export', '_wpnonce-pp-eu-export-footballclub-events-page_export' );

			$args = array(
				'posts_per_page'   => -1,
				'post_type' => 'tb_match',
				'post_status' => 'any',
			);

			$posts = get_posts( $args );

			if ( ! $posts ) {
				$referer = add_query_arg( 'error', 'empty', wp_get_referer() );
				wp_redirect( $referer );
				exit;
			}

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) )
				$sitename .= '.';
			$filename = $sitename . 'events.' . date( 'Y-m-d-H-i-s' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
			$headers = array( 'Date', 'Time', 'Venue', 'Teams', 'Results', 'Outcome', 'Players', 'Goals', 'Assists', 'Yellow Cards', 'Red Cards' );
			echo implode( ',', $headers ) . "\n";

			foreach ( $posts as $post ) {
				$data = array();
				$played = get_post_meta( $post->ID, 'tb_played', true );
				$home_goals = get_post_meta( $post->ID, 'tb_home_goals', true );
				$away_goals = get_post_meta( $post->ID, 'tb_away_goals', true );
				if ( $played ) {
					if ( ! $home_goals ) $home_goals = 0;
					if ( ! $away_goals ) $away_goals = 0;
					if ( $home_goals > $away_goals ) {
						$home_outcome = 'Win';
						$away_outcome = 'Loss';
					} elseif ( $home_goals < $away_goals ) {
						$home_outcome = 'Loss';
						$away_outcome = 'Win';
					} else {
						$home_outcome = 'Draw';
						$away_outcome = 'Draw';
					}
				} else {
					$home_goals = null;
					$away_goals = null;
					$home_outcome = null;
					$away_outcome = null;
				}
				$players = get_post_meta( $post->ID, 'tb_players', true );
				if ( $players ) {
					$players = unserialize( $players );
				} else {
					$players = array();
				}
				$home_stats = array();
				$away_stats = array();
				if ( array_key_exists( 'home', $players ) && is_array( $players['home'] ) ) {
					foreach( $players['home'] as $player_group ) {
						foreach ( $player_group as $id => $player ) {
							if ( ! $id ) continue;
							$p = array();
							$name = get_the_title( $id );
							$p[] = str_replace( array( '"', '&#039;' ), "'", $name );

							if ( array_key_exists( 'goals', $player ) ) {
								$p[] = $player['goals'];
							} else {
								$p[] = 0;
							}

							if ( array_key_exists( 'assists', $player ) ) {
								$p[] = $player['assists'];
							} else {
								$p[] = 0;
							}

							if ( array_key_exists( 'yellowcards', $player ) ) {
								$p[] = $player['yellowcards'];
							} else {
								$p[] = 0;
							}

							if ( array_key_exists( 'redcards', $player ) ) {
								$p[] = $player['redcards'];
							} else {
								$p[] = 0;
							}

							$home_stats[] = $p;
						}
					}
				}
				if ( array_key_exists( 'away', $players ) && is_array( $players['away'] ) ) {
					foreach( $players['away'] as $player_group ) {
						foreach ( $player_group as $id => $player ) {
							if ( ! $id ) continue;
							$p = array();
							$name = get_the_title( $id );
							$p[] = str_replace( array( '"', '&#039;' ), "'", $name );

							if ( array_key_exists( 'goals', $player ) ) {
								$p[] = $player['goals'];
							} else {
								$p[] = 0;
							}

							if ( array_key_exists( 'assists', $player ) ) {
								$p[] = $player['assists'];
							} else {
								$p[] = 0;
							}

							if ( array_key_exists( 'yellowcards', $player ) ) {
								$p[] = $player['yellowcards'];
							} else {
								$p[] = 0;
							}

							if ( array_key_exists( 'redcards', $player ) ) {
								$p[] = $player['redcards'];
							} else {
								$p[] = 0;
							}

							$away_stats[] = $p;
						}
					}
				}

				list( $date, $time ) = explode( ' ', $post->post_date );
				
				// Date
				$data[] = $date;

				// Time
				$data[] = $time;

				// Venue
				$venues = array();
				$terms = get_the_terms( $post->ID, 'tb_venue' );
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$venues[] = $term->name;
					}
				}
				$data[] = implode( '|', $venues );

				// Home Team
				$team = null;
				$team_id = get_post_meta( $post->ID, 'tb_home_club', true );
				$team = get_post( $team_id );
				if ( $team ) {
					$team = $team->post_title;
				}
				$data[] = $team;

				// Home Results
				$data[] = $home_goals;

				// Home Outcome
				$data[] = $home_outcome;

				foreach ( $data as $key => $value ) {
					$data[ $key ] = str_replace( array( '"', '&#039;' ), "'", $value );
				}

				echo '"' . implode( '","', $data ) . '"';

				if ( count( $home_stats ) ) {
					echo ',';
					$i = 0;
					foreach ( $home_stats as $stats ) {
						if ( $i > 0 ) {
							echo '"","","","","","",';
						}
						echo '"' . implode( '","', $stats ) . '"' . "\n";
						$i++;
					}
				} else {
					echo "\n";
				}

				// Away Team
				$data = array( "", "", "" );
				$team = null;
				$team_id = get_post_meta( $post->ID, 'tb_away_club', true );
				$team = get_post( $team_id );
				if ( $team ) {
					$team = $team->post_title;
				}
				$data[] = $team;

				// Away Results
				$data[] = $away_goals;

				// Away Outcome
				$data[] = $away_outcome;

				foreach ( $data as $key => $value ) {
					$data[ $key ] = str_replace( array( '"', '&#039;' ), "'", $value );
				}

				echo '"' . implode( '","', $data ) . '"';

				if ( count( $away_stats ) ) {
					echo ',';
					$i = 0;
					foreach ( $away_stats as $stats ) {
						if ( $i > 0 ) {
							echo '"","","","","","",';
						}
						echo '"' . implode( '","', $stats ) . '"' . "\n";
						$i++;
					}
				} else {
					echo "\n";
				}
			}

			exit;
		} elseif ( isset( $_POST['_wpnonce-pp-eu-export-footballclub-sponsors-page_export'] ) ) {
			check_admin_referer( 'fc-e-export-footballclub-sponsors-page_export', '_wpnonce-pp-eu-export-footballclub-sponsors-page_export' );

			$args = array(
				'posts_per_page'   => -1,
				'post_type' => 'tb_sponsor',
			);

			$posts = get_posts( $args );

			if ( ! $posts ) {
				$referer = add_query_arg( 'error', 'empty', wp_get_referer() );
				wp_redirect( $referer );
				exit;
			}

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) )
				$sitename .= '.';
			$filename = $sitename . 'sponsors.' . date( 'Y-m-d-H-i-s' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
			$headers = array( 'Name', 'URL' );
			echo implode( ',', $headers ) . "\n";

			foreach ( $posts as $post ) {
				$data = array();
				
				// Name
				$data[] = $post->post_title;

				// Number
				$url = get_post_meta( $post->ID, 'tb_link_url', true );
				if ( ! $url ) $url = null;
				$data[] = $url;

				foreach ( $data as $key => $value ) {
					$data[ $key ] = str_replace( array( '"', '&#039;' ), "'", $value );
				}

				echo '"' . implode( '","', $data ) . '"' . "\n";
			}

			exit;
		}
	}

	/**
	 * Content of the settings page
	 *
	 * @since 0.1
	 **/
	public function export_page() {
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'footballclub' ) );
?>

<div class="wrap">
	<h2><?php _e( 'Export Football Club data to CSV files', 'footballclub' ); ?></h2>
	<?php
	if ( isset( $_GET['error'] ) ) {
		echo '<div class="updated"><p><strong>' . __( 'No user found.', 'footballclub' ) . '</strong></p></div>';
	}
	?>
	<p class="submit">
		<form method="post" action="" enctype="multipart/form-data">
			<h3><?php _e( 'Clubs', 'footballclub' ); ?></h3>
			<p><?php _e( 'Clubs will be exported as "Teams" with the columns "Leagues" (Competitions) and "Seasons".', 'footballclub' ); ?></p>
			<?php wp_nonce_field( 'fc-e-export-footballclub-teams-page_export', '_wpnonce-pp-eu-export-footballclub-teams-page_export' ); ?>
			<input type="hidden" name="_wp_http_referer" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
			<input type="submit" class="button button-hero button-primary" value="<?php _e( 'Download Clubs as Teams', 'footballclub' ); ?>" /><br><br>
		</form>
		<form method="post" action="" enctype="multipart/form-data">
			<h3><?php _e( 'Players', 'footballclub' ); ?></h3>
			<p><?php _e( 'Players will be exported with the columns "Number", "Name", "Positions", "Teams" (Clubs), "Leagues" (Teams), "Seasons", and "Nationality" (Hometown Country).', 'footballclub' ); ?></p>
			<?php wp_nonce_field( 'fc-e-export-footballclub-players-page_export', '_wpnonce-pp-eu-export-footballclub-players-page_export' ); ?>
			<input type="hidden" name="_wp_http_referer" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
			<input type="submit" class="button button-hero button-primary" value="<?php _e( 'Download Players', 'footballclub' ); ?>" /><br><br>
		</form>
		<form method="post" action="" enctype="multipart/form-data">
			<h3><?php _e( 'Staff', 'footballclub' ); ?></h3>
			<p><?php _e( 'Staff will be exported with the columns "Name", "Teams" (Clubs), "Leagues" (Teams), "Seasons", and "Nationality".', 'footballclub' ); ?></p>
			<?php wp_nonce_field( 'fc-e-export-footballclub-staff-page_export', '_wpnonce-pp-eu-export-footballclub-staff-page_export' ); ?>
			<input type="hidden" name="_wp_http_referer" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
			<input type="submit" class="button button-hero button-primary" value="<?php _e( 'Download Staff', 'footballclub' ); ?>" /><br><br>
		</form>
		<form method="post" action="" enctype="multipart/form-data">
			<h3><?php _e( 'Matches', 'footballclub' ); ?></h3>
			<p><?php _e( 'Matches will be exported as "Events" with the columns "Date", "Time" (Kick-off), "Venue", "Teams" (Clubs), "Results", "Outcome", "Players", "Goals", "Assists", "Yellow Cards", and "Red Cards".', 'footballclub' ); ?></p>
			<?php wp_nonce_field( 'fc-e-export-footballclub-events-page_export', '_wpnonce-pp-eu-export-footballclub-events-page_export' ); ?>
			<input type="hidden" name="_wp_http_referer" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
			<input type="submit" class="button button-hero button-primary" value="<?php _e( 'Download Matches as Events', 'footballclub' ); ?>" /><br><br>
		</form>
		<form method="post" action="" enctype="multipart/form-data">
			<h3><?php _e( 'Sponsors', 'footballclub' ); ?></h3>
			<p><?php _e( 'Sponsors will be exported with the columns "Name" and "URL".', 'footballclub' ); ?></p>
			<?php wp_nonce_field( 'fc-e-export-footballclub-sponsors-page_export', '_wpnonce-pp-eu-export-footballclub-sponsors-page_export' ); ?>
			<input type="hidden" name="_wp_http_referer" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
			<input type="submit" class="button button-hero button-primary" value="<?php _e( 'Download Sponsors', 'footballclub' ); ?>" />
		</form>
	</p>
<?php
	}
}

new Football_Club_Exporter;
