<?php

/**
 * Created by PhpStorm.
 * User: jorgeromero
 * Date: 4/16/16
 * Time: 4:40 PM
 */
class ChatContext
{
    private $context;

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }



}