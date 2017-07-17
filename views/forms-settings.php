<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="wrap">
    <h2>Hellodialog Forms</h2>
     <h2><?php esc_html_e( 'Available contact fields', 'hellodialog' ) ?></h2>
            <?php
            $token = esc_attr( get_option('api_key'));
            if ( $token !== "" ) {
                KBApi::setToken($token);
                $kbFields       = new KBApi('fields');
                $fields         = $kbFields->get();
                $decodedResult  = json_decode(json_encode($fields), true);
                function show_fields ($mainArray) { ?>
                    <table id="apifields">
                        <tr>
                            <th><p><b><?php     esc_html_e( 'Fieldname', 'hellodialog' )         ?></b></p></th>
                            <th><p><b><?php     esc_html_e( 'Data type', 'hellodialog' )         ?></b></p></th>
                            <th><p><b><?php     esc_html_e( 'Req.', 'hellodialog' )              ?></b></p></th>
                            <th><p><b><?php     esc_html_e( 'User Viewable', 'hellodialog' )              ?></b></p></th>
                        </tr>
                        <?php

                        foreach ( $mainArray as $singlearray ) {
                            $required = esc_html__('No','hellodialog');
                            if ( $singlearray['subscription_field_mandatory'] == 1 ) {
                                $required = esc_html__('Yes','hellodialog');
                            }
                            if ( $singlearray['user_viewable'] == 1 ) {
                                echo "<tr>";
                                echo "<td><p>" . $singlearray['name'] . "</p></td>";
                                echo "<td><p>" . $singlearray['type'] . "</p></td>";
                                echo "<td><p>" . $required . "</p></td>";
                                echo "<td><p>" . $singlearray['user_viewable'] . "</p></td>";
                                echo "</tr>";
                            }
                        }
                    echo "</table>";
                }
                show_fields($decodedResult);
            } else {
                esc_html_e( 'Set API key to show available fields', 'hellodialog' );
            }
echo "</form></div>";