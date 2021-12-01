<?php



abstract class AbstractController {
    protected object $databaseObject;
    protected object $smarty;
    abstract public function indexAction();

}