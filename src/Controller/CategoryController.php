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
        $superCategoryId = $request->getAttribute('super_category_id');
        $categoryId = $request->getAttribute('category_id');

        $form = new Form\Category();
        $form->setData(array_merge($request->getParsedBody(), [
            'super_category_id' => $superCategoryId,
            'category_id' => $categoryId
        ]));
        if ($form->isValid()) {
            $affectedRows = $this->categoryService->save($form->getObject());
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
        if (($category = $this->categoryService->get($request->getAttribute('category_id'))) != null) {
            $form = new Form\Category();
            $form->bind($category);
            $form->setData($request->getParsedBody());

            if ($form->isValid()) {
                $this->categoryService->update($form->getObject());
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
        $assemblyId = $request->getAttribute('id');
        $categorySummary = $this->categoryService->fetchByAssembly($assemblyId);

        return new JsonResponse($categorySummary, 206);
    }

    public function setCategoryService(Service\Category $category): self
    {
        $this->categoryService = $category;
        return $this;
    }
}
