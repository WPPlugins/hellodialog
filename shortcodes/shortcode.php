<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post;
if (!is_dir(WP_CONTENT_DIR."/hellodialog")) {
    // dir doesn't exist, make it
    mkdir(WP_CONTENT_DIR."/hellodialog");
}
$file = WP_CONTENT_DIR."/hellodialog/cachedfields.txt";
if ( file_exists ( $file ) ) {
    if (time()-filemtime( $file ) > 2 * 3600) {
                // file older than 2 hours
                $token      = esc_attr( get_option('api_key'));
                KBApi::setToken($token);
                $kbFields   = new KBApi('fields');
                $input      = $kbFields->get();
                file_put_contents($file, serialize($input));
    } else {
        // file younger than 2 hours
        $serinput    = file_get_contents($file);
		$input       = unserialize($serinput);
    }
} else {
    $token      = esc_attr( get_option('api_key'));
    KBApi::setToken($token);
    $kbFields   = new KBApi('fields');
    $input      = $kbFields->get();
    file_put_contents($file, serialize($input));
}
$decodedResult  = json_decode(json_encode($input), true);
$wporg_atts     = shortcode_atts([
    'id'    =>  '',
], $atts);
$formID         = esc_html__($wporg_atts['id'], 'wporg');
$custompostid   = $formID;
$custom_meta    = get_post_meta($custompostid, '_custom-meta-box', true);
$custom_meta2    = get_post_meta($custompostid, '_custom-meta-box2', true);


