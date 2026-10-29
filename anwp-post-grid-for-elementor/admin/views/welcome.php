<?php
/**
 * Welcome page for AnWP_Post_Grid
 *
 * Included by AnWP_Post_Grid::render_welcome_page() with $data from get_welcome_data().
 *
 * @link       https://anwp.pro
 * @since      0.7.0
 *
 * @package    AnWP_Post_Grid
 * @subpackage AnWP_Post_Grid/admin/views
 *
 * @var array $data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_premium = ! empty( $data['premium'] );
$registry   = anwp_post_grid()->elements->get_widgets_registry();
$features   = anwp_post_grid()->elements->get_premium_features();
$pro_rows   = wp_list_filter( $registry, [ 'pro' => true ] );
$free_rows  = wp_list_filter( $registry, [ 'pro' => false ] );
$new_pro    = wp_list_filter( $pro_rows, [ 'new' => true ] );

$news_core = AnWP_Post_Grid::parse_changelog( $data['readme'] ?? '', 3 );
$news_pro  = $is_premium ? AnWP_Post_Grid::parse_changelog( $data['premium_readme'] ?? '', 2 ) : [];

$new_tab     = '<span class="screen-reader-text">' . esc_html__( '(opens in a new tab)', 'anwp-post-grid' ) . '</span>';
$known_types = [ 'added', 'fixed', 'changed', 'improved', 'note', 'important' ];

$render_card = function ( $row ) use ( $is_premium ) {
	?>
	<div class="pg-welcome__widget">
		<div class="pg-welcome__thumb">
			<img src="<?php echo esc_url( AnWP_Post_Grid::url( 'admin/img/welcome/' . $row['image'] ) ); ?>" width="640" height="400" loading="lazy" alt="">
			<?php if ( $row['pro'] ) : ?>
				<span class="pg-welcome__pill pg-welcome__pill--pro">
					<?php if ( ! $is_premium ) : ?>
						<span class="dashicons dashicons-lock" aria-hidden="true"></span>
					<?php endif; ?>
					<?php echo esc_html__( 'PRO', 'anwp-post-grid' ); ?>
				</span>
			<?php endif; ?>
		</div>
		<div class="pg-welcome__widget-body">
			<h4 class="pg-welcome__widget-name">
				<?php echo esc_html( $row['title'] ); ?>
				<?php if ( $row['new'] ) : ?>
					<span class="pg-welcome__pill pg-welcome__pill--new"><?php echo esc_html__( 'New', 'anwp-post-grid' ); ?></span>
				<?php endif; ?>
			</h4>
			<p class="pg-welcome__widget-desc"><?php echo esc_html( $row['desc'] ); ?></p>
			<a class="pg-welcome__demo" href="<?php echo esc_url( $row['demo_url'] ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html__( 'Live demo', 'anwp-post-grid' ); ?>
				<span class="dashicons dashicons-external" aria-hidden="true"></span>
				<span class="screen-reader-text">
					<?php
					/* translators: %s: widget name */
					echo esc_html( sprintf( __( '%s (opens in a new tab)', 'anwp-post-grid' ), $row['title'] ) );
					?>
				</span>
			</a>
		</div>
	</div>
	<?php
};

