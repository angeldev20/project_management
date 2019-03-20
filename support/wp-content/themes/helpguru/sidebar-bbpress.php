<?php if ( is_active_sidebar( 'ht_sidebar_bbpress' ) ) { ?>

<!-- #sidebar -->
<aside id="sidebar" role="complementary" itemtype="http://schema.org/WPSideBar" itemscope="itemscope">
	<?php dynamic_sidebar( 'ht_sidebar_bbpress' );?>
</aside>
<!-- /#sidebar -->

<?php } ?>