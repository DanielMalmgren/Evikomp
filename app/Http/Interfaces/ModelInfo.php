<?php

namespace App\Interfaces;

interface ModelInfo
{
    public function modelName(): String;
    public function modelUrl(): String;
    public function hasUrl(): bool;
}
