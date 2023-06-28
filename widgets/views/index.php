<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $commentModel \yii2mod\comments\models\CommentModel */
/* @var $maxLevel null|integer comments max level */
/* @var $encryptedEntity string */
/* @var $pjaxContainerId string */
/* @var $formId string comment form id */
/* @var $commentDataProvider \yii\data\ArrayDataProvider */
/* @var $listViewConfig array */
/* @var $commentWrapperId string */
?>

<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Chats</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <ul class="nk-block-tools g-3">
                <li>
                    <a href="#" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>New Chat</span></a>
                </li>
                <li class="nk-block-tools-opt">
                    <div class="drodown">
                        <a href="#" class="dropdown-toggle btn btn-white btn-light btn-icon" data-bs-toggle="dropdown"><em class="icon ni ni-setting"></em></a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <ul class="link-list-opt no-bdr">
                                <li><a href="#"><span>Notification Setting</span></a></li>
                                <li><a href="#"><span>Chats Setting</span></a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->


<div class="nk-chat" id="<?php echo $commentWrapperId; ?>">
    <?php Pjax::begin(['enablePushState' => false, 'timeout' => 20000, 'id' => $pjaxContainerId]); ?>
    <div class="nk-chat-body profile-shown">
        <div class="nk-chat-head">
            <ul class="nk-chat-head-info">
                <li class="nk-chat-body-close">
                    <a href="#" class="btn btn-icon btn-trigger nk-chat-hide ms-n1">
                        <em class="icon ni ni-arrow-left"></em></a></li>
                <li class="nk-chat-head-user">
                    <div class="user-card">
                        <div class="user-info">
                            <div class="lead-text">Acreedor</div>
                            <div class="sub-text"><span class="d-none d-sm-inline me-1">
                                        <?php echo Yii::t('yii2mod.comments', 'Comments ({0})', $commentModel->getCommentsCount()); ?>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="nk-chat-head-search">
                <div class="form-group">
                    <div class="form-control-wrap">
                        <div class="form-icon form-icon-left"><em class="icon ni ni-search"></em></div>
                        <input type="text" class="form-control form-round" id="chat-search"
                               placeholder="Search in Conversation"></div>
                </div>
            </div>
        </div>

        <div class="nk-chat-panel data-simplebar">
            <?php echo ListView::widget(ArrayHelper::merge(
                [
                    'dataProvider' => $commentDataProvider,
                    'layout' => "{items}\n{pager}",
                    'itemView' => '_list',
                    'viewParams' => [
                        'maxLevel' => $maxLevel,
                    ],
                    'options' => [
                        //  'tag' => 'div',
                        //  'class' => 'simplebar-content',
                    ],
                    'itemOptions' => [
                        'tag' => false,
                    ],
                ],
                $listViewConfig
            )); ?>
           <!--
            <div class="simplebar-wrapper" style="margin: -20px -28px;">
                <div class="simplebar-height-auto-observer-wrapper">
                    <div class="simplebar-height-auto-observer"></div>
                </div>
                <div class="simplebar-mask">
                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                          <div class="simplebar-content-wrapper" tabindex="0" role="region"
                               aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">

                        <div class="simplebar-content" style="padding: 20px 28px;">
                            <?php /*echo ListView::widget(ArrayHelper::merge(
                                [
                                    'dataProvider' => $commentDataProvider,
                                    'layout' => "{items}\n{pager}",
                                    'itemView' => '_list',
                                    'viewParams' => [
                                        'maxLevel' => $maxLevel,
                                    ],
                                    'options' => [
                                      //  'tag' => 'div',
                                      //  'class' => 'simplebar-content',
                                    ],
                                    'itemOptions' => [
                                        'tag' => false,
                                    ],
                                ],
                                $listViewConfig
                            )); */?>
                        </div>-->
                      <!--  </div>
                    </div>
                </div>-->
                <!--<div class="simplebar-placeholder" style="width: auto; height: 753px;"></div>-->
            </div>
            <!--<div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
            </div>
            <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                <div class="simplebar-scrollbar"
                     style="height: 116px; transform: translate3d(0px, 185px, 0px); display: block;"></div>
            </div>-->

        </div>

        <?php if (!Yii::$app->user->isGuest) : ?>

            <?php echo $this->render('_form', [
                'commentModel' => $commentModel,
                'formId' => $formId,
                'encryptedEntity' => $encryptedEntity,
            ]); ?>

        <?php endif; ?>
    </div>
    <?php Pjax::end(); ?>
</div>

