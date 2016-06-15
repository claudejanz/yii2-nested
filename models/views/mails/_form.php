<?php

use app\extentions\MyTinyMce;
use app\models\Mail;
use claudejanz\toolbox\widgets\ajax\AjaxSubmit;
use kartik\builder\Form;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\redactor\widgets\Redactor;
use yii\web\View;

/**
 * @var View $this
 * @var Mail $model
 * @var ActiveForm $form
 */
?>

<div class="mail-form">

    <?php
    $form = ActiveForm::begin(['type' => ActiveForm::TYPE_VERTICAL]);
    switch (substr(Yii::$app->language, 0, 2)) {
        case 'de':
            $language = 'de';
            break;
        case 'fr':
            $language = 'fr_FR';
            break;
        default:
            $language = 'en';
    }
    echo Form::widget([

        'model'      => $model,
        'form'       => $form,
        'columns'    => 1,
        'attributes' => [

            'subject' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Sujet...', 'maxlength' => 1024]],
//            'content' => ['type'        => Form::INPUT_HTML5,
//                
//            ],
//            'content' => ['type'        => Form::INPUT_WIDGET, 'widgetClass' => Redactor::className()],
            'content' => ['type'        => Form::INPUT_WIDGET, 'widgetClass' => MyTinyMce::className(),
                'options'     => [
                    'options'       => ['rows' => 6],
                    'language'      => $language,
                    'clientOptions' => [
                        'plugins' => [
                            "",
                            "visualblocks code fullscreen",
                            "table contextmenu paste"
//                            "advlist autolink lists link charmap print preview anchor",
//                            "searchreplace visualblocks code fullscreen",
//                            "insertdatetime media table contextmenu paste"
                        ],
//                        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent"
                    ]
                ]
            ],
        ]
    ]);

    if (Yii::$app->request->isAjax) {
        echo AjaxSubmit::widget(['label'   => $model->isNewRecord ? Yii::t('app', 'Send') : Yii::t('app', 'Send'),
            'options' => [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]]);
    } else {
        echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    }
    ActiveForm::end();
    $this->registerJs('$("form input:text, form textarea").first().select();');
    ?>

</div>