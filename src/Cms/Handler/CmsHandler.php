<?php


namespace Pars\Frontend\Cms\Handler;


use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\Diactoros\CallbackStream;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Core\Cache\ParsCache;
use Pars\Frontend\Base\Handler\FrontendHandler;
use Pars\Frontend\Cms\Model\ModelFactory;
use Pars\Frontend\Cms\Model\PageModel;
use Pars\Frontend\Cms\Model\ParagraphModel;
use Pars\Frontend\Cms\Model\PostModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CmsHandler extends FrontendHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $cacheID =  md5($request->getUri());
        $pageCache = new ParsCache('page');
        if ($pageCache->has($cacheID) && $this->config[ConfigAggregator::ENABLE_CACHE]) {
            return new HtmlResponse(new CallbackStream(function () use ($pageCache, $cacheID){
                return $pageCache->get($cacheID);
            }));
        }

        $code = $request->getAttribute('code', '/');

        $pageModel = (new ModelFactory())($request, PageModel::class);
        $page = $pageModel->getPage();
        if ($page != null && ($page->empty('ArticleTranslation_Host') || trim($page->get('ArticleTranslation_Host') == $request->getUri()->getHost()))) {
            if (!$page->empty('CmsPage_ID_Redirect')) {
                $redirect = $pageModel->getPage(null, $page->empty('CmsPage_ID'));
                $redirectCode = $redirect->get('ArticleTranslation_Code');
                $redirectCode = $redirectCode == '/' ? null : $redirectCode;
                return new RedirectResponse($this->urlHelper->generate('cms', ['code' => $redirectCode]));
            }
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'form', $pageModel->getForm());
            return new HtmlResponse(new CallbackStream(function () use ($request, $page, $pageCache, $cacheID) {
                $this->initDefaultVars($request);
                $this->assign('page', $page);
                $out = $this->renderer->render('index::index');
                if (in_array(
                    $page->get('CmsPageType_Code'),
                    ['home', 'gallery', 'about', 'faq', 'tiles', 'blog', 'columns']
                )) {
                    $pageCache->set($cacheID, $out, 300);
                }
                return $out;
            }));
        }

        $paragraphModel = (new ModelFactory())($request, ParagraphModel::class);
        $paragraph = $paragraphModel->getParagraph();
        if ($paragraph != null) {
            return new HtmlResponse(new CallbackStream(function () use ($request, $paragraph) {
                $this->initDefaultVars($request);
                $this->assign('paragraph', $paragraph);
                return $this->renderer->render('index::paragraph');
            }));
        }


        $postModel = (new ModelFactory())($request, PostModel::class);
        $post = $postModel->getPost();
        if ($post != null) {
            return new HtmlResponse(new CallbackStream(function () use ($request, $post, $postModel) {
                $this->initDefaultVars($request);
                $this->assign('post', $post);
                $this->assign('similarList', $postModel->getSimilarPosts($post));
                return $this->renderer->render('index::post');
            }));
        }
        $this->initDefaultVars($request);
        return new HtmlResponse($this->renderer->render('error::404'), 404);
    }
}
