<?php
use luya\admin\filters\MediumCrop;

/* @var $this \luya\web\View */
/* @var $model \luya\posts\models\Article */
?>
<h1><?= $model->title; ?></h1>
<?php if ($model->image_id): ?>
    <img src="<?= $model->getImage()->applyFilter(MediumCrop::identifier())->source; ?>" class="pull-right img-responsive img-rounded" />
<?php endif; ?>
<?= $model->text; ?>
<?php if ($model->image_list): ?>
	<div class="row" style="margin-top:15px;">
		<?php foreach ($model->image_list as $imageId): $image = Yii::$app->storage->getImage($imageId['imageId']); ?>
			<div class="col-md-4">
				<img src="<?= $image->source; ?>" class="img-responsive img-rounded" />
			</div>
		<?php endforeach;?>
	</div>
<?php endif; ?>
<p style="margin-top:15px;"><small><i><?= strftime('%A, %e. %B %Y', $model->timestamp_create); ?></i></small>