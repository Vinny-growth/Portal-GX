<?php namespace App\Models;

use CodeIgniter\Model;
use Config\Globals;

class SettingsModel extends BaseModel
{
    protected $builder;
    protected $builderGeneral;
    protected $builderWidgets;
    protected $builderFonts;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('settings');
        $this->builderGeneral = $this->db->table('general_settings');
        $this->builderWidgets = $this->db->table('widgets');
        $this->builderFonts = $this->db->table('fonts');
    }

    //input values
    public function inputValues()
    {
        return [
            'application_name' => inputPost('application_name'),
            'about_footer' => inputPost('about_footer'),
            'optional_url_button_name' => inputPost('optional_url_button_name'),
            'copyright' => inputPost('copyright'),
            'contact_address' => inputPost('contact_address'),
            'contact_email' => inputPost('contact_email'),
            'contact_phone' => inputPost('contact_phone'),
            'contact_text' => inputPost('contact_text'),
            'cookies_warning' => inputPost('cookies_warning'),
            'cookies_warning_text' => inputPost('cookies_warning_text')
        ];
    }

    //get bio settings
    public function getBioSettings()
    {
        $activeLang = Globals::$activeLang;
        $settings = $this->builder->where('lang_id', $activeLang->id)->get()->getRow();
        
        if (!$settings) {
            return (object) ['bio_description' => 'Assessoria em investimentos, gestão de patrimônio e consultoria financeira personalizada para o seu sucesso.'];
        }
        
        return $settings;
    }

    //update bio settings
    public function updateBioSettings($langId, $bioDescription)
    {
        $data = ['bio_description' => $bioDescription];
        return $this->builder->where('lang_id', $langId)->update($data);
    }

    //update settings
    public function updateSettings($langId)
    {
        $general = [
            'timezone' => inputPost('timezone'),
            'facebook_comment_active' => inputPost('facebook_comment_active'),
            'facebook_comment' => inputPost('facebook_comment'),
            'custom_header_codes' => inputPost('custom_header_codes'),
            'custom_footer_codes' => inputPost('custom_footer_codes')
        ];

        $uploadModel = new UploadModel();
        $logoPath = $uploadModel->uploadLogo('logo');
        $logoFooterPath = $uploadModel->uploadLogo('logo_footer');
        $logoEmailPath = $uploadModel->uploadLogo('logo_email');
        $faviconPath = $uploadModel->uploadFavicon('favicon');
        if (!empty($logoPath) && !empty($logoPath['path'])) {
            $general['logo'] = $logoPath['path'];
        }
        if (!empty($logoFooterPath) && !empty($logoFooterPath['path'])) {
            $general['logo_footer'] = $logoFooterPath['path'];
        }
        if (!empty($logoEmailPath) && !empty($logoEmailPath['path'])) {
            $general['logo_email'] = $logoEmailPath['path'];
        }
        if (!empty($faviconPath) && !empty($faviconPath['path'])) {
            $general['favicon'] = $faviconPath['path'];
        }

        $general['logo_size'] = '';
        $logoWidth = inputPost('logo_width');
        $logoHeight = inputPost('logo_height');
        if (!empty($logoWidth)) {
            $logoWidth = intval($logoWidth);
            if (intval($logoWidth) < 10 || intval($logoWidth) > 300) {
                $logoWidth = 160;
            }
            $general['logo_size'] .= $logoWidth;
        }
        if (!empty($logoHeight)) {
            $logoHeight = intval($logoHeight);
            if (intval($logoHeight) < 10 || intval($logoHeight) > 300) {
                $logoHeight = 60;
            }
            $general['logo_size'] .= 'x' . $logoHeight;
        }

        $this->builderGeneral->where('id', 1)->update($general);
        $data = $this->inputValues();

        $social = $this->getSocialMediaData(false);
        $data['social_media_data'] = !empty($social) ? serialize($social) : '';

        return $this->builder->where('lang_id', clrNum($langId))->update($data);
    }

    //update recaptcha settings
    public function updateRecaptchaSettings()
    {
        $data = [
            'recaptcha_site_key' => inputPost('recaptcha_site_key'),
            'recaptcha_secret_key' => inputPost('recaptcha_secret_key')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update maintenance mode settings
    public function updateMaintenanceModeSettings()
    {
        $data = [
            'maintenance_mode_title' => inputPost('maintenance_mode_title'),
            'maintenance_mode_description' => inputPost('maintenance_mode_description'),
            'maintenance_mode_status' => inputPost('maintenance_mode_status')
        ];
        if (empty($data["maintenance_mode_status"])) {
            $data["maintenance_mode_status"] = 0;
        }
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update preferences
    public function updatePreferences($form)
    {
        if ($form == 'general') {
            $data = [
                'multilingual_system' => inputPost('multilingual_system'),
                'registration_system' => inputPost('registration_system'),
                'sticky_sidebar' => inputPost('sticky_sidebar'),
                'show_rss' => inputPost('show_rss'),
                'rss_content_type' => inputPost('rss_content_type'),
                'file_manager_show_files' => inputPost('file_manager_show_files'),
                'delete_images_with_post' => inputPost('delete_images_with_post'),
                'audio_download_button' => inputPost('audio_download_button'),
                'show_user_email_on_profile' => inputPost('show_user_email_on_profile'),
                'pwa_status' => inputPost('pwa_status')
            ];

            //pwa logo
            $uploadModel = new UploadModel();
            $tempFile = $uploadModel->uploadTempFile('pwa_logo');
            if (!empty($tempFile) && !empty($tempFile['path'])) {
                $pwaLogo = $this->generalSettings->pwa_logo;
                if (!empty($pwaLogo)) {
                    $pwaLogoArr = unserializeData($pwaLogo);
                    if (!empty($pwaLogoArr) && countItems($pwaLogoArr) > 0) {
                        if (!empty($pwaLogoArr['lg']) && file_exists($pwaLogoArr['lg'])) {
                            @unlink($pwaLogoArr['lg']);
                        }
                        if (!empty($pwaLogoArr['md']) && file_exists($pwaLogoArr['md'])) {
                            @unlink($pwaLogoArr['md']);
                        }
                        if (!empty($pwaLogoArr['sm']) && file_exists($pwaLogoArr['sm'])) {
                            @unlink($pwaLogoArr['sm']);
                        }
                    }
                }
                $newLogo = [
                    'lg' => $uploadModel->uploadPwaLogo($tempFile['path'], 512, 512),
                    'md' => $uploadModel->uploadPwaLogo($tempFile['path'], 192, 192),
                    'sm' => $uploadModel->uploadPwaLogo($tempFile['path'], 144, 144)
                ];
                $data['pwa_logo'] = serialize($newLogo);
            }

        } elseif ($form == 'homepage') {
            $data = [
                'show_featured_section' => inputPost('show_featured_section'),
                'show_latest_posts' => inputPost('show_latest_posts'),
                'show_newsticker' => inputPost('show_newsticker'),
                'show_latest_posts_on_slider' => inputPost('show_latest_posts_on_slider'),
                'show_latest_posts_on_featured' => inputPost('show_latest_posts_on_featured'),
                'sort_slider_posts' => inputPost('sort_slider_posts'),
                'sort_featured_posts' => inputPost('sort_featured_posts')
            ];
        } elseif ($form == 'posts') {
            $data = [
                'post_url_structure' => inputPost('post_url_structure'),
                'bulk_post_upload_for_authors' => inputPost('bulk_post_upload_for_authors'),
                'comment_system' => inputPost('comment_system'),
                'comment_approval_system' => inputPost('comment_approval_system'),
                'emoji_reactions' => inputPost('emoji_reactions'),
                'show_post_author' => inputPost('show_post_author'),
                'show_post_date' => inputPost('show_post_date'),
                'show_hits' => inputPost('show_hits'),
                'approve_added_user_posts' => inputPost('approve_added_user_posts'),
                'approve_updated_user_posts' => inputPost('approve_updated_user_posts'),
                'redirect_rss_posts_to_original' => inputPost('redirect_rss_posts_to_original'),
                'pagination_per_page' => inputPost('pagination_per_page')
            ];
        } elseif ($form == 'post_formats') {
            $data = [
                'post_format_article' => inputPost('post_format_article'),
                'post_format_gallery' => inputPost('post_format_gallery'),
                'post_format_sorted_list' => inputPost('post_format_sorted_list'),
                'post_format_table_of_contents' => inputPost('post_format_table_of_contents'),
                'post_format_video' => inputPost('post_format_video'),
                'post_format_audio' => inputPost('post_format_audio'),
                'post_format_trivia_quiz' => inputPost('post_format_trivia_quiz'),
                'post_format_personality_quiz' => inputPost('post_format_personality_quiz'),
                'post_format_poll' => inputPost('post_format_poll'),
                'post_format_recipe' => inputPost('post_format_recipe')
            ];
        } elseif ($form == 'post_deletion') {
            $data = [
                'auto_post_deletion' => inputPost('auto_post_deletion'),
                'auto_post_deletion_days' => inputPost('auto_post_deletion_days'),
                'auto_post_deletion_delete_all' => inputPost('auto_post_deletion_delete_all')
            ];
        } elseif ($form == 'openai_env') {
            $envKey = trim(inputPost('openai_api_key') ?? '');
            $model = trim(inputPost('openai_model') ?? '');
            $size = trim(inputPost('openai_size') ?? '');
            $quality = trim(inputPost('openai_quality') ?? '');
            $textModel = trim(inputPost('openai_text_model') ?? '');
            $textFallback = trim(inputPost('openai_text_fallback_model') ?? '');
            $textTimeout = trim(inputPost('openai_text_timeout') ?? '');
            $brandStyle = trim(inputPost('openai_brand_style') ?? '');

            $ok = true;
            $ok = $ok && $this->setEnvVar('OPENAI_API_KEY', $envKey);
            if (!empty($model)) { $ok = $ok && $this->setEnvVar('OPENAI_DEFAULT_MODEL', $model); }
            if (!empty($size)) { $ok = $ok && $this->setEnvVar('OPENAI_DEFAULT_SIZE', $size); }
            if (!empty($quality)) { $ok = $ok && $this->setEnvVar('OPENAI_DEFAULT_QUALITY', $quality); }
            if (!empty($textModel)) { $ok = $ok && $this->setEnvVar('OPENAI_TEXT_MODEL', $textModel); }
            if (!empty($textFallback)) { $ok = $ok && $this->setEnvVar('OPENAI_TEXT_FALLBACK_MODEL', $textFallback); }
            if (!empty($textTimeout)) { $ok = $ok && $this->setEnvVar('OPENAI_TEXT_TIMEOUT', $textTimeout); }
            // brand style can be empty to clear; still persist
            $ok = $ok && $this->setEnvVar('OPENAI_BRAND_STYLE', $brandStyle);
            return $ok;
        }
        if (!empty($data)) {
            return $this->builderGeneral->where('id', 1)->update($data);
        }
        return false;
    }

    /**
     * Safely set/update an environment var in .env and process env.
     */
    protected function setEnvVar(string $name, string $value): bool
    {
        $envPath = FCPATH . '.env';

        // Build line in KEY="value" format with escaping
        $escaped = str_replace(["\\", '"'], ["\\\\", '\\"'], $value);
        $newLine = $name . '="' . $escaped . '"';

        $contents = '';
        if (file_exists($envPath)) {
            $contents = file_get_contents($envPath);
            if ($contents === false) {
                return false;
            }
            $pattern = '/^(?!\s*#)\s*' . preg_quote($name, '/') . '\s*=\s*.*$/m';
            if (preg_match($pattern, $contents)) {
                $contents = preg_replace($pattern, $newLine, $contents);
            } else {
                $contents = rtrim($contents, "\r\n") . "\n" . $newLine . "\n";
            }
        } else {
            $contents = $newLine . "\n";
        }

        // Attempt to write back
        $result = @file_put_contents($envPath, $contents) !== false;
        if ($result) {
            // Update current process environment
            @putenv($name . '=' . $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
        return $result;
    }

    //get socail social media links
    public function getSocialMediaData($personalWebsite = true)
    {
        $data = array();
        if ($personalWebsite == true && !empty(inputPost('personal_website_url'))) {
            $data['personal_website_url'] = addHttpsToUrl(trim(inputPost('personal_website_url')));
        }
        $socialArray = getSocialLinksArray();
        foreach ($socialArray as $item) {
            $inputValue = inputPost($item['name']);
            if (!empty($inputValue)) {
                $inputValue = trim($inputValue);
                if (!empty($inputValue)) {
                    $data[$item['name']] = addHttpsToUrl($inputValue);
                }
            }
        }
        return $data;
    }

    //update ai writer settings
    public function updateAIWriterSettings()
    {
        $data = [
            'status' => inputPost('status'),
            'api_key' => inputPost('api_key'),
            'temperature' => inputPost('temperature')
        ];
        return $this->builderGeneral->where('id', 1)->update(['ai_writer' => serialize($data)]);
    }

    //update file upload settings
    public function updateFileUploadSettings()
    {
        $data['image_file_format'] = inputPost('image_file_format');
        if ($data['image_file_format'] != 'JPG' && $data['image_file_format'] != 'PNG' && $data['image_file_format'] != 'WEBP') {
            $data['image_file_format'] = 'JPG';
        }

        $extStr = '';
        $exts = '';
        $input = inputPost('allowed_file_extensions');
        if (!empty($input)) {
            $input = json_decode($input, true);
            if (!empty($input)) {
                $exts = array_map(function ($item) {
                    return strtolower($item['value'] ?? '');
                }, $input);
            }
            if (!empty($exts)) {
                $extStr = implode(',', $exts);
            }
        }
        $data['allowed_file_extensions'] = $extStr;
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update seo settings
    public function updateSeoSettings()
    {
        $submit = inputPost('submit');
        if ($submit == 'google_analytics') {
            $data = [
                'google_analytics' => inputPost('google_analytics'),
            ];
            return $this->builderGeneral->where('id', 1)->update($data);
        } elseif ($submit == 'settings') {
            $langId = inputPost('lang_id');
            $data = [
                'site_title' => inputPost('site_title'),
                'home_title' => inputPost('home_title'),
                'site_description' => inputPost('site_description'),
                'keywords' => inputPost('keywords')
            ];
            return $this->builder->where('lang_id', clrNum($langId))->update($data);
        }
        return true;
    }


    //update google indexing api settings
    public function updateGoogleIndexingApiSettings()
    {
        $data = [
            'google_indexing_api' => !empty(inputPost('google_indexing_api')) ? 1 : 0
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update IndexNow settings
    public function updateIndexNowSettings()
    {
        $enabled = !empty(inputPost('indexnow_enabled')) ? 1 : 0;
        $newKey = trim(inputPost('indexnow_api_key') ?? '');

        // Get current key to handle file cleanup
        $current = $this->builderGeneral->where('id', 1)->get()->getRow();
        $oldKey = $current->indexnow_api_key ?? '';

        // If key changed, remove old key file and create new one
        if (!empty($newKey) && $newKey !== $oldKey) {
            \App\Libraries\IndexNowClient::removeKeyFile($oldKey);
            \App\Libraries\IndexNowClient::ensureKeyFile($newKey);
        }

        // If enabling, ensure key file exists
        if ($enabled && !empty($newKey)) {
            \App\Libraries\IndexNowClient::ensureKeyFile($newKey);
        }

        $data = [
            'indexnow_enabled' => $enabled,
            'indexnow_api_key' => $newKey,
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }
    
    //get seo settings base data
    private function getSeoSettingsBase($langId)
    {
        $langId = clrNum($langId);
        return $this->builder->where('lang_id', $langId)->get()->getRow();
    }

    //update route settings
    public function updateRouteSettings()
    {
        $routes = \Config\App::$routes;
        $routesArray = [];
        foreach ($routes as $key => $value) {
            $routesArray[$key] = inputPost($key);
        }
        $data = [
            'routes' => serialize($routesArray)
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }
    
    //get routes
    public function getRoutes()
    {
        $settings = $this->getGeneralSettings();
        if (!empty($settings) && !empty($settings->routes) && is_string($settings->routes)) {
            return unserializeData($settings->routes);
        }
        return null;
    }

    //update email settings
    public function updateEmailSettings()
    {
        $data = [
            'mail_protocol' => inputPost('mail_protocol'),
            'mail_service' => inputPost('mail_service'),
            'mail_title' => inputPost('mail_title'),
            'mail_encryption' => inputPost('mail_encryption'),
            'mail_host' => inputPost('mail_host'),
            'mail_port' => inputPost('mail_port'),
            'mail_username' => inputPost('mail_username'),
            'mail_password' => inputPost('mail_password'),
            'mail_reply_to' => inputPost('mail_reply_to'),
            'mailjet_api_key' => inputPost('mailjet_api_key'),
            'mailjet_secret_key' => inputPost('mailjet_secret_key'),
            'mailjet_email_address' => inputPost('mailjet_email_address')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update contact email settings
    public function updateContactEmailSettings()
    {
        $data = [
            'mail_contact' => inputPost('mail_contact'),
            'mail_contact_status' => inputPost('mail_contact_status')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update email verification settings
    public function updateEmailVerificationSettings()
    {
        $data = [
            'email_verification' => inputPost('email_verification')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }
    
    //update general settings
    public function updateGeneralSettings()
    {
        $data = [
            'application_name' => inputPost('application_name'),
            'timezone' => inputPost('timezone'),
            'copyright' => inputPost('copyright')
        ];
        
        $uploadModel = new \App\Models\UploadModel();
        $logoPath = $uploadModel->uploadLogo('logo');
        if (!empty($logoPath) && !empty($logoPath['path'])) {
            $data['logo'] = $logoPath['path'];
        }
        
        $faviconPath = $uploadModel->uploadFavicon('favicon');
        if (!empty($faviconPath) && !empty($faviconPath['path'])) {
            $data['favicon'] = $faviconPath['path'];
        }
        
        return $this->builderGeneral->where('id', 1)->update($data);
    }
    
    //update cookies warning settings
    public function updateCookiesSettings()
    {
        $cookiesData = [
            'cookies_warning' => inputPost('cookies_warning'),
            'cookies_warning_text' => inputPost('cookies_warning_text')
        ];
        
        $data = [
            'cookies_settings' => json_encode($cookiesData)
        ];
        
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update meta conversions api settings
    public function updateMetaConversionsApiSettings()
    {
        $trackEvents = inputPost('track_events');
        if (empty($trackEvents) || !is_array($trackEvents)) {
            $trackEvents = [];
        }
        
        $metaApiData = [
            'pixel_id' => inputPost('pixel_id'),
            'access_token' => inputPost('access_token'),
            'test_event_code' => inputPost('test_event_code'),
            'api_enabled' => inputPost('meta_api_enabled'),
            'test_mode' => inputPost('meta_test_mode'),
            'track_events' => $trackEvents
        ];
        
        $data = [
            'meta_conversions_api' => json_encode($metaApiData)
        ];
        
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update storage settings
    public function updateStorageSettings()
    {
        $data = [
            'storage' => inputPost('storage')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update aws s3
    public function updateAwsS3()
    {
        $data = [
            'aws_key' => inputPost('aws_key'),
            'aws_secret' => inputPost('aws_secret'),
            'aws_bucket' => inputPost('aws_bucket'),
            'aws_region' => inputPost('aws_region')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update cache system
    public function updateCacheSystem()
    {
        if (inputPost('action') == 'save_static') {
            $data = [
                'static_cache_system' => inputPost('static_cache_system'),
            ];
        } else {
            $data = [
                'cache_system' => inputPost('cache_system'),
                'refresh_cache_database_changes' => inputPost('refresh_cache_database_changes'),
                'cache_refresh_time' => inputPost('cache_refresh_time') * 60
            ];
        }
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update google news settings
    public function updateGoogleNewsSettings()
    {
        $data = [
            'google_news' => inputPost('google_news')
        ];
        return $this->builderGeneral->where('id', 1)->update($data);
    }

    //update social login settings
    public function updateSocialLoginSettings()
    {
        $loginType = inputPost('login_type');
        
        if ($loginType == 'facebook') {
            $data = [
                'facebook_app_id' => inputPost('facebook_app_id'),
                'facebook_app_secret' => inputPost('facebook_app_secret'),
                'facebook_login' => inputPost('facebook_login_status')
            ];
        } elseif ($loginType == 'google') {
            $data = [
                'google_client_id' => inputPost('google_client_id'),
                'google_client_secret' => inputPost('google_client_secret'),
                'google_login' => inputPost('google_login_status')
            ];
        } elseif ($loginType == 'vk') {
            $data = [
                'vk_app_id' => inputPost('vk_app_id'),
                'vk_secure_key' => inputPost('vk_secure_key'),
                'vk_redirect_url' => inputPost('vk_redirect_url'),
                'vk_login' => inputPost('vk_login_status')
            ];
        }
        
        if (!empty($data)) {
            return $this->builderGeneral->where('id', 1)->update($data);
        }
        return false;
    }

    //update social settings
    public function updateSocialSettings()
    {
        $submit = inputPost('submit');
        if ($submit == 'facebook') {
            $data = [
                'facebook_app_id' => inputPost('facebook_app_id'),
                'facebook_app_secret' => inputPost('facebook_app_secret')
            ];
        } elseif ($submit == 'google') {
            $data = [
                'google_client_id' => inputPost('google_client_id'),
                'google_client_secret' => inputPost('google_client_secret')
            ];
        } elseif ($submit == 'vk') {
            $data = [
                'vk_app_id' => inputPost('vk_app_id'),
                'vk_secure_key' => inputPost('vk_secure_key')
            ];
        }
        if (!empty($data)) {
            return $this->builderGeneral->where('id', 1)->update($data);
        }
        return true;
    }

    //get settings
    public function getSettings($langId)
    {
        return $this->builder->where('lang_id', clrNum($langId))->get()->getRow();
    }

    //get general settings
    public function getGeneralSettings()
    {
        return $this->builderGeneral->where('id', 1)->get()->getRow();
    }

    //get seo settings
    public function getSeoSettings($langId)
    {
        $settings = $this->getSeoSettingsBase($langId);
        if (!$settings) {
            $settings = new \stdClass();
            $settings->site_title = '';
            $settings->home_title = '';
            $settings->site_description = '';
            $settings->keywords = '';
        }
        return $settings;
    }

    //set theme mode
    public function setThemeMode()
    {
        $mode = inputPost('theme_mode');
        if ($mode == 'light' || $mode == 'dark') {
            helperSetCookie('theme_mode', $mode);
            $this->builderGeneral->where('id', 1)->update(['theme_mode' => $mode]);
        }
    }

    //set theme
    public function setTheme()
    {
        $id = inputPost('theme_id');
        $theme = $this->getTheme($id);
        if (!empty($theme)) {
            $this->db->table('themes')->update(['is_active' => 0]);
            $this->db->table('themes')->where('id', $theme->id)->update(['is_active' => 1]);
        }
    }

    //set theme settings
    public function setThemeSettings()
    {
        $id = inputPost('id');
        $theme = $this->getTheme($id);
        if (!empty($theme)) {
            $data = [
                'theme_color' => inputPost('theme_color'),
                'block_color' => inputPost('block_color'),
                'mega_menu_color' => inputPost('mega_menu_color')
            ];
            $this->db->table('themes')->where('id', $theme->id)->update($data);
        }
    }

    //get theme
    public function getTheme($id)
    {
        return $this->db->table('themes')->where('id', clrNum($id))->get()->getRow();
    }

    //get themes
    public function getThemes()
    {
        return $this->db->table('themes')->get()->getResult();
    }

    //set last cron update
    public function setLastCronUpdate()
    {
        return $this->builderGeneral->where('id', 1)->update(['last_cron_update' => date('Y-m-d H:i:s')]);
    }

    //delete old sessions
    function deleteOldSessions()
    {
        $days = date('Y-m-d H:i:s', strtotime('-8 days'));
        $this->db->table('ci_sessions')->where('timestamp <', $days)->delete();
    }

    //download database backup
    public function downloadBackup()
    {
        $prefs = array(
            'tables' => array(),
            'ignore' => array(),
            'filename' => '',
            'format' => 'gzip', // gzip, zip, txt
            'add_drop' => TRUE,
            'add_insert' => TRUE,
            'newline' => "\n",
            'foreign_key_checks' => TRUE
        );
        if (count($prefs['tables']) === 0) {
            $prefs['tables'] = $this->db->listTables();
        }
        // Extract the prefs for simplicity
        extract($prefs);
        $output = '';
        // Do we need to include a statement to disable foreign key checks?
        if ($foreign_key_checks === FALSE) {
            $output .= 'SET foreign_key_checks = 0;' . $newline;
        }
        foreach ((array)$tables as $table) {
            // Is the table in the "ignore" list?
            if (in_array($table, (array)$ignore, TRUE)) {
                continue;
            }
            // Get the table schema
            $query = $this->db->query('SHOW CREATE TABLE ' . $this->db->escapeIdentifiers($this->db->database . '.' . $table));
            // No result means the table name was invalid
            if ($query === FALSE) {
                continue;
            }
            // Write out the table schema
            $output .= '#' . $newline . '# TABLE STRUCTURE FOR: ' . $table . $newline . '#' . $newline . $newline;

            if ($add_drop === TRUE) {
                $output .= 'DROP TABLE IF EXISTS ' . $this->db->protectIdentifiers($table) . ';' . $newline . $newline;
            }
            $i = 0;
            $result = $query->getResultArray();
            foreach ($result[0] as $val) {
                if ($i++ % 2) {
                    $output .= $val . ';' . $newline . $newline;
                }
            }
            // If inserts are not needed we're done...
            if ($add_insert === FALSE) {
                continue;
            }
            // Grab all the data from the current table
            $query = $this->db->query('SELECT * FROM ' . $this->db->protectIdentifiers($table));

            if ($query->getFieldCount() === 0) {
                continue;
            }
            // Fetch the field names and determine if the field is an
            // integer type. We use this info to decide whether to
            // surround the data with quotes or not
            $i = 0;
            $field_str = '';
            $isInt = array();
            while ($field = $query->resultID->fetch_field()) {
                // Most versions of MySQL store timestamp as a string
                $isInt[$i] = in_array($field->type, array(MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_INT24, MYSQLI_TYPE_LONG), TRUE);

                // Create a string of field names
                $field_str .= $this->db->escapeIdentifiers($field->name) . ', ';
                $i++;
            }
            // Trim off the end comma
            $field_str = preg_replace('/, $/', '', $field_str);
            // Build the insert string
            foreach ($query->getResultArray() as $row) {
                $valStr = '';
                $i = 0;
                foreach ($row as $v) {
                    if ($v === NULL) {
                        $valStr .= 'NULL';
                    } else {
                        // Escape the data if it's not an integer
                        $valStr .= ($isInt[$i] === FALSE) ? $this->db->escape($v) : $v;
                    }
                    // Append a comma
                    $valStr .= ', ';
                    $i++;
                }
                // Remove the comma at the end of the string
                $valStr = preg_replace('/, $/', '', $valStr);
                // Build the INSERT string
                $output .= 'INSERT INTO ' . $this->db->protectIdentifiers($table) . ' (' . $field_str . ') VALUES (' . $valStr . ');' . $newline;
            }
            $output .= $newline . $newline;
        }
        // Do we need to include a statement to re-enable foreign key checks?
        if ($foreign_key_checks === FALSE) {
            $output .= 'SET foreign_key_checks = 1;' . $newline;
        }
        return $output;
    }

    /*
    *------------------------------------------------------------------------------------------
     * WIDGETS
    *------------------------------------------------------------------------------------------
    */

    //input values
    public function inputValuesWidget()
    {
        return [
            'lang_id' => inputPost('lang_id'),
            'title' => inputPost('title'),
            'content' => inputPost('content'),
            'widget_order' => inputPost('widget_order'),
            'visibility' => inputPost('visibility'),
            'is_custom' => inputPost('is_custom'),
            'display_category_id' => inputPost('display_category_id')
        ];
    }

    //add widget
    public function addWidget()
    {
        $data = $this->inputValuesWidget();
        $data['is_custom'] = 1;
        $data['type'] = 'custom';
        if (empty($data['display_category_id']) || $data['display_category_id'] == 'latest_posts') {
            $data['display_category_id'] = '';
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->builderWidgets->insert($data);
    }

    //update widget
    public function editWidget($id)
    {
        $widget = $this->getWidget($id);
        if (!empty($widget)) {
            $data = $this->inputValuesWidget();
            $data['is_custom'] = $widget->is_custom;
            if (empty($data['display_category_id']) || $data['display_category_id'] == 'latest_posts') {
                $data['display_category_id'] = '';
            }
            return $this->builderWidgets->where('id', $widget->id)->update($data);
        }
        return true;
    }

    //get widgets
    public function getWidgets()
    {
        return $this->builderWidgets->orderBy('widget_order')->get()->getResult();
    }

    //get widgets by lang
    public function getWidgetsByLang($langId)
    {
        return $this->builderWidgets->where('lang_id', clrNum($langId))->orderBy('widget_order')->get()->getResult();
    }

    //get widget
    public function getWidget($id)
    {
        return $this->builderWidgets->where('id', clrNum($id))->get()->getRow();
    }

    //delete widget
    public function deleteWidget($id)
    {
        $widget = $this->getWidget($id);
        if (!empty($widget)) {
            return $this->builderWidgets->where('id', $widget->id)->delete();
        }
        return false;
    }

    /*
    *------------------------------------------------------------------------------------------
     * FONTS
    *------------------------------------------------------------------------------------------
    */

    //get selected fonts
    public function getSelectedFonts($settings)
    {
        $arrayFonts = array();
        $fonts = $this->builderFonts->whereIn('id', [clrNum($settings->primary_font), clrNum($settings->secondary_font), clrNum($settings->tertiary_font)], false)->get()->getResult();
        if (!empty($fonts)) {
            foreach ($fonts as $font) {
                if ($font->id == $settings->primary_font) {
                    $arrayFonts['primary'] = $font;
                }
                if ($font->id == $settings->secondary_font) {
                    $arrayFonts['secondary'] = $font;
                }
                if ($font->id == $settings->tertiary_font) {
                    $arrayFonts['tertiary'] = $font;
                }
            }
        }
        return $arrayFonts;
    }

    //get fonts
    public function getFonts()
    {
        return $this->builderFonts->get()->getResult();
    }

    //get font
    public function getFont($id)
    {
        return $this->builderFonts->where('id', clrNum($id))->get()->getRow();
    }

    //add font
    public function addFont()
    {
        $data = [
            'font_name' => inputPost('font_name'),
            'font_url' => inputPost('font_url'),
            'font_family' => inputPost('font_family'),
            'font_source' => 'google',
            'has_local_file' => 0,
            'is_default' => 0
        ];
        $data['font_key'] = strSlug($data['font_name']);
        return $this->builderFonts->insert($data);
    }

    //edit font
    public function editFont($id)
    {
        $font = $this->getFont($id);
        if (!empty($font)) {
            $data = [
                'font_name' => inputPost('font_name'),
                'font_url' => inputPost('font_url'),
                'font_family' => inputPost('font_family')
            ];
            return $this->builderFonts->where('id', clrNum($id))->update($data);
        }
        return false;
    }

    //set site font
    public function setSiteFont()
    {
        $langId = inputPost('lang_id');
        $data = [
            'primary_font' => inputPost('primary_font'),
            'secondary_font' => inputPost('secondary_font'),
            'tertiary_font' => inputPost('tertiary_font'),
        ];
        return $this->db->table('settings')->where('lang_id', clrNum($langId))->update($data);
    }

    //update font settings
    public function setDefaultFonts()
    {
        $langId = inputPost('lang_id');
        $data = [
            'primary_font' => inputPost('primary_font'),
            'secondary_font' => inputPost('secondary_font'),
            'tertiary_font' => inputPost('tertiary_font'),
        ];
        return $this->db->table('settings')->where('lang_id', clrNum($langId))->update($data);
    }

    //delete font
    public function deleteFont($id)
    {
        $font = $this->getFont($id);
        if (!empty($font)) {
            return $this->builderFonts->where('id', $font->id)->delete();
        }
        return false;
    }
}
