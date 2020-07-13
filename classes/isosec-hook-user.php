<?php
class ISOSEC_Hook_User
{

    public static function profileShow($user)
    {
        $ctx = ISOSEC_Context::getInstance();
        $html = $ctx->getHtmlObj();
        $dict = [];

        // Get templates
        $tmpl = $html->getTemplate('extra-user-meta.html', 'extra_user_meta');
        $select_templ = $html->getTemplatePart($tmpl, 'option',true, true);
        $dict['head'] = 'Veteranklubben medlemsdata';

        // Phone
        $dict['label_phone'] = 'Telefon';
        $dict['phone'] = get_user_meta($user->ID, 'isosec_phone', true);

        // Born
        $dict['label_born'] = 'Fødselsdato';
        $dict['born'] = get_user_meta($user->ID, 'isosec_born', true);

        // Ansatt nummr
        $dict['label_ansattnr'] = 'Ansatt nummer';
        $dict['ansattnr'] = get_user_meta($user->ID, 'isosec_ansattnr', true);
        if ( in_array("subscriber", $user->roles) ) {
            $dict['disabled'] = 'disabled';
        }

        // Selskap
        $dict['label_company'] = 'Selskap';
        $company = get_user_meta($user->ID, 'isosec_company', true);
        $selskapene = file_get_contents($ctx->pluginDir . '/selskapene.json');
        $selskapene = json_decode($selskapene);

        $option = "";
        foreach ( $selskapene as $key => $val ) {
            $odict['value'] = $val->value;
            $odict['name'] = $val->name;
            $odict['sel'] = '';
            if ( $val->value == $company ) {
                $odict['sel'] = 'selected';
            }
            $option .= $html->replace($select_templ, $odict);
        }

        $dict['option'] = $option;

        $dict['tilbake'] = get_site_url();
        $dict['d2'] = "{2}";
        $dict['d4'] = "{4}";

        $res = $html->replaceSymbols($dict, $tmpl);
        echo $res;
    }

    public static function profileSave($user_id)
    {
        $user = wp_get_current_user();

        if (isset($_POST['isosec_phone'])) {
            update_user_meta($user_id, 'isosec_phone', $_POST['isosec_phone']);
        }
        if (isset($_POST['isosec_born'])) {
            update_user_meta($user_id, 'isosec_born', $_POST['isosec_born']);
        }
        if ( ! in_array("subscriber", $user->roles) ) {
            if (isset($_POST['isosec_ansattnr']) ) {
                update_user_meta($user_id, 'isosec_ansattnr', $_POST['isosec_ansattnr']);
            }
        }

        if (isset($_POST['isosec_company']) && $_POST['isosec_company'] != "0") {
            update_user_meta($user_id, 'isosec_company', $_POST['isosec_company']);
        }

    }

}
