<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Form;
use Althingi\Service;
use Althingi\Injector\ServiceCategoryAwareInterface;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerTrait,
    RestControllerInterface
};

class CategoryController implements
    RestControllerInterface,
    ServiceCategoryAwareInterface
{
    use RestControllerTrait;

    private Service\Category $categoryService;

    /**
     * @output \Althingi\Model\Category
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $category = $this->categoryService->get(
            $request->getAttribute('category_id')
        );

        return $category
            ? new JsonResponse($category, 200)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Category[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $categories = $this->categoryService->fetch(
            $request->getAttribute('super_category_id')
        );

        return new JsonResponse($categories, 206);
    }

    /**
     * @input \Althingi\Form\Category
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\Category([
            ...$request->getParsedBody(),
            'super_category_id' => $request->getAttribute('super_category_id'),
            'category_id' => $request->getAttribute('category_id')
        ]);

        if ($form->isValid()) {
            $affectedRows = $this->categoryService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorFormResponse($form));
    }

    /**
     * @input \Althingi\Form\Category
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (
            ($category = $this->categoryService->get(
                $request->getAttribute('category_id')
            )) != null
        ) {
            $form = new Form\Category([
                ...$category->toArray(),
                ...$request->getParsedBody(),
                'super_category_id' => $request->getAttribute('super_category_id'),
                'category_id' => $request->getAttribute('category_id')
            ]);

            if ($form->isValid()) {
                $this->categoryService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return (new EmptyResponse(404));
    }

    /**
     * @output \Althingi\Model\CategoryAndCount[]
     * @206 Success
     */
    public function assemblySummaryAction(ServerRequest $request): ResponseInterface
    {
        $categorySummary = $this->categoryService->fetchByAssembly(
            $request->getAttribute('id')
        );

        return new JsonResponse($categorySummary, 206);
    }

    public function setCategoryService(Service\Category $category): static
    {
        $this->categoryService = $category;
        return $this;
    }
}