echo '<form action="" method="post" id="hellodialogForm">';
echo '<h3 class="hdtitle">'. $custom_meta2[0] .'</h3>';
echo '<p class="hdtitle">'. $custom_meta2[1] .'</p>';


    foreach ( $decodedResult as $field ) {

        $headappend = "<div class='hd-field field-". strtolower($field['type']) ."'><div class='hd-field-label'><label>" . $field['name'] . "</label></div><div class='hd-field-element'>";
        $tailappend = "</div></div>";

        if ( $field['user_viewable'] == 1 ) {
            if ( in_array( $field['name'], $custom_meta ) ) {
                if ( $field['type'] == "Text" ) {
                    if ( $field['name'] == "Email" ) {
                        // NAME EMAIL IS ALWAYS REQUIRED
                        echo "".$headappend."<input type='text' id='email' name='" . $field['name'] ."' required='required'>".$tailappend;
                    } else {
                        echo "".$headappend."<input type='text' name='" . $field['name'] ."' ". (($field['subscription_field_mandatory'] == '1') ? 'required=\'required\'' : '') .">".$tailappend;
                    }
                }
                if ($field['type'] == "Dropdown"){
                    echo "".$headappend ."<select name='". $field['name'] ."' ". (($field['subscription_field_mandatory'] == '1') ? 'required=\'required\'' : '') .">";
                    foreach ($field['options'] as $optionvalue ) {
                        $i      =   count($optionvalue) -1;
                            while ( $i >=  0 ) {
                                echo "<option value='" . $optionvalue[$i] . "'>".$optionvalue[$i]."</option>";
                                $i--;
                            }
                    }
                    echo "</select>".$tailappend;
                }
                if ( $field['type'] == "Multiselect" ) {
                    echo "" . $headappend . "";
                    echo "<select id='multi' name='". $field['name'] ."[]' multiple='multiple[]' ". (($field['subscription_field_mandatory'] == '1') ? 'required=\'required\'' : '') .">";
                    foreach ( $field['options'] as $optionvalue ) {
                        $i      =   count($optionvalue) -1;
                        while ($i >= 0){
                            echo "<option value='" . $optionvalue[$i] . "'>".$optionvalue[$i]."</option>";
                            $i--;
                        }
                    }
                    echo "</select>".$tailappend;

                }
                if ( $field['type'] == "Date" ) {
                    echo "" . $headappend . "";
                    $day =  "<input type='number' name='" . $field['name'] . "[day]' class='field-day' min='1' max='31' placeholder='1' value='1'  />";
                    $month =  "
                    <select name='" . $field['name'] . "[month]' class='field-month' >
                    
                    <option value='0' selected>January</option>
                    <option value='1'>February</option>
                    <option value='2'>March</option>
                    <option value='3'>April</option>
                    <option value='4'>May</option>
                    <option value='5'>June</option>
                    <option value='6'>July</option>
                    <option value='7'>August</option>
                    <option value='8'>September</option>
                    <option value='9'>October</option>
                    <option value='10'>November</option>
                    <option value='11'>December</option>
                    </select>
                    ";
                    $year =  "<input type='number' name='" . $field['name'] . "[year]' class='field-year' min='1850' max='" . date('Y')  . "' placeholder='1985' value='1985'  />";
                    echo $day . $month . $year . "" . $tailappend;

                }
                if ( $field['type'] == "Integer" ) {
                    echo "" . $headappend . "<input type='number' name='" . $field['name'] ."' ". (($field['subscription_field_mandatory'] == '1') ? 'required=\'required\'' : '') .">".$tailappend;
                }
                if ( $field['type'] == "Decimals" ) {
                    echo "" . $headappend . "<input type='number' step='any' name='" . $field['name'] ."' ". (($field['subscription_field_mandatory'] == '1') ? 'required=\'required\'' : '') .">".$tailappend;
                }
                if ( $field['type'] == "Textarea" ) {
                    echo "" . $headappend . "<input type='text' name='" . $field['name'] ."' ". (($field['subscription_field_mandatory'] == '1') ? 'required=\'required\'' : '') .">".$tailappend;
                }
            }
        }
    }

    $token      = esc_attr( get_option('api_key'));
    KBApi::setToken($token);
    $kbGroups   = new KBApi('groups');
    $inputgroup      = $kbGroups->get();
    $decodedGroups  = json_decode(json_encode($inputgroup), true);
    $size = count($decodedGroups);

    if ($size > 1) {
        echo "<div class='hd-field field-sendlist'><div class='hd-field-label'><label>".esc_attr_e('Newsletter lists','hellodialog')."</label></div><div class='hd-field-element'>";
        echo "<select id='groups' name='groups[]' multiple='multiple[]' >";
        foreach ( $decodedGroups as $groupdata ) {
            $i      =   count($groupdata['name']) -1;

            while ($i >= 0){

                if ( $groupdata['is_private'] == false ){
                    echo "<option value='" . $groupdata['id'] . "'>".$groupdata['visible_name']."</option>";
                }
                $i--;


            }
        }
        echo "</select></div></div>";
    }
    elseif ($size == 1) {
        echo "<select id='hiddengroups' name='groups[]' multiple='multiple[]' hidden='hidden' >";
        foreach ( $decodedGroups as $groupdata ) {
            $i      =   count($groupdata['name']) -1;

            while ($i >= 0){

                if ( $groupdata['is_private'] == false ){
                    echo "<option style='display: none;' selected value='" . $groupdata['id'] . "'>".$groupdata['visible_name']." </option>";
                }
                $i--;


            }
        }
        echo "</select>";
    }

?>
<input type="hidden" name="action" value="saveContact"/>
<input type="submit" id="submit" name="formSubmit" value="<?php esc_attr_e('Submit','hellodialog');?>" />
<?php wp_nonce_field( 'submit_form' ); ?>
</form>
<div id="feedback"></div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#multi').multiselect();
        $('#groups').multiselect();

    });
</script>
<script type="text/javascript">
    jQuery('#hellodialogForm').submit(ajaxSubmit);

	function validateEmail(email) {
	  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	  return re.test(email);
	}

    function ajaxSubmit(){

        var hellodialogForm = jQuery(this).serialize();
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		var email = jQuery("#email").val();

		if (validateEmail(email)) {

		    jQuery("#submit").prop('value', 'Processing');
		    jQuery.ajax({
		        type:"POST",
		        url: ajaxurl,
		        data: hellodialogForm,
		        success:function(data){
		            jQuery("#hellodialogForm").remove();
		            jQuery("#feedback").html(data);
		            jQuery("#submit").prop("value", "Submit");
		        }
		    });

		} else {
			jQuery("#feedback").text("<?php _e('Emailaddress is not valid','hellodialog');?>");
		}

        return false;
    }
</script>