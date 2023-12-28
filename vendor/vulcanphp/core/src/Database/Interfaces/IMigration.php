<?php

namespace VulcanPhp\Core\Database\Interfaces;

interface IMigration
{
    public function up(): string;

    public function down(): string;
}