$render_changes = function ( $changes ) use ( $known_types ) {
	?>
	<ul class="pg-welcome__changes">
		<?php foreach ( $changes as $change ) : ?>
			<li>
				<span class="pg-welcome__type<?php echo in_array( $change['type'], $known_types, true ) ? ' pg-welcome__type--' . esc_attr( $change['type'] ) : ''; ?>"><?php echo esc_html( $change['type'] ); ?></span>
				<span><?php echo esc_html( $change['text'] ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
};

$render_releases = function ( $releases ) use ( $render_changes ) {
	$shown = 6;

	foreach ( $releases as $index => $release ) :
		?>
		<details class="pg-welcome__release" <?php echo 0 === $index ? 'open' : ''; ?>>
			<summary><?php echo esc_html( $release['version'] ); ?> <span class="pg-welcome__date"><?php echo esc_html( $release['date'] ); ?></span></summary>
			<?php $render_changes( array_slice( $release['changes'], 0, $shown ) ); ?>
			<?php if ( count( $release['changes'] ) > $shown ) : ?>
				<details class="pg-welcome__more">
					<summary>
						<?php
						$more = count( $release['changes'] ) - $shown;
						/* translators: %d: number of hidden changelog lines */
						echo esc_html( sprintf( _n( 'Show %d more change', 'Show %d more changes', $more, 'anwp-post-grid' ), $more ) );
						?>
					</summary>
					<?php $render_changes( array_slice( $release['changes'], $shown ) ); ?>
				</details>
			<?php endif; ?>
		</details>
		<?php
	endforeach;
};
?>
<div class="wrap pg-welcome">
	<hr class="wp-header-end">

	<div class="pg-welcome__card">
		<div class="pg-welcome__header">
			<div class="pg-welcome__logo"><span class="dashicons dashicons-screenoptions" aria-hidden="true"></span></div>
			<div>
				<div class="pg-welcome__title">
					<h1><?php echo esc_html__( 'Welcome to AnWP Post Grid', 'anwp-post-grid' ); ?></h1>
					<span class="pg-welcome__version">v<?php echo esc_html( AnWP_Post_Grid::VERSION ); ?></span>
					<?php if ( $is_premium && ! empty( $data['premium_version'] ) ) : ?>
						<span class="pg-welcome__version">
							<?php
							/* translators: %s: Premium plugin version */
							echo esc_html( sprintf( __( 'Premium v%s', 'anwp-post-grid' ), $data['premium_version'] ) );
							?>
						</span>
					<?php endif; ?>
				</div>
				<p class="pg-welcome__tagline">
					<?php
					if ( $is_premium ) {
						/* translators: %d: number of widgets */
						echo esc_html( sprintf( __( 'All %d widgets are ready in Elementor. Thanks for supporting the plugin!', 'anwp-post-grid' ), count( $registry ) ) );
					} else {
						/* translators: 1: number of free widgets, 2: number of Premium widgets */
						echo esc_html( sprintf( __( 'Post grids and sliders for Elementor - %1$d free widgets, and %2$d more with Premium.', 'anwp-post-grid' ), count( $free_rows ), count( $pro_rows ) ) );
					}
					?>
				</p>
			</div>
		</div>
		<div class="pg-welcome__links">
			<a class="button" href="https://anwppro.userecho.com/communities/50-anwp-post-grid-for-elementor" target="_blank" rel="noopener">
				<span class="dashicons dashicons-book" aria-hidden="true"></span><?php echo esc_html__( 'Docs & Forum', 'anwp-post-grid' ); ?><?php echo $new_tab; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<a class="button" href="https://anwppro.userecho.com/knowledge-bases/52-anwp-post-grid-for-elementor-changelog" target="_blank" rel="noopener">
				<span class="dashicons dashicons-list-view" aria-hidden="true"></span><?php echo esc_html__( 'Changelog', 'anwp-post-grid' ); ?><?php echo $new_tab; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<a class="button" href="<?php echo esc_url( $is_premium ? AnWP_Post_Grid_Elements::PREMIUM_DEMO_URL : AnWP_Post_Grid_Elements::DEMO_URL ); ?>" target="_blank" rel="noopener">
				<span class="dashicons dashicons-visibility" aria-hidden="true"></span><?php echo esc_html__( 'Live Demo', 'anwp-post-grid' ); ?><?php echo $new_tab; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=anwp_pg_settings' ) ); ?>">
				<span class="dashicons dashicons-admin-generic" aria-hidden="true"></span><?php echo esc_html__( 'Settings', 'anwp-post-grid' ); ?>
			</a>
			<?php if ( $is_premium && ! empty( $data['support_url'] ) ) : ?>
				<a class="button" href="<?php echo esc_url( $data['support_url'] ); ?>">
					<span class="dashicons dashicons-sos" aria-hidden="true"></span><?php echo esc_html__( 'Premium Support', 'anwp-post-grid' ); ?>
				</a>
			<?php endif; ?>
			<?php if ( ! $is_premium ) : ?>
				<a class="button pg-welcome__btn-pro pg-welcome__links-end" href="<?php echo esc_url( $data['premium_url'] ); ?>" target="_blank" rel="noopener">
					<span class="dashicons dashicons-star-filled" aria-hidden="true"></span><?php echo esc_html__( 'Get Premium', 'anwp-post-grid' ); ?><?php echo $new_tab; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div class="pg-welcome__card">
		<div class="pg-welcome__section-head">
			<h2><?php echo esc_html__( 'Quick start', 'anwp-post-grid' ); ?></h2>
			<p><?php echo esc_html__( 'Your first grid in under a minute.', 'anwp-post-grid' ); ?></p>
		</div>
		<div class="pg-welcome__start">
			<div class="pg-welcome__steps">
				<div class="pg-welcome__step">
					<span class="pg-welcome__step-num" aria-hidden="true">1</span>
					<div>
						<h3><?php echo esc_html__( 'Open a page with Elementor', 'anwp-post-grid' ); ?></h3>
						<p><?php echo esc_html__( 'Any page or post - click "Edit with Elementor".', 'anwp-post-grid' ); ?></p>
					</div>
				</div>
				<div class="pg-welcome__step">
					<span class="pg-welcome__step-num" aria-hidden="true">2</span>
					<div>
						<h3>
							<?php
							/* translators: %s: the search keyword "anwp" */
							printf( esc_html__( 'Search for %s', 'anwp-post-grid' ), '<kbd>anwp</kbd>' );
							?>
						</h3>
						<p><?php echo esc_html__( 'Type it in the widget search box. Every Post Grid widget shows up.', 'anwp-post-grid' ); ?></p>
					</div>
				</div>
				<div class="pg-welcome__step">
					<span class="pg-welcome__step-num" aria-hidden="true">3</span>
					<div>
						<h3><?php echo esc_html__( 'Drag a widget onto the page', 'anwp-post-grid' ); ?></h3>
						<p><?php echo esc_html__( 'Pick which posts to show, then style the cards. Columns, slides and card height follow your Elementor breakpoints.', 'anwp-post-grid' ); ?></p>
					</div>
				</div>
			</div>
			<div class="pg-welcome__panel-shot">
				<img src="<?php echo esc_url( AnWP_Post_Grid::url( 'admin/img/welcome/panel.webp' ) ); ?>" width="320" height="340" loading="lazy" alt="<?php echo esc_attr__( 'The Elementor widget panel after searching for anwp', 'anwp-post-grid' ); ?>">
			</div>
		</div>
	</div>

	<div class="pg-welcome__card">
		<div class="pg-welcome__section-head">
			<h2><?php echo esc_html__( 'Widgets', 'anwp-post-grid' ); ?></h2>
			<p>
				<?php
				if ( $is_premium ) {
					/* translators: %d: number of widgets */
					echo esc_html( sprintf( __( '%d widgets, all unlocked.', 'anwp-post-grid' ), count( $registry ) ) );
				} else {
					/* translators: 1: number of Premium widgets, 2: number of free widgets */
					echo esc_html( sprintf( __( '%1$d Premium, %2$d free.', 'anwp-post-grid' ), count( $pro_rows ), count( $free_rows ) ) );
				}
				?>
			</p>
		</div>

		<h3 class="pg-welcome__group-title"><?php echo esc_html__( 'Premium widgets', 'anwp-post-grid' ); ?></h3>
		<div class="pg-welcome__grid">
			<?php array_map( $render_card, $pro_rows ); ?>
		</div>

		<?php if ( ! $is_premium ) : ?>
			<div class="pg-welcome__cta">
				<p>
					<strong>
						<?php
						/* translators: %d: number of Premium widgets */
						echo esc_html( sprintf( __( 'Get all %d Premium widgets', 'anwp-post-grid' ), count( $pro_rows ) ) );
						?>
					</strong>
					<?php echo esc_html__( 'Hex Grid, Parallax Grid, News Ticker, Hero Slider and more - plus the header category filter, advanced pagination and custom taxonomies.', 'anwp-post-grid' ); ?>
				</p>
				<a class="button button-hero pg-welcome__btn-pro" href="<?php echo esc_url( $data['premium_url'] ); ?>" target="_blank" rel="noopener">
					<?php echo esc_html__( 'Get Premium', 'anwp-post-grid' ); ?><?php echo $new_tab; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
		<?php endif; ?>

		<h3 class="pg-welcome__group-title"><?php echo esc_html__( 'Free widgets', 'anwp-post-grid' ); ?></h3>
		<div class="pg-welcome__grid">
			<?php array_map( $render_card, $free_rows ); ?>
		</div>
	</div>

	<div class="pg-welcome__card">
		<div class="pg-welcome__section-head">
			<h2><?php echo esc_html__( 'Premium features', 'anwp-post-grid' ); ?></h2>
			<?php if ( ! $is_premium ) : ?>
				<span class="pg-welcome__pill pg-welcome__pill--pro"><span class="dashicons dashicons-lock" aria-hidden="true"></span><?php echo esc_html__( 'PRO', 'anwp-post-grid' ); ?></span>
			<?php endif; ?>
			<p><?php echo esc_html__( 'Beyond the widgets.', 'anwp-post-grid' ); ?></p>
		</div>
		<div class="pg-welcome__features">
			<?php foreach ( $features as $feature ) : ?>
				<div class="pg-welcome__feature">
					<span class="pg-welcome__feature-icon"><span class="dashicons dashicons-<?php echo esc_attr( $feature['icon'] ); ?>" aria-hidden="true"></span></span>
					<div>
						<h3><?php echo esc_html( $feature['title'] ); ?></h3>
						<p><?php echo esc_html( $feature['desc'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="pg-welcome__card" id="pg-welcome-news">
		<div class="pg-welcome__section-head">
			<h2><?php echo esc_html__( 'What\'s new', 'anwp-post-grid' ); ?></h2>
			<p><?php echo esc_html__( 'From the plugin readme.', 'anwp-post-grid' ); ?></p>
		</div>
		<div class="pg-welcome__news">
			<div>
				<h3><?php echo esc_html__( 'AnWP Post Grid', 'anwp-post-grid' ); ?></h3>
				<?php $render_releases( $news_core ); ?>
			</div>
			<div>
				<h3><?php echo esc_html__( 'Premium', 'anwp-post-grid' ); ?> <span class="pg-welcome__pill pg-welcome__pill--pro"><?php echo esc_html__( 'PRO', 'anwp-post-grid' ); ?></span></h3>
				<?php if ( $is_premium ) : ?>
					<?php $render_releases( $news_pro ); ?>
				<?php else : ?>
					<div class="pg-welcome__teaser">
						<?php foreach ( $new_pro as $row ) : ?>
							<p>
								<strong>
									<?php
									/* translators: %s: widget name */
									echo esc_html( sprintf( __( 'New in Premium: %s', 'anwp-post-grid' ), $row['title'] ) );
									?>
								</strong>
								- <?php echo esc_html( $row['desc'] ); ?>
							</p>
						<?php endforeach; ?>
						<a class="button pg-welcome__btn-pro" href="<?php echo esc_url( $data['premium_url'] ); ?>" target="_blank" rel="noopener">
							<?php echo esc_html__( 'Get Premium', 'anwp-post-grid' ); ?><?php echo $new_tab; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
