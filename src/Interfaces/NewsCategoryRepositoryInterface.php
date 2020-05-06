<?php

namespace Scaupize1123\JustOfficalNews\Interfaces;

interface NewsCategoryRepositoryInterface
{
    public function getList($filter);

    public function getListPage($filter);

    public function delete($id);

    public function create($category);

    public function update($category, $id);

    public function getGroupByLang();

    public function get($data);
}
