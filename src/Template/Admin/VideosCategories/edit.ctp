<?php
/**
 * This file is part of me-cms-youtube.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-cms-youtube
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
$this->extend('MeCms./Admin/Common/form');
$this->assign('title', $title = __d('me_cms', 'Edit videos category'));
$this->Library->slugify();
?>

<?= $this->Form->create($category); ?>
<div class="row">
    <div class="col-lg-3 order-12">
        <div class="float-form">
        <?php
        if (!empty($categories)) {
            echo $this->Form->control('parent_id', [
                'label' => I18N_PARENT_CATEGORY,
                'options' => $categories,
                'help' => I18N_BLANK_TO_CREATE_CATEGORY,
            ]);
        }
        ?>
        </div>
    </div>
    <fieldset class="col-lg-9">
    <?php
        echo $this->Form->control('title', [
            'id' => 'title',
            'label' => I18N_TITLE,
        ]);
        echo $this->Form->control('slug', [
            'id' => 'slug',
            'label' => I18N_SLUG,
            'help' => __d('me_cms', 'The slug is a string identifying a resource.' .
                'If you do not have special needs, let it be generated automatically'),
        ]);
        echo $this->Form->control('description', [
            'label' => I18N_DESCRIPTION,
            'rows' => 3,
        ]);
    ?>
    </fieldset>
</div>
<?= $this->Form->submit($title) ?>
<?= $this->Form->end() ?>