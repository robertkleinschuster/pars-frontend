<?php


namespace Pars\Frontend\Cms\Handler;


use Laminas\Diactoros\CallbackStream;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Frontend\Base\Handler\FrontendHandler;
use Pars\Frontend\Cms\Model\BlockModel;
use Pars\Frontend\Cms\Model\ModelFactory;
use Pars\Frontend\Cms\Model\PageModel;
use Pars\Frontend\Cms\Model\PostModel;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CmsHandler
 * @package Pars\Frontend\Cms\Handler
 */
class CmsHandler extends FrontendHandler
{
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->renderPage($request)
            ?? $this->renderPost($request)
            ?? $this->renderBlock($request)
            ?? $this->renderNotFound($request);
    }


    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    protected function renderPage(RequestInterface $request): ?ResponseInterface
    {
        $pageModel = (new ModelFactory())($request, PageModel::class);
        $page = $pageModel->getPage();
        if (
            $page != null
            && (
                $page->empty('ArticleTranslation_Host')
                || trim($page->get('ArticleTranslation_Host')) == trim($request->getUri()->getHost()
                )
            )
        ) {
            if (!$page->empty('CmsPage_ID_Redirect')) {
                $redirect = $pageModel->getPage(null, $page->get('CmsPage_ID_Redirect'));
                if ($redirect != null) {
                    $redirectCode = $redirect->get('ArticleTranslation_Code');
                    $redirectCode = $redirectCode == '/' ? null : $redirectCode;
                    return new RedirectResponse($this->urlHelper->generate('cms', ['code' => $redirectCode]));
                }
            } else {
                if ($page->get('CmsPageType_Code') == 'redirect' && !$page->empty('ArticleTranslation_Path')) {
                    return new RedirectResponse($page->get('ArticleTranslation_Path'));
                }
                $this->assign('form', $pageModel->getForm());
                return new HtmlResponse(new CallbackStream(function () use ($request, $page) {
                    $this->initDefaultVars($request);
                    $this->assign('page', $page);
                    return $this->renderer->render('index::index');
                }));
            }
        }
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    protected function renderBlock(RequestInterface $request): ?ResponseInterface
    {
        $blockModel = (new ModelFactory())($request, BlockModel::class);
        $block = $blockModel->getBlock();
        if ($block != null) {
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'form', $blockModel->getForm());
            return new HtmlResponse(new CallbackStream(function () use ($request, $block) {
                $this->initDefaultVars($request);
                $this->assign('block', $block);
                return $this->renderer->render('index::block');
            }));
        }
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    protected function renderPost(RequestInterface $request): ?ResponseInterface
    {
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
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return HtmlResponse
     */
    protected function renderNotFound(RequestInterface $request)
    {
        $this->initDefaultVars($request);
        return new HtmlResponse($this->renderer->render('error::404'), 404);
    }
}
