<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= trans('route_settings'); ?></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('general'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/routeSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="lang_id" value="1">
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('admin'); ?></label>
                        <input type="text" class="form-control" name="admin" placeholder="admin" value="<?= $routes->admin; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('profile'); ?></label>
                        <input type="text" class="form-control" name="profile" placeholder="profile" value="<?= $routes->profile; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('tag'); ?></label>
                        <input type="text" class="form-control" name="tag" placeholder="tag" value="<?= $routes->tag; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('reading_list'); ?></label>
                        <input type="text" class="form-control" name="reading_list" placeholder="reading-list" value="<?= $routes->reading_list; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('settings'); ?></label>
                        <input type="text" class="form-control" name="settings" placeholder="settings" value="<?= $routes->settings; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('social_accounts'); ?></label>
                        <input type="text" class="form-control" name="social_accounts" placeholder="social-accounts" value="<?= $routes->social_accounts; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('preferences'); ?></label>
                        <input type="text" class="form-control" name="preferences" placeholder="preferences" value="<?= $routes->preferences; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('visual_settings'); ?></label>
                        <input type="text" class="form-control" name="visual_settings" placeholder="visual-settings" value="<?= $routes->visual_settings; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('change_password'); ?></label>
                        <input type="text" class="form-control" name="change_password" placeholder="change-password" value="<?= $routes->change_password; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('forgot_password'); ?></label>
                        <input type="text" class="form-control" name="forgot_password" placeholder="forgot-password" value="<?= $routes->forgot_password; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('reset_password'); ?></label>
                        <input type="text" class="form-control" name="reset_password" placeholder="reset-password" value="<?= $routes->reset_password; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('delete_account'); ?></label>
                        <input type="text" class="form-control" name="delete_account" placeholder="delete-account" value="<?= $routes->delete_account; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('register'); ?></label>
                        <input type="text" class="form-control" name="register" placeholder="register" value="<?= $routes->register; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('posts'); ?></label>
                        <input type="text" class="form-control" name="posts" placeholder="posts" value="<?= $routes->posts; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('search'); ?></label>
                        <input type="text" class="form-control" name="search" placeholder="search" value="<?= $routes->search; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('logout'); ?></label>
                        <input type="text" class="form-control" name="logout" placeholder="logout" value="<?= $routes->logout; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('cookies_warning'); ?></label>
                        <input type="text" class="form-control" name="cookies_warning" placeholder="cookies-warning" value="<?= $routes->cookies_warning; ?>">
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('pages'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/routeSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="lang_id" value="2">
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('contact'); ?></label>
                        <input type="text" class="form-control" name="contact" placeholder="contact" value="<?= $routes->contact; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('gallery'); ?></label>
                        <input type="text" class="form-control" name="gallery" placeholder="gallery" value="<?= $routes->gallery; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('gallery_album'); ?></label>
                        <input type="text" class="form-control" name="gallery_album" placeholder="gallery-album" value="<?= $routes->gallery_album; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('rss_feeds'); ?></label>
                        <input type="text" class="form-control" name="rss_feeds" placeholder="rss-feeds" value="<?= $routes->rss_feeds; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('terms_conditions'); ?></label>
                        <input type="text" class="form-control" name="terms_conditions" placeholder="terms-conditions" value="<?= $routes->terms_conditions; ?>">
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('membership'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/routeSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="lang_id" value="3">
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('select_membership_plan'); ?></label>
                        <input type="text" class="form-control" name="select_membership_plan" placeholder="select-membership-plan" value="<?= $routes->select_membership_plan; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('paypal'); ?></label>
                        <input type="text" class="form-control" name="paypal" placeholder="paypal" value="<?= $routes->paypal; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('stripe'); ?></label>
                        <input type="text" class="form-control" name="stripe" placeholder="stripe" value="<?= $routes->stripe; ?>">
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>