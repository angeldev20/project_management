<?php if ( is_active_sidebar( 'ht_primary' ) ) { ?>

<!-- #sidebar -->
<aside id="sidebar" role="complementary" itemtype="http://schema.org/WPSideBar" itemscope="itemscope">   
	<?php dynamic_sidebar( 'ht_primary' );?>
</aside>
<!-- /#sidebar -->

<?php } ?>