<?php

namespace App\Service\Article;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlProvider
{
    private $kernel;

    /**
     * YamlProvider constructor.
     *
     * @param $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }


    /**
     * Récupère les articles au format YAML
     * et retourne un Tableau.
     * @return iterable
     */
    public function getArticles(): iterable
    {
        $article =  unserialize(file_get_contents(
           $this->kernel->getCacheDir().'/yaml-article.php'
       ));

        return $article['data'];
    }
}
