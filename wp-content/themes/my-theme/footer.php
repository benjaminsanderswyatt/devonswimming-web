<footer>
  <div class="footer-waves" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/footer-waves.svg');"></div>

  <div class="footer-wrapper">
    <div class="footer-content">
      
      <!-- Footer Widget Area -->
      <?php if ( is_active_sidebar( 'footer-widget' ) ) : ?>
        <div class="footer-widget-area">
          <?php dynamic_sidebar( 'footer-widget' ); ?>
        </div>
      <?php endif; ?>



      <div class="footer-details">

        <p><?php echo date('Y'); ?> <?php echo get_theme_mod('footer_copyright', 'Devon Swimming A.S.A.'); ?></p>

        <div class="developer-credit">
          <p>Site developed by 
            <a href="https://www.bsanderswyatt.com" target="_blank" rel="noopener">
              <img src="https://www.bsanderswyatt.com/images/BSW.svg" alt="BSW" class="bsw-logo">
            </a>
          </p>
        </div>

      </div>


    </div>
  </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>