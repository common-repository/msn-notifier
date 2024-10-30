<?
/*
Plugin Name: MSN Notifier
Description: A plugin that send a notification to your MSN space after you publish your post.
Version: 2.2
Plugin URI: http://tension.usc.edu/blog/wp-plugins/#MSNNotifier
Author: tension
Author URI: http://tension.usc.edu/blog
*/
function msnnotifier_on_publish_post($post_ID)  {
    $msnnotifier_enable = $_POST['msnnotifier_enable'];
    if (!$msnnotifier_enable) return $post_ID;

    $post=get_post($post_ID);
    if ($post->post_type!="post") return $post_ID;
    setup_postdata($post);
    global $_msnn_mailer;
    if ( !is_object( $_msnn_mailer ) 
	    || !is_a( $_msnn_mailer, 'PHPMailer' ) ) {
       require_once ABSPATH . WPINC . '/class-phpmailer.php';
       $_msnn_mailer = new PHPMailer();
    }
    $_msnn_mailer->Mailer = get_option('msnnotifier_type');
    if ( $_msnn_mailer->Mailer=='smtp' ) {
      $_msnn_mailer->IsSMTP(); 
      $_msnn_mailer->SMTPAuth = true;
      $_msnn_mailer->Host = get_option("msnnotifier_smtp_server");
      $_msnn_mailer->Port = get_option("msnnotifier_smtp_port");
      $_msnn_mailer->Username = get_option('msnnotifier_smtp_username');
      $_msnn_mailer->Password = get_option('msnnotifier_smtp_password');
    }
    $_msnn_mailer->From = 'msnnotifier';
    $_msnn_mailer->Sender = 'msnnotifier@'.get_option('msnnotifier_domainname');
    $_msnn_mailer->FromName = 'msnnotifier';
    $_msnn_mailer->AddAddress(get_option('msnnotifier_email'),'msnspace');
    $_msnn_mailer->IsHTML(true); // send as HTML
    $_msnn_mailer->Subject = $post->post_title;
    $homelink = '<a href="'.get_option('home').'">'.get_option('blogname').'</a>';
    $msnnotifierlink = '<a href="http://wordpress.org/extend/plugins/msn-notifier"> MSN Notifier </a>';
    $postlink = '<p><a href="'.$post->guid.'">Read full story</a></p>';
    $content = get_the_content('');
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    //$content = get_the_content_with_formatting('... read full story');
    $_msnn_mailer->Body = $content.$postlink.'<p>Sent from '.$homelink.' <em>via</em> '.$msnnotifierlink.'</p>';
    if ( $_msnn_mailer->Send() ) return $post_ID;
    else wp_die("MSNNotifier failed. Have you setup the configuration?");
}

function msnnotifier_add_page() {
    add_submenu_page('plugins.php', __('MSN Notifier Options'), __('MSN Notifier'), 'manage_options', 'msnnotifier_option', 'msnnotifier_option_page');

}

function msnnotifier_option_page() {
  ?><div class="wrap">
    <h2> <?php _e('MSN Notifier Options'); ?> </h2><?
    if ( isset($_POST['submit']) ) {
        update_option('msnnotifier_email',$_POST['email']);
	update_option('msnnotifier_domainname',$_POST['domainname']);
	update_option('msnnotifier_type',$_POST['type']);
	update_option('msnnotifier_default_enable',$_POST['default_enable']);
	if($_POST['type']=='smtp') {
          update_option('msnnotifier_smtp_username',$_POST['smtp_username']);
          update_option('msnnotifier_smtp_password',$_POST['smtp_password']);
	  update_option('msnnotifier_smtp_server',$_POST['smtp_server']);
	  update_option('msnnotifier_smtp_port',$_POST['smtp_port']);
	}	
	echo '<div id="message" class="updated fade"><p> Options updated. </p></div>';
    }?>
	  <form action="" method="post">
    	  <div> 
	    <p> <strong>MSNSpace publishing email</strong>
	    : (<i>eg.</i> wp@spaces.live.com)</p>
	    <input type="text" name="email" style="width:400px"
		  value="<? _e(get_option('msnnotifier_email')); ?>" />
	  </div>
	  <div> 
	    <p> <strong>Your local domain name</strong>
	    : (<i>eg.</i> wordpress.com).</p>
	    <input type="text" name="domainname" style="width:400px"
		  value="<? _e(get_option('msnnotifier_domainname')); ?>" />
	    <p> Make sure msnnotifier@<?_e(get_option('msnnotifier_domainname')); ?> is in your MSNSpace email publishing FROM-addresses.</p>
	  </div>
	  <div>
	    <input type='checkbox' name='default_enable'
	      <? if(get_option('msnnotifier_default_enable')) _e('checked'); ?>
	    > Enable MSN-Notifier for every new post publishing by Default
	    </input>
	  </div>
	  <div>
	    <p> <strong>Use PHPMailer's 
	      <select name="type" id="sel_type"
		onchange="smtp_option(this.selectedIndex==2);">
		<?foreach(array('mail','sendmail','smtp') as $msnntp) {
		    echo '<option value="'.$msnntp.'" ';
		    if(get_option('msnnotifier_type')==$msnntp)
		      echo 'selected';
		    echo '>'.$msnntp.'</option>';
		}?>
	      </select>
	      method</strong></p>
	  </div>
	  <script type="text/javascript">
	    function smtp_option(enable) {
	      var opdiv = document.getElementById("div_smtp_option");
	      if (enable) { 
	        opdiv.style.visibility = "visible";
		opdiv.style.height="auto";
	      }
	      else {
		opdiv.style.visibility = "hidden";
		opdiv.style.height = "0px";
	      }
	    }
	  </script>
	  <div id="div_smtp_option">
	    <p> <strong>SMTP Server</strong>:</p>
	    <input type="text" name="smtp_server"
		  value="<? _e(get_option('msnnotifier_smtp_server'));?>" />
	    port
	    <input type="text" name="smtp_port"
		  value="<? _e(get_option('msnnotifier_smtp_port')); ?>" />
	    <p> <strong>SMTP Username</strong>:</p>
	    <input type="text" name="smtp_username"
		  value="<? _e(get_option('msnnotifier_smtp_username')); ?>" />
	    <p> <strong>SMTP Password</strong>:</p>
	    <input type="password" name="smtp_password"
		  value="<? _e(get_option('msnnotifier_smtp_password')); ?>" />
	  </div>
	  <script type="text/javascript">
	    document.getElementById("sel_type").onchange();
	  </script>
	  <div class="submit">
	    <input type="submit" name="submit" 
		    value="<? _e('Update options &raquo;'); ?>" />
	  </div>
	  </form>
    </div>
    <?
}

function msnnotifier_post_edit_form_options() {?>
  <div id="postmsnnotifier" class="postbox closed">
    <p>
    <form method="post">
    <input name="msnnotifier_enable" type='checkbox'
    <? if(get_option('msnnotifier_default_enable')){ _e(' checked '); }  ?>
    >Enable MSN-Notifier for this Post</input>
    </form>
    </p>
  </div><?
}

add_action ( 'private_to_published', 'msnnotifier_on_publish_post' );
add_action ( 'admin_menu', 'msnnotifier_add_page' );
add_action ( 'edit_form_advanced', 'msnnotifier_post_edit_form_options' );
add_option ( 'msnnotifier_type','mail');
add_option ( 'msnnotifier_domainname','your.local.domain.name');
?>
