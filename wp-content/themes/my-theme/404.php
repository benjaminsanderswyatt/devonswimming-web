<?php get_header(); ?>

<div class="site-main not-found">

    <div class="container error-404">
        
        <h2>404 - Page Not Found</h2>
        <p>Sorry, we couldn't find the page you were looking for.</p>

        <a class="link-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Return to homepage</a>
    
    </div>

</div>

<?php get_footer(); ?>
