<?php

namespace VulcanPhp\PhpRouter\Routing\Interfaces;

interface IResource
{
    public function index();
    public function show($id);
    public function store();
    public function create();
    public function edit($id);
    public function update($id);
    public function destroy($id);
}
