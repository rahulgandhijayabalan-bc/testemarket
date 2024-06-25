<?php
/* =-=-=-= Copyright © 2018 eMarket =-=-=-=  
  |    GNU GENERAL PUBLIC LICENSE v.3.0    |
  |  https://github.com/musicman3/eMarket  |
  =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */

use eMarket\Core\{
    Lang,
    Pages,
    Settings
};
?>

<div id="index" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light py-2 px-3">
                <h5 class="modal-title"><?php echo Settings::titlePageGenerator() ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form_add" class="was-validated" name="form_add" action="javascript:void(null);" onsubmit="Ajax.callAdd()">
                <div class="modal-body">
                    <input type="hidden" id="add" name="add" value="" />
                    <input type="hidden" id="edit" name="edit" value="" />

                    <?php require_once(ROOT . '/view/' . Settings::template() . '/layouts/lang_tabs_add.php') ?>

                    <div class="tab-content pt-2">

                        <?php for ($x = 0; $x < Lang::$count; $x++) { ?>

                            <div id="<?php echo lang('#lang_all')[$x] ?>" class="tab-pane fade <?php echo Pages::activeTab($x) ?>">
                                <div class="mb-2">
                                    <small class="form-text text-muted"><?php echo lang('weight_name_full') ?></small>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bi-file-text"></span>
                                        <input class="form-control" placeholder="<?php echo lang('enter_value') ?>" type="text" name="name_weight_<?php echo $x ?>" id="name_weight_<?php echo $x ?>" required />
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <small class="form-text text-muted"><?php echo lang('weight_name_little') ?></small>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bi-file-text"></span>
                                        <input class="form-control" placeholder="<?php echo lang('enter_value') ?>" type="text" name="code_weight_<?php echo $x ?>" id="code_weight_<?php echo $x ?>" required />
                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                        <div class="mb-2">
                            <small class="form-text text-muted"><?php echo lang('weight_value') ?></small>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bi-calculator"></span>
                                <input class="form-control" placeholder="<?php echo lang('enter_value') ?>" type="text" name="value_weight" pattern="\d+(\.\d{0,7})?" id="value_weight" required />
                            </div>
                        </div>
                        <div class="mb-2 form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="default_weight" id="default_weight" checked>
                            <label class="form-check-label" for="default_weight"><?php echo lang('default_set') ?></label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm bi-x-circle" type="button" data-bs-dismiss="modal"> <?php echo lang('cancel') ?></button>
                    <button class="btn btn-primary btn-sm bi-check-circle" type="submit"> <?php echo lang('save') ?></button>
                </div>

            </form>
        </div>
    </div>
</div>