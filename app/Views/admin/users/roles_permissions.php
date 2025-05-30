<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans("roles_permissions"); ?></h3>
        </div>
        <div class="right">
            <a href="<?= adminUrl('add-role'); ?>" class="btn btn-success btn-add-new">
                <i class="fa fa-plus"></i>&nbsp;&nbsp;<?= trans("add_role"); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th><?= trans("role_name"); ?></th>
                            <th><?= trans("permissions"); ?></th>
                            <th class="max-width-120"><?= trans("options"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($roles)):
                            foreach ($roles as $item): ?>
                                <tr>
                                    <td class="font-600">
                                        <?= esc(getRoleName($item, $activeLang->id)); ?>
                                        <?php if ($item->is_default): ?>
                                            &nbsp;<label class="label label-default"><?= trans("default"); ?></label>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item->is_super_admin == 1): ?>
                                            <label class="label label-success"><?= trans("all_permissions"); ?></label>
                                        <?php endif;
                                        if (!empty($item->permissions)):
                                            $permissions = @explode(',', $item->permissions);
                                            if (!empty($permissions) && is_array($permissions)):
                                                foreach ($permissions as $permission):
                                                    if (!empty($permission)):
                                                        if ($permission != "admin_panel"): ?>
                                                            <label class="label label-success"><?= trans($permission); ?></label>
                                                        <?php endif;
                                                    endif;
                                                endforeach;
                                            endif;
                                        endif; ?>
                                    </td>
                                    <td style="width: 180px;">
                                        <div class="btn-group btn-group-option">
                                            <?php if (isSuperAdmin() || $item->is_super_admin != 1): ?>
                                                <a href="<?= adminUrl('edit-role/' . $item->id); ?>" class="btn btn-sm btn-default btn-edit"><i class="fa fa-edit"></i>&nbsp;&nbsp;<?= trans("edit"); ?></a>
                                            <?php endif;
                                            if ($item->is_default != 1): ?>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-default btn-delete" onclick='deleteItem("Admin/deleteRolePost","<?= $item->id; ?>","<?= clrQuotes(trans("confirm_record", true)); ?>");'><i class="fa fa-trash-o"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;
                        endif; ?>
                        </tbody>
                    </table>
                    <?php if (empty($roles)): ?>
                        <p class="text-center">
                            <?= trans("no_records_found"); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>