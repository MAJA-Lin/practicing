<?php

namespace scott\board\classes;

interface FrontOutput
{
    public function listMessage($rows);
    public function listPages($count, $limit);
}
?>