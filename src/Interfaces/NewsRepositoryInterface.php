<?php

namespace Scaupize1123\JustOfficalNews\Interfaces;

interface NewsRepositoryInterface
{
    public function getListPage($filter);

    public function delete($uuid, $lang = null);

    public function create($create);

    public function update($update);

    public function getByUUID($uuid, $lang = null);

    //check lang news exist
    public function checkOneLangNews($uuid, $lang);

    public function checkNews($uuid);
}
