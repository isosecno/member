<?php
/*
Plugin Name: ISOSEC Medlemsregister
Description: Diverse funksjoner for medlemsregister
Version: 2.0
Author: Pål Bergquist
Author URI: https://isosec.no
*/
if (!function_exists('wp_enqueue_scripts')) die('Not proper Wordpress initialization!');

if ( !class_exists( 'ISOSEC_Mem' ) ) {
    class ISOSEC_Mem
    {
        public $plugin_dir;
        private $ctx;

        public function init() {
            spl_autoload_register([$this, 'autoload_classes']);
            $this->ctx = new ISOSEC_Context();
            $this->ctx->pluginDir = dirname(__FILE__);
            $this->ctx->pluginUrl = plugins_url('', __FILE__);
            $this->ctx->htmlDir = dirname(__FILE__).'/html';
            $this->ctx->imageUrl = plugins_url('images', __FILE__);
            //$this->ctx->uploadDir =  wp_upload_dir()['basedir'] . '/chadm_uploads';
            $this->ctx->cssUrl = plugins_url('css', __FILE__);
            $this->ctx->jsUrl = plugins_url('js', __FILE__);

            //$url = plugins_url('js/sortable.js');
            wp_enqueue_script('chadm-Sortable', $this->ctx->jsUrl . '/sortable.js');


            add_shortcode('isosec_mem', [$this ,'isosec_shortcode']);
            //add_action( 'user_new_form', [$this,'isosec_admin_registration_form']);
            // Hooks to add extra user meta fields i.e Voice
            add_action('show_user_profile', ['ISOSEC_Hook_User', 'profileShow'] );
            add_action('edit_user_profile', ['ISOSEC_Hook_User', 'profileShow'] );
            add_action( 'personal_options_update', ['ISOSEC_Hook_User', 'profileSave'] );
            add_action( 'edit_user_profile_update', ['ISOSEC_Hook_User', 'profileSave'] );
            return;
        }

        public function autoload_classes($class) {
            $mydir = dirname(__FILE__);  // Ctx not yet ready
            $file = $mydir . '/classes/' . str_replace('_', '-', strtolower($class)) . '.php';
            if (file_exists($file)) {
                include $file;
            }
        }

        public function isosec_shortcode( $atts = [], $content, $shortcode) {
            // do something to $content
            // always return
            //ob_start();
            $ctx = $this->ctx;
            $selskapene = file_get_contents($ctx->pluginDir . '/selskapene.json');
            $selskapene = json_decode($selskapene);
            foreach ( $selskapene as $key => $val ) {
                $odict[$val->value] = $val->name;
            }
            $html = $ctx->getHtmlObj();
            $edition = 'tabell';
            if ( isset($atts['page'])) {
                $edition = 'public';
            }
            $page_tmpl = $html->getTemplate('memberList.html', $edition);
            $rad_tmpl = $html->getTemplatePart($page_tmpl, 'rad', true, true);

            $page = "";
            $users = get_users();
            $dette_aar = date('Y');
            $rundt_aar = [70, 75, 80, 85, 90];
            foreach($users as $user) {
                if ( in_array("administrator", $user->roles) ) {
                    continue;
                }

                $dict['display_name'] = $user->display_name;
                $dict['user_email'] = $user->user_email;
                $dict['phone'] = get_user_meta($user->ID, 'isosec_phone', true);
                $dict['born'] = get_user_meta($user->ID, 'isosec_born', true);
                $dict['jubilee'] = "";
                if ($dict['born'] != "") {
                    $fodt_aar = substr($dict['born'], 0, 4);
                    for ($i = 0; $i < 4; $i++) {
                        if ( $fodt_aar + $rundt_aar[$i] == $dette_aar ) {
                            $dict['jubilee'] = $dette_aar . '-' .
                                substr($dict['born'], 6) . " (" .  $rundt_aar[$i] . ")";
                        }
                    }

                }

                $company = get_user_meta($user->ID, 'isosec_company', true);
                $dict['company'] =  $company == "" ?  "" : $odict[$company];
                $page .= $html->replace($rad_tmpl, $dict);
            }

            $dict['rad'] = $page;
            $page = $html->replace($page_tmpl, $dict);
            return $page;
        }

    }
    $obj = new ISOSEC_Mem();
    $obj->init();
}

