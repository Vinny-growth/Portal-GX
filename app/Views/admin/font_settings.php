<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= trans('font_settings'); ?></h3>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="pull-right">
                    <a href="<?= adminUrl('font-settings?show=update'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-plus"></i>
                        <?= trans('add_font'); ?>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" id="cs_datatable_lang" role="grid" aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th width="20"><?= trans('id'); ?></th>
                                    <th><?= trans('font_name'); ?></th>
                                    <th><?= trans('font_url'); ?></th>
                                    <th><?= trans('font_family'); ?></th>
                                    <th><?= trans('language'); ?></th>
                                    <th class="max-width-120"><?= trans('options'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($fonts)):
                                    foreach ($fonts as $font):
                                        $langName = '';
                                        if (!empty($activeLanguages)) {
                                            foreach ($activeLanguages as $language) {
                                                if ($language->id == $font->lang_id) {
                                                    $langName = $language->name;
                                                    break;
                                                }
                                            }
                                        } ?>
                                        <tr>
                                            <td><?= esc($font->id); ?></td>
                                            <td><?= esc($font->font_name); ?></td>
                                            <td><?= esc($font->font_url); ?></td>
                                            <td><?= esc($font->font_family); ?></td>
                                            <td>
                                                <?php if ($font->lang_id == 0): ?>
                                                    <?= trans('all_languages'); ?>
                                                <?php else: ?>
                                                    <?= esc($langName); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn bg-purple dropdown-toggle btn-select-option"
                                                            type="button"
                                                            data-toggle="dropdown"><?= trans('select_option'); ?>
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu options-dropdown">
                                                        <li>
                                                            <a href="<?= adminUrl('font-settings?show=update&id=' . $font->id); ?>"><i class="fa fa-edit option-icon"></i><?= trans('edit'); ?></a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0)" onclick="deleteItem('Admin/deleteFontPost','<?= $font->id; ?>','<?= trans("confirm_font"); ?>');"><i class="fa fa-trash option-icon"></i><?= trans('delete'); ?></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (inputGet('show') == 'update'): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans('update_font'); ?></h3>
                </div>
                <form action="<?= base_url('Admin/updateFontPost'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id" value="<?= esc($font->id); ?>">
                    <div class="box-body">
                        <div class="form-group">
                            <label><?= trans("language"); ?></label>
                            <select name="lang_id" class="form-control" required>
                                <option value="0" <?= $fontUpdate->lang_id == 0 ? 'selected' : ''; ?>><?= trans('all_languages'); ?></option>
                                <?php foreach ($activeLanguages as $language): ?>
                                    <option value="<?= $language->id; ?>" <?= $fontUpdate->lang_id == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('font_name'); ?></label>
                            <input type="text" class="form-control" name="font_name" value="<?= esc($fontUpdate->font_name); ?>" placeholder="<?= trans('font_name'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('font_url'); ?> </label>
                            <input type="text" class="form-control" name="font_url" value="<?= esc($fontUpdate->font_url); ?>" placeholder="<?= trans('font_url'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('font_family'); ?> </label>
                            <input type="text" class="form-control" name="font_family" value="<?= esc($fontUpdate->font_family); ?>" placeholder="<?= trans('font_family'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('add_css_style'); ?> </label>
                            <textarea class="form-control text-area" name="css_style" placeholder="<?= trans('add_css_style_for_body'); ?>"><?= $fontUpdate->css_style; ?></textarea>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= trans('add_font'); ?></h3>
                </div>
                <form action="<?= base_url('Admin/addFontPost'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label><?= trans("language"); ?></label>
                            <select name="lang_id" class="form-control" required>
                                <option value="0"><?= trans('all_languages'); ?></option>
                                <?php foreach ($activeLanguages as $language): ?>
                                    <option value="<?= $language->id; ?>"><?= $language->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('font_name'); ?></label>
                            <input type="text" class="form-control" name="font_name" placeholder="<?= trans('font_name'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('font_url'); ?> </label>
                            <input type="text" class="form-control" name="font_url" placeholder="<?= trans('font_url'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('font_family'); ?> </label>
                            <input type="text" class="form-control" name="font_family" placeholder="<?= trans('font_family'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('add_css_style'); ?> </label>
                            <textarea class="form-control text-area" name="css_style" placeholder="<?= trans('add_css_style_for_body'); ?>"></textarea>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right"><?= trans('add_font'); ?></button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>