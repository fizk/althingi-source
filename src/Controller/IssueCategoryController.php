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
use Althingi\Injector\{
    ServiceIssueCategoryAwareInterface,
    ServiceCategoryAwareInterface
};
use Althingi\Utils\{
    ErrorFormResponse
};
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class IssueCategoryController implements
    RestControllerInterface,
    ServiceIssueCategoryAwareInterface,
    ServiceCategoryAwareInterface
{
    use RestControllerTrait;
    private Service\IssueCategory $issueCategoryService;
    private Service\Category $categoryService;

    /**
     * @output \Althingi\Model\Category
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $categoryId = $request->getAttribute('category_id');

        $category = $this->categoryService
            ->fetchByAssemblyIssueAndCategory($assemblyId, $issueId, $categoryId);

        return $category
            ? new JsonResponse($category)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\Category[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');

        $categories = $this->categoryService
            ->fetchByAssemblyAndIssue($assemblyId, $issueId);

        return new JsonResponse($categories, 206);
    }

    /**
     * @input \Althingi\Form\IssueCategory
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $categoryId = $request->getAttribute('category_id');

        $form = (new Form\IssueCategory([
            ...$request->getParsedBody(),
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'category_id' => $categoryId,
            'category' => 'A'
        ]));

        if ($form->isValid()) {
            $affectedRows = $this->issueCategoryService->save($form->getModel());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\IssueCategory
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('id');
        $issueId = $request->getAttribute('issue_id');
        $categoryId = $request->getAttribute('category_id');

        if (($issueCategory = $this->issueCategoryService->get($assemblyId, $issueId, $categoryId)) != null) {
            $form = new Form\IssueCategory([
                ...(new \Althingi\Hydrator\IssueCategory())->extract($issueCategory),
                ...$request->getParsedBody(),
                'id' => $request->getAttribute('id'),
                'issue_id' => $request->getAttribute('issue_id'),
                'category_id' => $request->getAttribute('category_id'),
            ]);

            if ($form->isValid()) {
                $this->issueCategoryService->update($form->getModel());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setIssueCategoryService(Service\IssueCategory $issueCategory): self
    {
        $this->issueCategoryService = $issueCategory;
        return $this;
    }

    public function setCategoryService(Service\Category $category): self
    {
        $this->categoryService = $category;
        return $this;
    }
}
