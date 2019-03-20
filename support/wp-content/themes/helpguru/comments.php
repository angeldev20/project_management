<?php
/**
 * The template for displaying Comments.
 *
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<?php if ( have_comments() ) : ?>
	
<!-- #comments -->
<section id="comments" class="comments-area clearfix">

	<h3 id="comments-title">
		<?php printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments title', 'helpguru' ), number_format_i18n( get_comments_number() ) ); ?>
	</h3>

	<!-- .comment-list -->
	<ol class="comment-list">
		<?php wp_list_comments( array( 'callback' => 'ht_comment', 'style' => 'ol', ) );
		?>
	</ol>
	<!-- /.comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="navigation-comment" role="navigation">
			<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'helpguru' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'helpguru' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'helpguru' ) ); ?></div>
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

</section>
<!-- /#comments -->

<?php endif; // have_comments() ?>
    
    <?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
        
	<?php endif; ?>

	<?php 
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$comments_args = array(
		'cancel_reply_link' => __( 'Cancel Reply', 'helpguru' ),
		'fields' => array(
				'author' => '<p class="comment-form-author"><span class="ht-input-wrapper"><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" placeholder="' . __( 'Name', 'helpguru' ) . '" size="30"' . $aria_req . ' /></span></p>',
				'email' => '<p class="comment-form-email"><span class="ht-input-wrapper"><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" placeholder="' . __( 'Email', 'helpguru' ) . '" size="30"' . $aria_req . ' /></span></p>',
				'url' => '<p class="comment-form-url"><span class="ht-input-wrapper"><input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" placeholder="' . __( 'Website', 'helpguru' ) . '" size="30" /></span></p>',
		),
		'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" placeholder="'. __( 'Your Comment', 'helpguru' ) .'" cols="45" rows="5" aria-required="true"></textarea></p>',
		'name_submit'          => 'submit',
		'class_submit'         => 'submit',
		'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
		'submit_field' => '<p class="form-submit">%1$s %2$s</p>',
	);
	comment_form($comments_args); ?>