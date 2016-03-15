<?php

use yii\helpers\Html;

/*
 * Copyright (C) 2014 Claude Janz <claude.janz@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<?= $form->field($login, 'username')
?>

<?= $form->field($login, 'password')->passwordInput() ?>
<?php
$options = [];
if (isset($form->fieldConfig['template'])) {
//echo $form->fieldConfig['template'];
    $options = ['template' => "<div class=\"col-md-offset-3 col-md-3\">{input}</div>\n<div class=\"col-md-6\">{error}</div>"];
}
echo $form->field($login, 'rememberMe', $options)->checkbox()
?>
<div class='row'>
    <div class="<?= isset($form->fieldConfig['template']) ? 'col-md-offset-3 col-md-9' : 'col-md-12'; ?>">
        <p>
            Si vous avez oublié votre mot de passe vous pouvez en demander <?= Html::a('un nouveau', ['site/request-password-reset']) ?>.
        </p>
    </div>
</div>