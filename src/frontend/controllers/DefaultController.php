<?php

namespace luya\posts\frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use luya\admin\filters\LargeThumbnail;
use luya\posts\models\Article;
use luya\posts\models\Cat;

/**
 * Posts Module Default Controller contains actions to display and render views with predefined data.
 *
 * @author Basil Suter <basil@nadar.io>
 */
class DefaultController extends \luya\web\Controller
{
    /**
     * @var string
     * @since 2.1.0
     */
    const LINK_CANONICAL = 'linkCanonical';

    /**
     * @var string og:type key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_OG_TYPE = 'ogType';
    
    /**
     * @var string twitter:card key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_TWITTER_CARD = 'twitterCard';
    
    /**
     * @var string og:title key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_OG_TITLE = 'ogTitle';
    
    /**
     * @var string twitter:title key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_TWITTER_TITLE = 'twitterTitle';
    
    /**
     * @var string og:url key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_OG_URL = 'ogUrl';
    
    /**
     * @var string twitter:url key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_TWITTER_URL = 'twitterUrl';

    /**
     * @var string description meta key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_DESCRIPTION = 'metaDescription';
    
    /**
     * @var string og:description key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_OG_DESCRIPTION = 'ogDescription';
    
    /**
     * @var string twitter:description key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_TWITTER_DESCRIPTION = 'twitterDescription';
    
    /**
     * @var string keywords meta key which is used for meta registration. Use this constant in order to override the default implementation.
     * @since 2.1.0
     */
    const META_KEYWORDS = 'metaKeywords';

    /**
     * @var string The og:image constant.
     * @since 2.1.0
     */
    const META_OG_IMAGE = 'ogImage';
    
    /**
     * @var string The twitter:image constant.
     * @since 2.1.0
     */
    const META_TWITTER_IMAGE = 'twitterImage';
    
    /**
     * Get Article overview.
     *
     * The index action will return an active data provider object inside the $provider variable:
     *
     * ```php
     * foreach ($provider->models as $item) {
     *     var_dump($item);
     * }
     * ```
     *
     * @return string
     */
    public function actionIndex()
    {
        $provider = new ActiveDataProvider([
            'query' => Article::find()->andWhere(['is_deleted' => false]),
            'sort' => [
                'defaultOrder' => $this->module->articleDefaultOrder,
            ],
            'pagination' => [
                'route' => $this->module->id,
                'params' => ['page' => Yii::$app->request->get('page')],
                'defaultPageSize' => $this->module->articleDefaultPageSize,
            ],
        ]);
        
        return $this->render('index', [
            'model' => Article::className(),
            'provider' => $provider,
        ]);
    }
    
    /**
     * Get all articles for a given categorie ids string seperated by command.
     *
     * @param string $ids The categorie ids: `1,2,3`
     * @return \yii\web\Response|string
     */
    public function actionCategories($ids)
    {
        $ids = explode(",", Html::encode($ids));
        
        if (!is_array($ids)) {
            return $this->goHome();
        }
        
        $provider = new ActiveDataProvider([
            'query' => Article::find()->where(['in', 'cat_id', $ids])->andWhere(['is_deleted' => false]),
            'sort' => [
                'defaultOrder' => $this->module->articleDefaultOrder,
            ],
            'pagination' => [
                'route' => $this->module->id,
                'params' => ['page' => Yii::$app->request->get('page')],
                'defaultPageSize' => $this->module->articleDefaultPageSize,
            ],
        ]);
        
        return $this->render('categories', [
            'provider' => $provider,
        ]);
    }

    /**
     * Get the category Model for a specific ID.
     *
     * The most common way is to use the active data provider object inside the $provider variable:
     *
     * ```php
     * foreach ($provider->getModels() as $cat) {
     *     var_dump($cat);
     * }
     * ```
     *
     * Inside the Cat Object you can then retrieve its articles:
     *
     * ```php
     * foreach ($model->articles as $item) {
     *
     * }
     * ```
     *
     * or customize the where query:
     *
     * ```php
     * foreach ($model->getArticles()->where(['timestamp', time())->all() as $item) {
     *
     * }
     * ```
     *
     * @param integer $categoryId
     * @return \yii\web\Response|string
     */
    public function actionCategory($categoryId)
    {
        $model = Cat::findOne($categoryId);
        
        if (!$model) {
            return $this->goHome();
        }
        
        $provider = new ActiveDataProvider([
            'query' => $model->getArticles(),
            'sort' => [
                'defaultOrder' => $this->module->categoryArticleDefaultOrder,
            ],
            'pagination' => [
                'route' => $this->module->id,
                'params' => ['page' => Yii::$app->request->get('page')],
                'defaultPageSize' => $this->module->categoryArticleDefaultPageSize,
            ],
        ]);
        
        return $this->render('category', [
            'model' => $model,
            'provider' => $provider,
        ]);
    }
    
    /**
     * Detail Action of an article by Id.
     *
     * @param integer $id
     * @param string $title
     * @return \yii\web\Response|string
     */
    public function actionDetail($id, $title)
    {
        $model = Article::findOne(['id' => $id, 'is_deleted' => false]);
        
        if (!$model) {
            return $this->goHome();
        }

        $this->view->title = $model->title;

        $this->view->registerMetaTag(['name' => 'og:type', 'content' => 'website'], self::META_OG_TYPE);
        $this->view->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'], self::META_TWITTER_CARD);
        
        $this->view->registerMetaTag(['name' => 'og:title', 'content' => $this->view->title], self::META_OG_TITLE);
        $this->view->registerMetaTag(['name' => 'twitter:title', 'content' => $this->view->title], self::META_TWITTER_TITLE);
        
        $this->view->registerLinkTag(['rel' => 'canonical', 'href' => Yii::$app->request->absoluteUrl], self::LINK_CANONICAL);
        $this->view->registerMetaTag(['name' => 'og:url', 'content' => Yii::$app->request->absoluteUrl], self::META_OG_URL);
        $this->view->registerMetaTag(['name' => 'twitter:url', 'content' => Yii::$app->request->absoluteUrl], self::META_TWITTER_URL);

        if (! empty($model->teaser_text)) {
            $this->view->registerMetaTag(['name' => 'description', 'content' => $model->teaser_text], self::META_DESCRIPTION);
            $this->view->registerMetaTag(['name' => 'og:description', 'content' => $model->teaser_text], self::META_OG_DESCRIPTION);
            $this->view->registerMetaTag(['name' => 'twitter:description', 'content' => $model->teaser_text], self::META_TWITTER_DESCRIPTION);
        }
        
        // if (! empty($model->keywords)) {
        //     $this->view->registerMetaTag(['name' => 'keywords', 'content' => implode(", ", $currentMenu->keywords)], self::META_KEYWORDS);
        // }

        if (! empty($model->image_id)) {
            $image = Yii::$app->storage->getImage($model->image_id);
            if ($image) {
                $this->view->registerMetaTag(['name' => 'og:image', 'content' => $image->applyFilter(LargeThumbnail::identifier())->sourceAbsolute], self::META_OG_IMAGE);
                $this->view->registerMetaTag(['name' => 'twitter:image', 'content' => $image->applyFilter(LargeThumbnail::identifier())->sourceAbsolute], self::META_TWITTER_IMAGE);
            }
        }
        
        return $this->render('detail', [
            'model' => $model,
        ]);
    }
}
