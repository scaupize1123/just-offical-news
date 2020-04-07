<?php

namespace Scaupize1123\JustOfficalNews\Interfaces;

interface NewsRepositoryInterface
{
    //get one news by UUID
    public function getByUUID($uuid, $lang = null);
    //delete one language news
    public function delete($uuid);
    //delete one news
    //public function deleteAllLanguage($uuid, $lang);
    //create news
    public function create($create);
    //update news
    public function update($update);
}
