<?php
namespace Habari;
if ( !defined( 'HABARI_PATH' ) ) { die('No direct access'); }
?>
<?php if ( count($posts) != 0 ) : ?>
<ul>
<?php	/** @var Post $post */
	foreach ( $posts as $post ) :
		$post_permissions = $post->get_access();
?>
<li class="<?php echo $post->typename . ' ' . $post->statusname; ?> manage_item" id="post_<?php echo $post->id; ?>">
	<div class="head">
		<?php if ( ACL::access_check( $post_permissions, 'delete' ) ): ?>
		<span class="checkbox">
			<input class="post_item" type="checkbox" name="checkbox_ids[<?php echo $post->id; ?>]" value="<?php echo $post->id; ?>">
		</span>
		<?php endif; ?>
		
		<span class="title">
			<?php if ( ACL::access_check( $post_permissions, 'edit' ) ): ?>
			<a href="<?php echo URL::out('display_publish', $post, false); ?>" class="title" title="<?php _e('Edit \'%s\'', array( Utils::htmlspecialchars( $post->title ) ) ) ?>"><?php echo ($post->title == '') ? '&nbsp;' : Utils::htmlspecialchars( $post->title ); ?></a>
			<?php else:
			echo ($post->title == '') ? '&nbsp;' : Utils::htmlspecialchars( $post->title );
			endif; ?>
		</span>
		<div class="meta">
			<span class="state"><a href="<?php URL::out('admin', array('page' => 'posts', 'type' => $post->content_type, 'status' => $post->status ) ); ?>" title="<?php _e('Search for other %s items', array( MultiByte::ucfirst( Plugins::filter( "post_status_display", $post->statusname ) ) ) ); ?>"><?php echo MultiByte::ucfirst( Plugins::filter( "post_status_display", $post->statusname ) ); ?></a></span>
			<span class="author"><?php _e('by'); ?> <a href="<?php URL::out('admin', array('page' => 'posts', 'user_id' => $post->user_id, 'type' => $post->content_type, 'status' => 'any') ); ?>" title="<?php _e('Search for other items by %s', array( $post->author->displayname ) ) ?>"><?php echo $post->author->displayname; ?></a></span>
			<span class="date"><?php _e('on'); ?> <a href="<?php URL::out('admin', array('page' => 'posts', 'type' => $post->content_type, 'year_month' => $post->pubdate->get('Y-m') ) ); ?>" title="<?php _e('Search for other items from %s', array( $post->pubdate->get( 'M, Y' ) ) ); ?>"><?php $post->pubdate->out( DateTime::get_default_date_format() ); ?></a></span>
			<span class="time"><?php _e('at'); ?> <?php $post->pubdate->out( DateTime::get_default_time_format()); ?></span>
			<div class="actions">
				<?php
				$post_actions = FormControlDropbutton::create('post' . $post->id . '_postactions');
				$post_actions->append(
					FormControlSubmit::create('edit')->set_caption(_t('Edit'))
						->set_url(URL::get( 'display_publish', $post, false ))
						->set_property('title', _t( 'Edit \'%s\'', array( $post->title ) ) )
						->set_enable(function($control) use($post) {
							return ACL::access_check( $post->get_access(), 'edit' );
						})
				);
				$post_actions->append(
					FormControlSubmit::create('view')->set_caption(_t('View'))
						->set_url($post->permalink . '?preview=1')
						->set_property('title', _t( 'View \'%s\'', array( $post->title ) ) )
				);
				$post_actions->append(
					FormControlSubmit::create('delete')->set_caption(_t('Delete'))
						->set_url('javascript:itemManage.remove('. $post->id . ', \'post\');')
						->set_property('title', _t( 'Delete \'%s\'', array( $post->title ) ) )
						->set_enable(function($control) use($post) {
							return ACL::access_check( $post->get_access(), 'delete' );
						})
				);
				Plugins::act('post_actions', $post_actions, $post);
				echo $post_actions->pre_out();
				echo $post_actions->get($theme);
				?>
			</div>
		</div>
	</div>
	<div class="content">
		<span class="excerpt" ><?php echo MultiByte::substr( strip_tags( $post->content ), 0, 250); ?>&hellip;</span>
	</div>
</li>

<?php endforeach; ?>
</ul>
<?php else : ?>
<div class="message none">
	<p><?php _e('No posts could be found to match the query criteria.'); ?></p>
</div>
<?php endif; ?>
